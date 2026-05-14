<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Donation;
use App\Models\FoodCategory;
use App\Models\Role;
use App\Models\Town;
use App\Models\User;
use Database\Seeders\FoodCategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonationCharityVisibilityTest extends TestCase
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

    public function test_pending_donation_is_visible_on_charity_marketplace(): void
    {
        $this->seed(FoodCategorySeeder::class);
        $this->seedRoles();

        $city = City::create(['name' => 'Amman']);
        $town = Town::create(['city_id' => $city->id, 'name' => 'Jabal']);

        $donor = User::factory()->create([
            'status' => 'approved',
            'city_id' => $city->id,
            'town_id' => $town->id,
        ]);
        $donor->addRole('donor');

        $charity = User::factory()->create([
            'status' => 'approved',
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addMonth(),
        ]);
        $charity->addRole('charity');

        $leaf = FoodCategory::whereNotNull('parent_id')->firstOrFail();

        $donation = Donation::create([
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
            'delivery_volunteers_needed' => 1,
            'packaging_volunteers_needed' => 0,
            'volunteers_needed' => 1,
            'volunteers_count' => 0,
        ]);

        $response = $this->actingAs($charity)->get(route('charity.donations.index'));
        $response->assertOk();
        $donations = $response->viewData('donations');
        $this->assertTrue($donations->getCollection()->pluck('id')->contains($donation->id));
    }

    public function test_charity_accept_marks_donation_accepted_and_hides_from_marketplace(): void
    {
        $this->seed(FoodCategorySeeder::class);
        $this->seedRoles();

        $city = City::create(['name' => 'Amman']);
        $town = Town::create(['city_id' => $city->id, 'name' => 'Jabal']);

        $donor = User::factory()->create([
            'status' => 'approved',
            'city_id' => $city->id,
            'town_id' => $town->id,
        ]);
        $donor->addRole('donor');

        $charity = User::factory()->create([
            'status' => 'approved',
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addMonth(),
        ]);
        $charity->addRole('charity');

        $leaf = FoodCategory::whereNotNull('parent_id')->firstOrFail();

        $donation = Donation::create([
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
            'delivery_volunteers_needed' => 1,
            'packaging_volunteers_needed' => 0,
            'volunteers_needed' => 1,
            'volunteers_count' => 0,
        ]);

        $this->actingAs($charity)
            ->postJson(route('charity.donations.accept', $donation->id), ['message' => 'We can collect today.'])
            ->assertCreated();

        $donation->refresh();
        $this->assertSame(Donation::STATUS_ACCEPTED, $donation->status);

        $ids = $this->actingAs($charity)->get(route('charity.donations.index'))->viewData('donations')->getCollection()->pluck('id');
        $this->assertFalse($ids->contains($donation->id));
    }

    public function test_volunteer_sees_accepted_donation_when_charity_claimed(): void
    {
        $this->seed(FoodCategorySeeder::class);
        $this->seedRoles();

        $city = City::create(['name' => 'Amman']);
        $town = Town::create(['city_id' => $city->id, 'name' => 'Jabal']);

        $donor = User::factory()->create([
            'status' => 'approved',
            'city_id' => $city->id,
            'town_id' => $town->id,
        ]);
        $donor->addRole('donor');

        $charity = User::factory()->create([
            'status' => 'approved',
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addMonth(),
        ]);
        $charity->addRole('charity');

        $volunteer = User::factory()->create([
            'status' => 'approved',
            'city_id' => $city->id,
            'town_id' => $town->id,
            'role_type' => 'delivery',
        ]);
        $volunteer->addRole('volunteer');

        $leaf = FoodCategory::whereNotNull('parent_id')->firstOrFail();

        $donation = Donation::create([
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
            'delivery_volunteers_needed' => 1,
            'packaging_volunteers_needed' => 0,
            'volunteers_needed' => 1,
            'volunteers_count' => 0,
        ]);

        $this->actingAs($charity)->postJson(route('charity.donations.accept', $donation->id))->assertCreated();

        $response = $this->actingAs($volunteer)->get(route('volunteer.donations.index'));
        $response->assertOk();
        $donations = $response->viewData('donations');
        $this->assertTrue($donations->getCollection()->pluck('id')->contains($donation->id));
    }
}
