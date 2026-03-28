<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('city_id')->nullable()->after('city')->constrained('cities')->nullOnDelete();
            $table->foreignId('town_id')->nullable()->after('city_id')->constrained('towns')->nullOnDelete();
        });

        Schema::table('donations', function (Blueprint $table) {
            $table->foreignId('city_id')->nullable()->after('user_id')->constrained('cities')->nullOnDelete();
            $table->foreignId('town_id')->nullable()->after('city_id')->constrained('towns')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropForeign(['town_id']);
            $table->dropForeign(['city_id']);
            $table->dropColumn(['city_id', 'town_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['town_id']);
            $table->dropForeign(['city_id']);
            $table->dropColumn(['city_id', 'town_id']);
        });
    }
};
