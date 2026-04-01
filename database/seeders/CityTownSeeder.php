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
                'Jubeiha', 'Tabarbour', 'Al-Jandaweel', 'Basman', 'Naour',
            ],
            'Irbid' => [
                'Irbid City Center', 'University District', 'Al-Husn', 'Ar Ramtha', 'Bani Kinana',
            ],
            'Zarqa' => [
                'Zarqa', 'Russeifa', 'Hashemiyeh', 'Azraq', 'Jabal Tareq',
            ],
            'Aqaba' => [
                'Aqaba City', 'Ayla Oasis', 'South Beach', 'Tala Bay', 'Al-Rawdah',
            ],
            'Madaba' => [
                'Madaba City', 'Mount Nebo', 'Dhiban', 'Hanina',
            ],
            'Salt' => [
                'Salt City', 'Al-Salt Old Town', 'Fuheis', 'Mahis',
            ],
            'Karak' => [
                'Karak City', 'Al-Qasr', 'Mazra\'a', 'Al-Mazar',
            ],
            'Mafraq' => [
                'Mafraq City', 'Ruwaished', 'Badiah',
            ],
            'Ajloun' => [
                'Ajloun City', 'Kufranjah', 'Anjara', 'Sakhra',
            ],
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
