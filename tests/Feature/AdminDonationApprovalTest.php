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

class AdminDonationApprovalTest extends TestCase
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

    public function test_unapproved_donation_hidden_from_charity_until_admin_approves(): void
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

        $admin = User::factory()->create(['status' => 'approved']);
        $admin->addRole('admin');

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

        $ids = $this->actingAs($charity)->get(route('charity.donations.index'))->viewData('donations')->getCollection()->pluck('id');
        $this->assertFalse($ids->contains($donation->id));

        $this->actingAs($admin)->postJson(route('admin.donations.approve', $donation->id))->assertOk();

        $ids = $this->actingAs($charity)->get(route('charity.donations.index'))->viewData('donations')->getCollection()->pluck('id');
        $this->assertTrue($ids->contains($donation->id));
    }

    public function test_admin_status_accepted_publishes_without_hiding_from_charity(): void
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

        $admin = User::factory()->create(['status' => 'approved']);
        $admin->addRole('admin');

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

        $this->actingAs($admin)
            ->putJson(route('admin.donations.update-status', $donation->id), ['status' => 'accepted'])
            ->assertOk();

        $donation->refresh();
        $this->assertSame(Donation::STATUS_PENDING, $donation->status);
        $this->assertNotNull($donation->admin_approved_at);

        $ids = $this->actingAs($charity)->get(route('charity.donations.index'))->viewData('donations')->getCollection()->pluck('id');
        $this->assertTrue($ids->contains($donation->id));
    }
}
