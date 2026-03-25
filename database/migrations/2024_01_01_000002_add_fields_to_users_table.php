<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('avatar')->nullable()->after('phone');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('avatar');
            $table->text('address')->nullable()->after('status');
            $table->string('city')->nullable()->after('address');
            $table->string('role_type')->nullable()->after('city');
            $table->string('health_certificate')->nullable()->after('role_type');
            $table->string('organization_name')->nullable()->after('health_certificate');
            $table->string('organization_license')->nullable()->after('organization_name');
            $table->text('rejection_reason')->nullable()->after('organization_license');
            $table->string('locale')->default('en')->after('rejection_reason');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'avatar',
                'status',
                'address',
                'city',
                'role_type',
                'health_certificate',
                'organization_name',
                'organization_license',
                'rejection_reason',
                'locale',
            ]);
        });
    }
};
