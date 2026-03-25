<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donation_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_id')->constrained('donations')->onDelete('cascade');
            $table->foreignId('donation_request_id')->nullable()->constrained('donation_requests')->onDelete('set null');
            $table->foreignId('volunteer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('assignment_type', ['delivery', 'packaging']);
            $table->enum('status', [
                'pending', 'accepted', 'in_progress', 'completed', 'cancelled',
            ])->default('pending');
            $table->dateTime('pickup_at')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_external_delivery')->default(false);
            $table->timestamps();

            $table->index('status');
            $table->index('assignment_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_assignments');
    }
};
