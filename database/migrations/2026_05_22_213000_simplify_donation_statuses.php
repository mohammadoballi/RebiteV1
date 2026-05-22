<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('donations')) {
            return;
        }

        DB::table('donations')
            ->whereIn('status', ['accepted', 'assigned', 'in_transit', 'delivered', 'cancelled'])
            ->update([
                'status' => DB::raw("CASE 
                    WHEN status IN ('accepted','assigned','in_transit','delivered') THEN 'in_progress' 
                    ELSE 'pending' 
                END"),
            ]);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE donations MODIFY status ENUM('pending','in_progress','completed') NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('donations')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE donations MODIFY status ENUM('pending','accepted','assigned','in_transit','delivered','completed','cancelled') NOT NULL DEFAULT 'pending'");
        }

        DB::table('donations')
            ->where('status', 'in_progress')
            ->update(['status' => 'accepted']);
    }
};
