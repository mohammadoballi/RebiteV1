<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CityTownSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Amman' => [
                'Abdoun', 'Sweifieh', 'Jabal Amman', 'Rainbow Street', 'Dabouq',
                'Khalda', 'Al-Weibdeh', 'Shmeisani', 'Marj Al Hamam', 'Gardens Street',
                'Airport Road', 'Um Uthaina', 'Marka', 'Sahab', 'Tlaa Al Ali',
            ],
            'Irbid' => [
                'Irbid', 'University Street Irbid', 'Al-Husn',
            ],
            'Zarqa' => [
                'Zarqa', 'Russeifa', 'Hashemiyeh',
            ],
            'Aqaba' => [
                'Aqaba', 'Ayla Oasis', 'South Beach Aqaba',
            ],
            'Madaba' => [],
            'Salt' => [],
            'Karak' => [],
            'Mafraq' => [],
            'Ajloun' => [],
        ];

        foreach ($data as $cityName => $towns) {
            $city = City::create(['name' => $cityName]);

            foreach ($towns as $townName) {
                $city->towns()->create(['name' => $townName]);
            }
        }

        $this->command->info('Seeded: ' . count($data) . ' cities with towns.');
    }
}
