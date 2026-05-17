<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\FoodCategory;
use App\Models\Role;
use App\Models\Town;
use App\Models\User;
use App\Support\DonationItemAggregator;
use Database\Seeders\FoodCategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class VolunteerDonationWorkflowTest extends TestCase
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

    public function test_multi_item_donation_stores_all_quantities_and_items(): void
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

        $leaf = FoodCategory::whereNotNull('parent_id')->firstOrFail();

        $items = [
            ['food_type' => 'Rice', 'quantity' => '5', 'quantity_unit' => 'kg'],
            ['food_type' => 'Bread', 'quantity' => '10', 'quantity_unit' => 'pieces'],
        ];

        $response = $this->actingAs($donor)->postJson(route('donor.donations.store'), [
            'food_category_id' => $leaf->id,
            'pickup_address' => '123 Main St',
            'pickup_time' => now()->addDay()->format('Y-m-d\TH:i'),
            'delivery_volunteers_needed' => 1,
            'packaging_volunteers_needed' => 0,
            'items' => $items,
        ]);

        $response->assertCreated();

        $donationId = $response->json('donation.id');

        $this->assertDatabaseCount('donation_items', 2);
        $this->assertDatabaseHas('donation_items', [
            'donation_id' => $donationId,
            'food_type' => 'Rice',
            'quantity' => '5',
            'quantity_unit' => 'kg',
        ]);
        $this->assertDatabaseHas('donation_items', [
            'donation_id' => $donationId,
            'food_type' => 'Bread',
            'quantity' => '10',
            'quantity_unit' => 'pieces',
        ]);

        $aggregated = DonationItemAggregator::aggregateForDonation($items);
        $this->assertDatabaseHas('donations', [
            'id' => $donationId,
            'food_type' => $aggregated['food_type'],
            'quantity' => $aggregated['quantity'],
            'quantity_unit' => 'mixed',
        ]);
    }

    public function test_admin_donation_show_includes_all_items_with_quantities(): void
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

        $admin = User::factory()->create(['status' => 'approved']);
        $admin->addRole('admin');

        $leaf = FoodCategory::whereNotNull('parent_id')->firstOrFail();

        $create = $this->actingAs($donor)->postJson(route('donor.donations.store'), [
            'food_category_id' => $leaf->id,
            'pickup_address' => '456 Oak Ave',
            'pickup_time' => now()->addDay()->format('Y-m-d\TH:i'),
            'delivery_volunteers_needed' => 1,
            'packaging_volunteers_needed' => 0,
            'items' => [
                ['food_type' => 'Soup', 'quantity' => '3', 'quantity_unit' => 'boxes'],
                ['food_type' => 'Pasta', 'quantity' => '7', 'quantity_unit' => 'kg'],
            ],
        ]);

        $donationId = $create->json('donation.id');

        $response = $this->actingAs($admin)->getJson(route('admin.donations.show', $donationId));

        $response->assertOk();
        $response->assertJsonCount(2, 'items');
        $response->assertJsonPath('items.0.food_type', 'Soup');
        $response->assertJsonPath('items.0.quantity', '3');
        $response->assertJsonPath('items.1.food_type', 'Pasta');
        $response->assertJsonPath('items.1.quantity', '7');
        $response->assertJsonPath('pickup_address', '456 Oak Ave');
        $response->assertJsonPath('food_category_id', $leaf->id);
    }

    public function test_volunteer_registration_saves_packaging_role_type(): void
    {
        $this->seedRoles();

        $response = $this->post(route('register'), [
            'name' => 'Pack Volunteer',
            'email' => 'packer@example.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
            'phone' => '0790000000',
            'role' => 'volunteer',
            'role_type' => 'packaging',
            'safety_guidelines' => '1',
            'id_file' => UploadedFile::fake()->create('id.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect(route('login'));

        $this->assertDatabaseHas('users', [
            'email' => 'packer@example.com',
            'role_type' => 'packaging',
        ]);
    }

    public function test_volunteer_can_choose_assignment_type_when_self_assigning(): void
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

        $packager = User::factory()->create([
            'status' => 'approved',
            'city_id' => $city->id,
            'town_id' => $town->id,
            'role_type' => 'packaging',
        ]);
        $packager->addRole('volunteer');

        $leaf = FoodCategory::whereNotNull('parent_id')->firstOrFail();

        $deliveryOnly = $this->actingAs($donor)->postJson(route('donor.donations.store'), [
            'food_category_id' => $leaf->id,
            'pickup_address' => '123 Main St',
            'pickup_time' => now()->addDay()->format('Y-m-d\TH:i'),
            'delivery_volunteers_needed' => 1,
            'packaging_volunteers_needed' => 0,
            'items' => [['food_type' => 'Rice', 'quantity' => '5', 'quantity_unit' => 'kg']],
        ])->json('donation.id');

        $this->actingAs($packager)
            ->postJson(route('volunteer.donations.assign', $deliveryOnly), [
                'assignment_type' => 'packaging',
            ])
            ->assertStatus(422);

        $assignDelivery = $this->actingAs($packager)
            ->postJson(route('volunteer.donations.assign', $deliveryOnly), [
                'assignment_type' => 'delivery',
            ]);

        $assignDelivery->assertCreated();
        $assignDelivery->assertJsonPath('assignment_type', 'delivery');

        $bothTypes = $this->actingAs($donor)->postJson(route('donor.donations.store'), [
            'food_category_id' => $leaf->id,
            'pickup_address' => '456 Oak Ave',
            'pickup_time' => now()->addDay()->format('Y-m-d\TH:i'),
            'delivery_volunteers_needed' => 0,
            'packaging_volunteers_needed' => 1,
            'items' => [['food_type' => 'Soup', 'quantity' => '3', 'quantity_unit' => 'boxes']],
        ])->json('donation.id');

        $assignPackaging = $this->actingAs($packager)
            ->postJson(route('volunteer.donations.assign', $bothTypes), [
                'assignment_type' => 'packaging',
            ]);

        $assignPackaging->assertCreated();
        $assignPackaging->assertJsonPath('assignment_type', 'packaging');

        $this->assertDatabaseHas('donation_assignments', [
            'donation_id' => $deliveryOnly,
            'volunteer_id' => $packager->id,
            'assignment_type' => 'delivery',
        ]);
        $this->assertDatabaseHas('donation_assignments', [
            'donation_id' => $bothTypes,
            'volunteer_id' => $packager->id,
            'assignment_type' => 'packaging',
        ]);
    }

    public function test_admin_users_datatable_shows_volunteer_task_badge(): void
    {
        $this->seedRoles();

        $admin = User::factory()->create(['status' => 'approved']);
        $admin->addRole('admin');

        $volunteer = User::factory()->create([
            'status' => 'approved',
            'role_type' => 'packaging',
        ]);
        $volunteer->addRole('volunteer');

        $response = $this->actingAs($admin)->getJson(route('admin.users.datatable'));

        $response->assertOk();
        $rows = collect($response->json('data'));
        $row = $rows->firstWhere('id', $volunteer->id);

        $this->assertNotNull($row);
        $this->assertStringContainsString('packaging', strtolower($row['volunteer_task'] ?? ''));
    }
}
