<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin-specific roles
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web'
        ]);

        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web'
        ]);

        $moderatorRole = Role::firstOrCreate([
            'name' => 'moderator',
            'guard_name' => 'web'
        ]);

        $this->command->info('Admin roles created successfully!');
        $this->command->info('Created roles: super_admin, admin, moderator');
    }
}
