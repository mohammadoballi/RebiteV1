<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->foreignId('accepted_charity_id')
                ->nullable()
                ->after('user_id')
                ->constrained('users')
                ->nullOnDelete();
        });

        Schema::table('ratings', function (Blueprint $table) {
            $table->foreignId('donation_id')
                ->nullable()
                ->after('rater_id')
                ->constrained('donations')
                ->cascadeOnDelete();

            $table->unique(
                ['rater_id', 'rateable_id', 'rateable_type', 'donation_id'],
                'ratings_unique_per_donation'
            );
        });

        Schema::table('donation_requests', function (Blueprint $table) {
            $table->unique(['donation_id', 'charity_id'], 'donation_requests_donation_charity_unique');
        });

        $approvedRequests = \Illuminate\Support\Facades\DB::table('donation_requests')
            ->where('status', 'approved')
            ->get(['donation_id', 'charity_id']);

        foreach ($approvedRequests as $request) {
            \Illuminate\Support\Facades\DB::table('donations')
                ->where('id', $request->donation_id)
                ->whereNull('accepted_charity_id')
                ->update([
                    'accepted_charity_id' => $request->charity_id,
                    'status' => 'accepted',
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('donation_requests', function (Blueprint $table) {
            $table->dropUnique('donation_requests_donation_charity_unique');
        });

        Schema::table('ratings', function (Blueprint $table) {
            $table->dropUnique('ratings_unique_per_donation');
            $table->dropConstrainedForeignId('donation_id');
        });

        Schema::table('donations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('accepted_charity_id');
        });
    }
};
