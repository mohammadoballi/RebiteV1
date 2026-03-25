<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add volunteer capacity fields to donations
        Schema::table('donations', function (Blueprint $table) {
            $table->unsignedInteger('volunteers_needed')->default(1)->after('image');
            $table->unsignedInteger('volunteers_count')->default(0)->after('volunteers_needed');
        });

        // Create donation_items table for multiple food items per donation
        Schema::create('donation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_id')->constrained('donations')->onDelete('cascade');
            $table->string('food_type');
            $table->string('quantity');
            $table->string('quantity_unit')->default('kg');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_items');

        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn(['volunteers_needed', 'volunteers_count']);
        });
    }
};
