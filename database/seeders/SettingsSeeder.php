<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['key' => 'max_pending_requests_per_charity', 'value' => '3', 'label' => 'Max pending donation requests per charity', 'type' => 'number'],
            ['key' => 'volunteer_signup_points', 'value' => '5', 'label' => 'Points for volunteer when assigned to donation', 'type' => 'number'],
            ['key' => 'volunteer_pickup_points', 'value' => '5', 'label' => 'Points for volunteer on pickup', 'type' => 'number'],
            ['key' => 'volunteer_delivery_points', 'value' => '25', 'label' => 'Points for volunteer on delivery', 'type' => 'number'],
            ['key' => 'volunteer_rating_points', 'value' => '3', 'label' => 'Points for volunteer when rated', 'type' => 'number'],
            ['key' => 'donor_points_per_donation', 'value' => '10', 'label' => 'Points for donor per donation', 'type' => 'number'],
            ['key' => 'donor_points_per_item', 'value' => '2', 'label' => 'Points for donor per donation item', 'type' => 'number'],
            ['key' => 'charity_subscription_price', 'value' => '2', 'label' => 'Monthly subscription price for charities (JD)', 'type' => 'number'],
        ];

        foreach ($rows as $row) {
            Setting::updateOrCreate(
                ['key' => $row['key']],
                [
                    'value' => $row['value'],
                    'label' => $row['label'],
                    'type' => $row['type'],
                ]
            );
        }

        $this->command->info('Seeded: ' . count($rows) . ' application settings.');
    }
}
