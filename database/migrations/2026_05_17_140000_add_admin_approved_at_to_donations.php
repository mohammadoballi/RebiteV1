<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->timestamp('admin_approved_at')->nullable()->after('status');
        });

        \Illuminate\Support\Facades\DB::table('donations')
            ->where('status', 'pending')
            ->whereNull('admin_approved_at')
            ->update(['admin_approved_at' => now()]);

        \Illuminate\Support\Facades\DB::table('donations')
            ->where('status', 'accepted')
            ->whereNull('accepted_charity_id')
            ->update([
                'admin_approved_at' => now(),
                'status' => 'pending',
            ]);
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn('admin_approved_at');
        });
    }
};
