<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->unsignedInteger('delivery_volunteers_needed')->default(1)->after('volunteers_needed');
            $table->unsignedInteger('packaging_volunteers_needed')->default(0)->after('delivery_volunteers_needed');
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn(['delivery_volunteers_needed', 'packaging_volunteers_needed']);
        });
    }
};
