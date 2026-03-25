<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('food_type');
            $table->text('description')->nullable();
            $table->string('quantity');
            $table->string('quantity_unit')->default('kg');
            $table->text('pickup_address');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->dateTime('pickup_time');
            $table->dateTime('expiry_time')->nullable();
            $table->enum('status', [
                'pending', 'accepted', 'assigned', 'in_transit',
                'delivered', 'completed', 'cancelled',
            ])->default('pending');
            $table->text('notes')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('food_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
