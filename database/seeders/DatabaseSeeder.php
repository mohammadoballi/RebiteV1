<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /** All seeded users log in with this password */
    private const SEED_PASSWORD = 'Pass@123';

    public function run(): void
    {
        $hash = Hash::make(self::SEED_PASSWORD);

        if (class_exists(SettingsSeeder::class)) {
            $this->call(SettingsSeeder::class);
        }

        $this->call(CityTownSeeder::class);

        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['display_name' => 'Administrator', 'description' => 'System Administrator']
        );

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@rebite.com'],
            [
                'name' => 'Admin',
                'password' => $hash,
                'phone' => '0790000001',
                'status' => 'approved',
                'locale' => 'en',
            ]
        );

        if (!$adminUser->roles->contains('name', 'admin')) {
            $adminUser->addRole($adminRole);
        }

        $this->command->info('Seeded: settings (if any), cities/towns, admin user. Login password: ' . self::SEED_PASSWORD);
    }
}
