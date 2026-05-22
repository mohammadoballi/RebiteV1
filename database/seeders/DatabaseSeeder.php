<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\City;
use App\Models\Town;
use App\Models\Donation;
use App\Models\DonationItem;
use App\Models\DonationRequest;
use App\Models\DonationAssignment;
use App\Models\FoodCategory;
use App\Models\Rating;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /** All seeded users log in with this password */
    private const SEED_PASSWORD = 'Pass@123';

    public function run(): void
    {
        $hash = Hash::make(self::SEED_PASSWORD);

        // ── Roles ──
        $admin = Role::create(['name' => 'admin', 'display_name' => 'Administrator', 'description' => 'System Administrator']);
        $donor = Role::create(['name' => 'donor', 'display_name' => 'Donor', 'description' => 'Food Donor']);
        $charity = Role::create(['name' => 'charity', 'display_name' => 'Charity', 'description' => 'Charity Organization']);
        $volunteer = Role::create(['name' => 'volunteer', 'display_name' => 'Volunteer', 'description' => 'Volunteer']);

        // ── Permissions ──
        $permissions = [
            'users-create', 'users-read', 'users-update', 'users-delete', 'users-approve',
            'donations-create', 'donations-read', 'donations-update', 'donations-delete',
            'donations-accept',
            'assignments-read', 'assignments-update',
            'charity-management-read',
            'food-categories-manage',
        ];
        foreach ($permissions as $perm) {
            Permission::create(['name' => $perm, 'display_name' => ucwords(str_replace('-', ' ', $perm))]);
        }
        $admin->givePermissions(Permission::all());
        $donor->givePermissions(['donations-create', 'donations-read', 'donations-update', 'donations-delete']);
        $charity->givePermissions(['donations-read', 'donations-accept']);
        $volunteer->givePermissions(['assignments-read', 'assignments-update']);

        $this->call(SettingsSeeder::class);

        // ── Admin ──
        $adminUser = User::create([
            'name' => 'Admin', 'email' => 'admin@rebite.com',
            'password' => $hash, 'phone' => '0790000001',
            'status' => 'approved', 'locale' => 'en',
        ]);
        $adminUser->addRole('admin');

        // ── Seed Cities & Towns (Jordan) ──
        $this->call(CityTownSeeder::class);
        $this->call(FoodCategorySeeder::class);
        $allCities = City::with('towns')->get();
        $leafFoodCategories = FoodCategory::whereNotNull('parent_id')->get();

        $getRandomCityTown = function () use ($allCities) {
            $city = $allCities->random();
            $town = $city->towns->isNotEmpty() ? $city->towns->random() : null;
            return [$city->id, $town?->id];
        };

        // ── Donors (15) ──
        $donorNames = [
            'Hashem Restaurant', 'Jabri Restaurant', 'Fakhr El-Din', 'Shawarmer Amman', 'Pizza Hut Amman',
            'Dominos Irbid', 'McDonald\'s Jordan', 'Burger King Amman', 'Popeyes Jordan', 'KFC Zarqa',
            'Cozmo Markets', 'Safeway Hypermarket', 'Carrefour Jordan', 'Miles Supermarket', 'Sameh Mall',
        ];
        $donors = [];
        foreach ($donorNames as $i => $name) {
            [$cityId, $townId] = $getRandomCityTown();
            $cityRow = $allCities->firstWhere('id', $cityId);
            $cityName = $cityRow->name;
            $townLabel = $townId ? $cityRow->towns->firstWhere('id', $townId)?->name : $cityName;
            $user = User::create([
                'name' => $name, 'email' => 'donor' . ($i + 1) . '@rebite.com',
                'password' => $hash, 'phone' => sprintf('0791%06d', $i + 1),
                'status' => 'approved', 'city' => $cityName,
                'city_id' => $cityId, 'town_id' => $townId,
                'address' => 'Building ' . rand(1, 120) . ', ' . ($townLabel ?? $cityName),
                'health_certificate' => 'certificates/sample.pdf',
                'commercial_registration_certificate' => 'certificates/sample.pdf',
                'locale' => 'en',
            ]);
            $user->addRole('donor');
            $donors[] = $user;
        }

        // 3 pending donors
        for ($i = 0; $i < 3; $i++) {
            [$cityId, $townId] = $getRandomCityTown();
            $cityName = $allCities->firstWhere('id', $cityId)->name;
            $user = User::create([
                'name' => 'Pending Donor ' . ($i + 1), 'email' => 'pendingdonor' . ($i + 1) . '@rebite.com',
                'password' => $hash, 'phone' => sprintf('0792%06d', $i + 1),
                'status' => 'pending', 'city' => $cityName,
                'city_id' => $cityId, 'town_id' => $townId,
                'health_certificate' => 'certificates/sample.pdf',
                'commercial_registration_certificate' => 'certificates/sample.pdf',
                'locale' => 'en',
            ]);
            $user->addRole('donor');
        }

        // ── Charities (10) ──
        $charityNames = [
            'Food Bank Jordan', 'Ehsan Foundation', 'Tkiyet Um Ali', 'Jordan Red Crescent',
            'Takaful Charity', 'Al-Bir Society Amman', 'Orphans Care Society', 'Ensan Charity',
            'Al-Khair Foundation', 'Noor Al Hussein Foundation',
        ];
        $charities = [];
        foreach ($charityNames as $i => $name) {
            [$cityId, $townId] = $getRandomCityTown();
            $cityName = $allCities->firstWhere('id', $cityId)->name;
            $user = User::create([
                'name' => $name, 'email' => 'charity' . ($i + 1) . '@rebite.com',
                'password' => $hash, 'phone' => sprintf('0793%06d', $i + 1),
                'status' => 'approved', 'city' => $cityName,
                'city_id' => $cityId, 'town_id' => $townId,
                'organization_name' => $name,
                'organization_license' => 'certificates/sample.pdf',
                'locale' => 'en',
            ]);
            $user->addRole('charity');
            $charities[] = $user;
        }

        // 2 pending charities (awaiting admin approval)
        for ($i = 0; $i < 2; $i++) {
            [$cityId, $townId] = $getRandomCityTown();
            $cityName = $allCities->firstWhere('id', $cityId)->name;
            $user = User::create([
                'name' => 'Pending Charity ' . ($i + 1), 'email' => 'pendingcharity' . ($i + 1) . '@rebite.com',
                'password' => $hash, 'phone' => sprintf('0796%06d', $i + 1),
                'status' => 'pending', 'city' => $cityName,
                'city_id' => $cityId, 'town_id' => $townId,
                'organization_name' => 'Pending Charity Organization ' . ($i + 1),
                'organization_license' => 'certificates/sample.pdf',
                'locale' => 'en',
            ]);
            $user->addRole('charity');
        }

        // ── Volunteers (20) ──
        $volunteerNames = [
            'Ahmed Ali', 'Mohammed Hassan', 'Khalid Omar', 'Faisal Saeed', 'Sultan Nasser',
            'Turki Abdulaziz', 'Nayef Ibrahim', 'Saad Abdullah', 'Rayan Ahmad', 'Majed Fahad',
            'Omar Tariq', 'Youssef Waleed', 'Hamad Saleh', 'Bader Mansour', 'Nawaf Saud',
            'Rami Khaled', 'Hani Adel', 'Ziad Rashid', 'Sami Fawaz', 'Tamer Husain',
        ];
        $volunteers = [];
        foreach ($volunteerNames as $i => $name) {
            $type = $i < 14 ? 'delivery' : 'packaging';
            [$cityId, $townId] = $getRandomCityTown();
            $cityName = $allCities->firstWhere('id', $cityId)->name;
            $user = User::create([
                'name' => $name, 'email' => 'vol' . ($i + 1) . '@rebite.com',
                'password' => $hash, 'phone' => sprintf('0794%06d', $i + 1),
                'status' => 'approved', 'role_type' => $type,
                'city' => $cityName, 'city_id' => $cityId, 'town_id' => $townId,
                'locale' => 'en',
            ]);
            $user->addRole('volunteer');
            $volunteers[] = $user;
        }

        // 2 pending volunteers
        for ($i = 0; $i < 2; $i++) {
            [$cityId, $townId] = $getRandomCityTown();
            $cityName = $allCities->firstWhere('id', $cityId)->name;
            $user = User::create([
                'name' => 'Pending Volunteer ' . ($i + 1), 'email' => 'pendingvol' . ($i + 1) . '@rebite.com',
                'password' => $hash, 'phone' => sprintf('0795%06d', $i + 1),
                'status' => 'pending', 'role_type' => 'delivery',
                'city' => $cityName, 'city_id' => $cityId, 'town_id' => $townId,
                'locale' => 'en',
            ]);
            $user->addRole('volunteer');
        }

        // ── Food types pool ──
        $foodTypes = [
            'Rice', 'Chicken', 'Bread', 'Salad', 'Soup', 'Pasta', 'Sandwiches', 'Fruits',
            'Vegetables', 'Juice', 'Milk', 'Cake', 'Pizza', 'Biryani', 'Kabsa', 'Shawarma',
            'Falafel', 'Hummus', 'Dates', 'Yogurt', 'Cheese', 'Eggs', 'Fish', 'Lamb',
            'Mandi', 'Samosa', 'Spring Rolls', 'Croissant', 'Donuts', 'Cookies',
        ];
        $units = ['kg', 'pieces', 'boxes', 'bags', 'plates'];
        $addresses = [
            'King Hussein Street', 'Wasfi Al-Tal Street (Garden Street)', 'Mecca Street',
            'Rainbow Street, Jabal Amman', 'Queen Rania Street', 'Prince Mohammad Street',
            'Abdali Boulevard', 'Al-Weibdeh', 'Shmeisani, Arjan Complex',
            'Sports City area', 'Marka / Al-Hussein Medical City', 'Jubaiha',
        ];

        // ── Donations (60) ──
        $donations = [];
        $statuses = ['pending', 'pending', 'pending', 'in_progress', 'in_progress', 'completed', 'completed', 'completed'];

        for ($i = 0; $i < 60; $i++) {
            $donorUser = $donors[array_rand($donors)];
            $status = $statuses[array_rand($statuses)];
            $deliveryNeeded = rand(1, 4);
            $packagingNeeded = rand(0, 2);
            if ($deliveryNeeded + $packagingNeeded === 0) {
                $deliveryNeeded = 1;
            }
            $volunteersNeeded = $deliveryNeeded + $packagingNeeded;
            $volunteersCount = 0;
            if (in_array($status, ['in_progress', 'completed'])) {
                $volunteersCount = min(rand(1, $volunteersNeeded), $volunteersNeeded);
            }
            if ($status === 'completed') {
                $volunteersCount = $volunteersNeeded;
            }

            $pickupTime = Carbon::now()->addHours(rand(-48, 168));
            $numItems = rand(1, 5);
            $selectedFoods = array_rand(array_flip($foodTypes), $numItems);
            if (!is_array($selectedFoods)) {
                $selectedFoods = [$selectedFoods];
            }

            // Coordinates within Jordan (approximate bounding box)
            $latitude = round(29.19 + mt_rand(0, 4210) / 1000, 6);
            $longitude = round(34.96 + mt_rand(0, 4330) / 1000, 6);

            $donation = Donation::create([
                'user_id' => $donorUser->id,
                'city_id' => $donorUser->city_id,
                'town_id' => $donorUser->town_id,
                'food_type' => implode(', ', $selectedFoods),
                'description' => rand(0, 1) ? 'Surplus food from today\'s preparation. Good quality and fresh.' : null,
                'quantity' => rand(1, 50),
                'quantity_unit' => $units[array_rand($units)],
                'food_category_id' => $leafFoodCategories->isNotEmpty() ? $leafFoodCategories->random()->id : null,
                'pickup_address' => $addresses[array_rand($addresses)] . ', ' . ($donorUser->city ?? 'Amman'),
                'longitude' => $longitude,
                'pickup_time' => $pickupTime,
                'expiry_time' => rand(0, 1) ? $pickupTime->copy()->addHours(rand(4, 48)) : null,
                'status' => $status,
                'notes' => rand(0, 1) ? 'Please bring containers. Call before arriving.' : null,
                'volunteers_needed' => $volunteersNeeded,
                'delivery_volunteers_needed' => $deliveryNeeded,
                'packaging_volunteers_needed' => $packagingNeeded,
                'volunteers_count' => $volunteersCount,
            ]);

            // Create donation items
            foreach ($selectedFoods as $food) {
                DonationItem::create([
                    'donation_id' => $donation->id,
                    'food_type' => $food,
                    'quantity' => rand(1, 30),
                    'quantity_unit' => $units[array_rand($units)],
                    'description' => rand(0, 1) ? 'Fresh, prepared today' : null,
                ]);
            }

            $donations[] = $donation;
        }

        // ── Donation Requests (direct-take flow: request rows remain pending only) ──
        $pendingRequestStatuses = ['pending'];
        $messages = [
            'We would love to receive this donation for our beneficiaries.',
            'Our center serves 200 families daily, this would help greatly.',
            'We have refrigerated storage ready for pickup.',
            'Can we arrange pickup for tomorrow morning?',
            null, null,
        ];

        $createdRequests = [];
        foreach ($donations as $donation) {
            $requestCount = rand(1, 3);
            $usedCharityIds = [];

            if (in_array($donation->status, ['in_progress', 'completed'], true)) {
                $acceptedCharity = $charities[array_rand($charities)];
                $donation->update(['accepted_charity_id' => $acceptedCharity->id]);

                $approvedRequest = DonationRequest::create([
                    'donation_id' => $donation->id,
                    'charity_id' => $acceptedCharity->id,
                    'status' => DonationRequest::STATUS_PENDING,
                    'message' => $messages[array_rand($messages)],
                ]);
                $createdRequests[] = $approvedRequest;
                $usedCharityIds[] = (int) $acceptedCharity->id;

                $requestCount = max(0, $requestCount - 1);
            }

            for ($i = 0; $i < $requestCount; $i++) {
                $availableCharities = array_values(array_filter($charities, fn ($charity) => !in_array((int) $charity->id, $usedCharityIds, true)));
                if (empty($availableCharities)) {
                    break;
                }

                $charityUser = $availableCharities[array_rand($availableCharities)];
                $usedCharityIds[] = (int) $charityUser->id;

                $req = DonationRequest::create([
                    'donation_id' => $donation->id,
                    'charity_id' => $charityUser->id,
                    'status' => $pendingRequestStatuses[array_rand($pendingRequestStatuses)],
                    'message' => $messages[array_rand($messages)],
                ]);
                $createdRequests[] = $req;
            }
        }

        // ── Donation Assignments (40) ──
        $assignStatuses = ['pending', 'accepted', 'in_progress', 'completed', 'completed', 'completed'];
        $assignNotes = [
            'Will arrive in 30 minutes', 'Traffic delay, ETA 1 hour',
            'Picked up successfully', 'Delivered to charity office', null, null,
        ];

        for ($i = 0; $i < 40; $i++) {
            $donation = $donations[array_rand($donations)];
            $vol = $volunteers[array_rand($volunteers)];
            $assignStatus = $assignStatuses[array_rand($assignStatuses)];
            $type = $vol->role_type === 'packaging' ? 'packaging' : 'delivery';

            $pickupAt = null;
            $deliveredAt = null;
            if (in_array($assignStatus, ['in_progress', 'completed'])) {
                $pickupAt = Carbon::now()->subHours(rand(1, 24));
            }
            if ($assignStatus === 'completed') {
                $deliveredAt = $pickupAt ? $pickupAt->copy()->addHours(rand(1, 3)) : Carbon::now()->subHours(rand(1, 12));
            }

            $reqId = null;
            $matchingReq = DonationRequest::where('donation_id', $donation->id)
                ->when($donation->accepted_charity_id, fn ($q) => $q->where('charity_id', $donation->accepted_charity_id))
                ->first();
            if ($matchingReq) {
                $reqId = $matchingReq->id;
            }

            DonationAssignment::create([
                'donation_id' => $donation->id,
                'donation_request_id' => $reqId,
                'volunteer_id' => $vol->id,
                'assignment_type' => $type,
                'status' => $assignStatus,
                'pickup_at' => $pickupAt,
                'delivered_at' => $deliveredAt,
                'notes' => $assignNotes[array_rand($assignNotes)],
                'is_external_delivery' => rand(0, 10) > 8,
            ]);
        }

        // ── Ratings (for volunteers from donors & charities) ──
        $ratingComments = [
            'Excellent service!', 'Very professional and on time.',
            'Smooth experience overall.', 'Fast delivery, appreciated!',
            'Great volunteer, highly recommend.', 'Handled food with care.',
            'Punctual and reliable.', 'Thank you for helping our community.',
            'Could improve communication.', 'Outstanding effort!',
            null, null,
        ];

        $raterPool = array_merge($donors, $charities);
        foreach ($volunteers as $vol) {
            $numRatings = rand(2, 6);
            $usedRaters = [];
            for ($j = 0; $j < $numRatings; $j++) {
                $rater = $raterPool[array_rand($raterPool)];
                if (in_array($rater->id, $usedRaters)) continue;
                $usedRaters[] = $rater->id;
                Rating::create([
                    'rater_id' => $rater->id,
                    'rateable_id' => $vol->id,
                    'rateable_type' => User::class,
                    'rating' => rand(3, 5),
                    'comment' => $ratingComments[array_rand($ratingComments)],
                ]);
            }
        }

        // ── Award points ──
        // Donors: 10 per donation + 2 per item
        foreach ($donors as $d) {
            $donationCount = Donation::where('user_id', $d->id)->count();
            $itemCount = DonationItem::whereHas('donation', fn($q) => $q->where('user_id', $d->id))->count();
            $d->update(['points' => ($donationCount * 10) + ($itemCount * 2)]);
        }

        // Volunteers: based on assignment statuses
        foreach ($volunteers as $vol) {
            $completedCount = DonationAssignment::where('volunteer_id', $vol->id)->where('status', 'completed')->count();
            $inProgressCount = DonationAssignment::where('volunteer_id', $vol->id)->where('status', 'in_progress')->count();
            $acceptedCount = DonationAssignment::where('volunteer_id', $vol->id)->where('status', 'accepted')->count();
            $ratingsCount = Rating::where('rateable_id', $vol->id)->where('rateable_type', User::class)->count();
            $points = ($completedCount * 25) + ($inProgressCount * 5) + ($acceptedCount * 5) + ($ratingsCount * 3);
            $vol->update(['points' => $points]);
        }

        $this->command->info('Seeded (Jordan): 1 admin, 18 donors (15+3 pending), 12 charities (10+2 pending), 22 volunteers (20+2 pending), 60 donations, requests aligned to pending/in_progress/completed flow, 40 assignments, ratings & points. Login password: ' . self::SEED_PASSWORD);
    }
}
