<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add new fields to users table
            $table->string('phone')->nullable()->after('email');
            $table->boolean('whatsapp_verified')->default(false)->after('phone');
            $table->enum('role', ['student', 'teacher', 'admin'])->default('student')->after('whatsapp_verified');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('role');
            $table->string('avatar')->nullable()->after('status');
            $table->date('date_of_birth')->nullable()->after('avatar');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
            $table->string('country')->nullable()->after('gender');
            $table->string('timezone')->nullable()->after('country');
            $table->json('preferences')->nullable()->after('timezone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'whatsapp_verified',
                'role',
                'status',
                'avatar',
                'date_of_birth',
                'gender',
                'country',
                'timezone',
                'preferences'
            ]);
        });
    }
};
