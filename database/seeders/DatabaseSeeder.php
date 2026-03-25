<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Donation;
use App\Models\DonationItem;
use App\Models\DonationRequest;
use App\Models\DonationAssignment;
use App\Models\Rating;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Roles ──
        $admin = Role::create(['name' => 'admin', 'display_name' => 'Administrator', 'description' => 'System Administrator']);
        $donor = Role::create(['name' => 'donor', 'display_name' => 'Donor', 'description' => 'Food Donor']);
        $charity = Role::create(['name' => 'charity', 'display_name' => 'Charity', 'description' => 'Charity Organization']);
        $volunteer = Role::create(['name' => 'volunteer', 'display_name' => 'Volunteer', 'description' => 'Volunteer']);

        // ── Permissions ──
        $permissions = [
            'users-create', 'users-read', 'users-update', 'users-delete', 'users-approve',
            'donations-create', 'donations-read', 'donations-update', 'donations-delete',
            'donations-request', 'donations-assign',
            'assignments-read', 'assignments-update',
            'reports-read',
        ];
        foreach ($permissions as $perm) {
            Permission::create(['name' => $perm, 'display_name' => ucwords(str_replace('-', ' ', $perm))]);
        }
        $admin->givePermissions(Permission::all());
        $donor->givePermissions(['donations-create', 'donations-read', 'donations-update', 'donations-delete']);
        $charity->givePermissions(['donations-read', 'donations-request']);
        $volunteer->givePermissions(['assignments-read', 'assignments-update']);

        // ── Admin ──
        $adminUser = User::create([
            'name' => 'Admin', 'email' => 'admin@rebite.com',
            'password' => Hash::make('password'), 'phone' => '0500000000',
            'status' => 'approved', 'locale' => 'en',
        ]);
        $adminUser->addRole('admin');

        // ── Donors (15) ──
        $donorNames = [
            'Al Baik Restaurant', 'Herfy', 'Kudu', 'Shawarmer', 'Pizza Hut Riyadh',
            'Dominos Jeddah', 'McDonald\'s', 'Burger King', 'Hardees', 'KFC Dammam',
            'Tamimi Markets', 'Panda Hypermarket', 'Danube Supermarket', 'LuLu Hypermarket', 'Carrefour Saudi',
        ];
        $cities = ['Riyadh', 'Jeddah', 'Dammam', 'Makkah', 'Madinah', 'Khobar', 'Tabuk', 'Abha'];
        $donors = [];
        foreach ($donorNames as $i => $name) {
            $user = User::create([
                'name' => $name, 'email' => 'donor' . ($i + 1) . '@rebite.com',
                'password' => Hash::make('password'), 'phone' => '055' . str_pad($i, 7, '0', STR_PAD_LEFT),
                'status' => 'approved', 'city' => $cities[array_rand($cities)],
                'address' => 'Street ' . rand(1, 99) . ', District ' . rand(1, 20),
                'locale' => 'en',
            ]);
            $user->addRole('donor');
            $donors[] = $user;
        }

        // 3 pending donors
        for ($i = 0; $i < 3; $i++) {
            $user = User::create([
                'name' => 'Pending Donor ' . ($i + 1), 'email' => 'pendingdonor' . ($i + 1) . '@rebite.com',
                'password' => Hash::make('password'), 'phone' => '056' . str_pad($i, 7, '0', STR_PAD_LEFT),
                'status' => 'pending', 'city' => $cities[array_rand($cities)],
                'health_certificate' => 'certificates/sample.pdf', 'locale' => 'en',
            ]);
            $user->addRole('donor');
        }

        // ── Charities (10) ──
        $charityNames = [
            'Food Bank Saudi', 'Ehsan Foundation', 'Ita\'am (إطعام)', 'Saudi Red Crescent',
            'Takaful Charity', 'Al-Bir Society Riyadh', 'Orphans Care Society', 'Ensan Charity',
            'Al-Khair Foundation', 'Bir Society Jeddah',
        ];
        $charities = [];
        foreach ($charityNames as $i => $name) {
            $user = User::create([
                'name' => $name, 'email' => 'charity' . ($i + 1) . '@rebite.com',
                'password' => Hash::make('password'), 'phone' => '057' . str_pad($i, 7, '0', STR_PAD_LEFT),
                'status' => 'approved', 'city' => $cities[array_rand($cities)],
                'organization_name' => $name, 'locale' => 'en',
            ]);
            $user->addRole('charity');
            $charities[] = $user;
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
            $user = User::create([
                'name' => $name, 'email' => 'vol' . ($i + 1) . '@rebite.com',
                'password' => Hash::make('password'), 'phone' => '058' . str_pad($i, 7, '0', STR_PAD_LEFT),
                'status' => 'approved', 'role_type' => $type,
                'city' => $cities[array_rand($cities)], 'locale' => 'en',
            ]);
            $user->addRole('volunteer');
            $volunteers[] = $user;
        }

        // 2 pending volunteers
        for ($i = 0; $i < 2; $i++) {
            $user = User::create([
                'name' => 'Pending Volunteer ' . ($i + 1), 'email' => 'pendingvol' . ($i + 1) . '@rebite.com',
                'password' => Hash::make('password'), 'phone' => '059' . str_pad($i, 7, '0', STR_PAD_LEFT),
                'status' => 'pending', 'role_type' => 'delivery', 'locale' => 'en',
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
            'King Fahd Road, District 5', 'Olaya Street, Commercial Area',
            'Prince Sultan Road, Building 12', 'Tahlia Street, Ground Floor',
            'King Abdullah District, Unit 3', 'Al Malqa District, Villa 15',
            'Al Rawdah District, Shop 7', 'Al Hamra Street, Block B',
            'Industrial Area, Warehouse 22', 'University District, Near Gate 4',
        ];

        // ── Donations (60) ──
        $donations = [];
        $statuses = ['pending', 'pending', 'pending', 'accepted', 'accepted', 'assigned', 'in_transit', 'delivered', 'completed', 'completed', 'completed', 'cancelled'];

        for ($i = 0; $i < 60; $i++) {
            $donorUser = $donors[array_rand($donors)];
            $status = $statuses[array_rand($statuses)];
            $volunteersNeeded = rand(1, 5);
            $volunteersCount = 0;
            if (in_array($status, ['assigned', 'in_transit', 'delivered', 'completed'])) {
                $volunteersCount = min(rand(1, $volunteersNeeded), $volunteersNeeded);
            }
            if ($status === 'completed') {
                $volunteersCount = $volunteersNeeded;
            }

            $pickupTime = Carbon::now()->addHours(rand(-48, 168));
            $numItems = rand(1, 5);
            $selectedFoods = array_rand(array_flip($foodTypes), $numItems);
            if (!is_array($selectedFoods)) $selectedFoods = [$selectedFoods];

            $donation = Donation::create([
                'user_id' => $donorUser->id,
                'food_type' => implode(', ', $selectedFoods),
                'description' => rand(0, 1) ? 'Surplus food from today\'s preparation. Good quality and fresh.' : null,
                'quantity' => rand(1, 50),
                'quantity_unit' => $units[array_rand($units)],
                'pickup_address' => $addresses[array_rand($addresses)] . ', ' . $donorUser->city,
                'latitude' => rand(2100, 2700) / 100,
                'longitude' => rand(3600, 5000) / 100,
                'pickup_time' => $pickupTime,
                'expiry_time' => rand(0, 1) ? $pickupTime->copy()->addHours(rand(4, 48)) : null,
                'status' => $status,
                'notes' => rand(0, 1) ? 'Please bring containers. Call before arriving.' : null,
                'volunteers_needed' => $volunteersNeeded,
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

        // ── Donation Requests (80) ──
        $requestStatuses = ['pending', 'pending', 'approved', 'approved', 'rejected'];
        $messages = [
            'We would love to receive this donation for our beneficiaries.',
            'Our center serves 200 families daily, this would help greatly.',
            'We have refrigerated storage ready for pickup.',
            'Can we arrange pickup for tomorrow morning?',
            null, null,
        ];

        $createdRequests = [];
        for ($i = 0; $i < 80; $i++) {
            $donation = $donations[array_rand($donations)];
            $charityUser = $charities[array_rand($charities)];

            $exists = DonationRequest::where('donation_id', $donation->id)
                ->where('charity_id', $charityUser->id)->exists();
            if ($exists) continue;

            $req = DonationRequest::create([
                'donation_id' => $donation->id,
                'charity_id' => $charityUser->id,
                'status' => $requestStatuses[array_rand($requestStatuses)],
                'message' => $messages[array_rand($messages)],
            ]);
            $createdRequests[] = $req;
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
            $matchingReq = DonationRequest::where('donation_id', $donation->id)->where('status', 'approved')->first();
            if ($matchingReq) $reqId = $matchingReq->id;

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

        $this->command->info('Seeded: 1 admin, 18 donors (15+3 pending), 10 charities, 22 volunteers (20+2 pending), 60 donations, ~80 requests, 40 assignments, ratings & points.');
    }
}
