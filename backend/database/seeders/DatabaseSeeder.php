<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting database seeding process...');
        
        // Seed permissions and roles first (this must run first as other seeders depend on it)
        $this->command->info('📋 Seeding permissions and roles...');
        $this->call([
            PermissionsAndRolesSeeder::class,
        ]);

        // Seed all data in the correct order
        $this->command->info('📦 Seeding membership data...');
        $this->call([
            MembershipSeeder::class,
        ]);

        $this->command->info('👨‍💼 Seeding admin users...');
        $this->call([
            AdminSeeder::class,
        ]);

        $this->command->info('👥 Seeding regular users...');
        $this->call([
            UserSeeder::class,
        ]);

        $this->command->info('✅ Database seeded successfully!');
        $this->displaySeedingSummary();
    }

    /**
     * Display a summary of seeded data
     */
    private function displaySeedingSummary(): void
    {
        $this->command->info('');
        $this->command->info('📊 Seeding Summary:');
        $this->command->info('├── Permissions: ' . \Spatie\Permission\Models\Permission::count());
        $this->command->info('├── Roles: ' . \Spatie\Permission\Models\Role::count());
        $this->command->info('├── Admin Users: ' . \Modules\Admin\Models\Admin::count());
        $this->command->info('├── Regular Users: ' . \App\Models\User::count());
        $this->command->info('├── Membership Packages: ' . \App\Models\MembershipPackage::count());
        $this->command->info('└── User Memberships: ' . \App\Models\UserMembership::count());
        $this->command->info('');
    }
}
