<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Donation;
use App\Models\DonationRequest;
use App\Models\FoodCategory;
use App\Models\Rating;
use App\Models\Role;
use App\Models\Town;
use App\Models\User;
use Database\Seeders\FoodCategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CharityDonationAcceptanceAndRatingTest extends TestCase
{
    use RefreshDatabase;

    private function seedRoles(): void
    {
        foreach (['donor', 'charity', 'volunteer', 'admin'] as $r) {
            Role::firstOrCreate(
                ['name' => $r],
                ['display_name' => ucfirst($r), 'description' => $r]
            );
        }
    }

    private function createDonation(User $donor, City $city, Town $town): Donation
    {
        $leaf = FoodCategory::whereNotNull('parent_id')->firstOrFail();

        return Donation::create([
            'user_id' => $donor->id,
            'city_id' => $city->id,
            'town_id' => $town->id,
            'food_category_id' => $leaf->id,
            'food_type' => 'Rice',
            'quantity' => '10',
            'quantity_unit' => 'kg',
            'pickup_address' => '123 St',
            'pickup_time' => now()->addDay(),
            'status' => Donation::STATUS_PENDING,
            'admin_approved_at' => now(),
            'delivery_volunteers_needed' => 1,
            'packaging_volunteers_needed' => 0,
            'volunteers_needed' => 1,
            'volunteers_count' => 0,
        ]);
    }

    public function test_second_charity_cannot_accept_already_claimed_donation(): void
    {
        $this->seed(FoodCategorySeeder::class);
        $this->seedRoles();

        $city = City::create(['name' => 'Amman']);
        $town = Town::create(['city_id' => $city->id, 'name' => 'Jabal']);

        $donor = User::factory()->create(['status' => 'approved', 'city_id' => $city->id, 'town_id' => $town->id]);
        $donor->addRole('donor');

        $charityA = User::factory()->create([
            'status' => 'approved',
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addMonth(),
        ]);
        $charityA->addRole('charity');

        $charityB = User::factory()->create([
            'status' => 'approved',
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addMonth(),
        ]);
        $charityB->addRole('charity');

        $donation = $this->createDonation($donor, $city, $town);

        $this->actingAs($charityA)
            ->postJson(route('charity.donations.accept', $donation->id))
            ->assertCreated();

        $this->actingAs($charityB)
            ->postJson(route('charity.donations.accept', $donation->id))
            ->assertStatus(409);

        $idsB = $this->actingAs($charityB)->get(route('charity.donations.index'))->viewData('donations')->getCollection()->pluck('id');
        $this->assertFalse($idsB->contains($donation->id));

        $donation->refresh();
        $this->assertSame($charityA->id, $donation->accepted_charity_id);
    }

    public function test_charity_cannot_submit_duplicate_rating_for_same_donation(): void
    {
        $this->seed(FoodCategorySeeder::class);
        $this->seedRoles();

        $city = City::create(['name' => 'Amman']);
        $town = Town::create(['city_id' => $city->id, 'name' => 'Jabal']);

        $donor = User::factory()->create(['status' => 'approved', 'city_id' => $city->id, 'town_id' => $town->id]);
        $donor->addRole('donor');

        $charity = User::factory()->create([
            'status' => 'approved',
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addMonth(),
        ]);
        $charity->addRole('charity');

        $donation = $this->createDonation($donor, $city, $town);

        $this->actingAs($charity)->postJson(route('charity.donations.accept', $donation->id))->assertCreated();

        $payload = [
            'donation_id' => $donation->id,
            'rateable_id' => $donor->id,
            'rateable_type' => 'user',
            'rating' => 5,
            'comment' => 'Great donor',
        ];

        $this->actingAs($charity)->postJson(route('ratings.store'), $payload)->assertCreated();

        $this->actingAs($charity)->postJson(route('ratings.store'), $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors('rating');

        $this->assertSame(1, Rating::where('rater_id', $charity->id)->where('donation_id', $donation->id)->count());
    }

    public function test_charity_show_includes_rated_user_ids(): void
    {
        $this->seed(FoodCategorySeeder::class);
        $this->seedRoles();

        $city = City::create(['name' => 'Amman']);
        $town = Town::create(['city_id' => $city->id, 'name' => 'Jabal']);

        $donor = User::factory()->create(['status' => 'approved', 'city_id' => $city->id, 'town_id' => $town->id]);
        $donor->addRole('donor');

        $charity = User::factory()->create([
            'status' => 'approved',
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addMonth(),
        ]);
        $charity->addRole('charity');

        $donation = $this->createDonation($donor, $city, $town);
        $this->actingAs($charity)->postJson(route('charity.donations.accept', $donation->id))->assertCreated();

        Rating::create([
            'rater_id' => $charity->id,
            'donation_id' => $donation->id,
            'rateable_id' => $donor->id,
            'rateable_type' => User::class,
            'rating' => 4,
        ]);

        $response = $this->actingAs($charity)->getJson(route('charity.donations.show', $donation->id));
        $response->assertOk();
        $response->assertJsonFragment(['rated_user_ids' => [$donor->id]]);
    }
}
