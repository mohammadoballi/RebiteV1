<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('label')->nullable();
            $table->string('type')->default('number');
            $table->timestamps();
        });

        // Defaults for fresh installs; `SettingsSeeder` keeps these in sync when you run `db:seed`
        $now = now();
        DB::table('settings')->insert([
            ['key' => 'max_pending_requests_per_charity', 'value' => '3', 'label' => 'Max pending donation requests per charity', 'type' => 'number', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'volunteer_signup_points', 'value' => '5', 'label' => 'Points for volunteer when assigned to donation', 'type' => 'number', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'volunteer_pickup_points', 'value' => '5', 'label' => 'Points for volunteer on pickup', 'type' => 'number', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'volunteer_delivery_points', 'value' => '25', 'label' => 'Points for volunteer on delivery', 'type' => 'number', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'volunteer_rating_points', 'value' => '3', 'label' => 'Points for volunteer when rated', 'type' => 'number', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'donor_points_per_donation', 'value' => '10', 'label' => 'Points for donor per donation', 'type' => 'number', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'donor_points_per_item', 'value' => '2', 'label' => 'Points for donor per donation item', 'type' => 'number', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'charity_subscription_price', 'value' => '2', 'label' => 'Monthly subscription price for charities (JD)', 'type' => 'number', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
