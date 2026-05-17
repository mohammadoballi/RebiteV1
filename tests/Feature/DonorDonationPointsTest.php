<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\FoodCategory;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Town;
use App\Models\User;
use Database\Seeders\FoodCategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonorDonationPointsTest extends TestCase
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

    public function test_donor_receives_points_when_creating_donation(): void
    {
        $this->seed(FoodCategorySeeder::class);
        $this->seedRoles();

        Setting::updateOrCreate(['key' => 'donor_points_per_donation'], ['value' => '10', 'label' => 'test', 'type' => 'number']);
        Setting::updateOrCreate(['key' => 'donor_points_per_item'], ['value' => '2', 'label' => 'test', 'type' => 'number']);

        $city = City::create(['name' => 'Amman']);
        $town = Town::create(['city_id' => $city->id, 'name' => 'Jabal']);

        $donor = User::factory()->create([
            'status' => 'approved',
            'city_id' => $city->id,
            'town_id' => $town->id,
            'points' => 0,
        ]);
        $donor->addRole('donor');

        $leaf = FoodCategory::whereNotNull('parent_id')->firstOrFail();

        $response = $this->actingAs($donor)->postJson(route('donor.donations.store'), [
            'food_category_id' => $leaf->id,
            'pickup_address' => '123 Main St',
            'pickup_time' => now()->addDay()->format('Y-m-d\TH:i'),
            'delivery_volunteers_needed' => 1,
            'packaging_volunteers_needed' => 0,
            'items' => [
                ['food_type' => 'Rice', 'quantity' => '5', 'quantity_unit' => 'kg'],
                ['food_type' => 'Bread', 'quantity' => '10', 'quantity_unit' => 'pieces'],
            ],
        ]);

        $response->assertCreated();
        $response->assertJsonPath('points_awarded', 14);
        $response->assertJsonPath('donor_points', 14);

        $this->assertDatabaseHas('donations', [
            'user_id' => $donor->id,
            'city_id' => $city->id,
            'town_id' => $town->id,
            'food_category_id' => $leaf->id,
        ]);

        $donor->refresh();
        $this->assertSame(14, $donor->points);
    }

    public function test_donation_requires_donor_profile_city_and_town(): void
    {
        $this->seed(FoodCategorySeeder::class);
        $this->seedRoles();

        $donor = User::factory()->create([
            'status' => 'approved',
            'city_id' => null,
            'town_id' => null,
        ]);
        $donor->addRole('donor');

        $leaf = FoodCategory::whereNotNull('parent_id')->firstOrFail();

        $response = $this->actingAs($donor)->postJson(route('donor.donations.store'), [
            'food_category_id' => $leaf->id,
            'pickup_address' => '123 Main St',
            'pickup_time' => now()->addDay()->format('Y-m-d\TH:i'),
            'delivery_volunteers_needed' => 1,
            'packaging_volunteers_needed' => 0,
            'items' => [
                ['food_type' => 'Rice', 'quantity' => '5', 'quantity_unit' => 'kg'],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('profile');
    }
}
