<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionsAndRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔄 Resetting cached permissions...');
        
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for User management
        $userPermissions = [
            'users.view' => 'View users',
            'users.create' => 'Create users',
            'users.edit' => 'Edit users',
            'users.delete' => 'Delete users',
            'users.manage_roles' => 'Manage user roles',
            'users.manage_status' => 'Manage user status',
            'users.view_statistics' => 'View user statistics'
        ];

        // Create permissions for Membership management
        $membershipPermissions = [
            'memberships.view' => 'View memberships',
            'memberships.create' => 'Create memberships',
            'memberships.edit' => 'Edit memberships',
            'memberships.delete' => 'Delete memberships',
            'memberships.verify_payment' => 'Verify payments',
            'memberships.view_statistics' => 'View membership statistics'
        ];

        // Create permissions for Package management
        $packagePermissions = [
            'packages.view' => 'View packages',
            'packages.create' => 'Create packages',
            'packages.edit' => 'Edit packages',
            'packages.delete' => 'Delete packages',
            'packages.duplicate' => 'Duplicate packages',
            'packages.view_statistics' => 'View package statistics'
        ];

        // Create permissions for Content management
        $contentPermissions = [
            'content.view' => 'View content',
            'content.create' => 'Create content',
            'content.edit' => 'Edit content',
            'content.delete' => 'Delete content',
            'content.publish' => 'Publish content',
            'content.schedule' => 'Schedule content',
            'content.upload_media' => 'Upload media'
        ];

        // Create permissions for Quran progress
        $quranPermissions = [
            'quran.view' => 'View Quran lessons',
            'quran.progress.view' => 'View progress',
            'quran.progress.edit' => 'Edit progress',
            'quran.assessments.create' => 'Create assessments',
            'quran.assessments.edit' => 'Edit assessments',
            'quran.assessments.delete' => 'Delete assessments',
            'quran.statistics.view' => 'View Quran statistics'
        ];

        // Create permissions for Homework management
        $homeworkPermissions = [
            'homework.view' => 'View homework',
            'homework.create' => 'Create homework',
            'homework.edit' => 'Edit homework',
            'homework.delete' => 'Delete homework',
            'homework.grade' => 'Grade homework',
            'homework.submit' => 'Submit homework'
        ];

        // Create permissions for Meeting management
        $meetingPermissions = [
            'meetings.view' => 'View meetings',
            'meetings.create' => 'Create meetings',
            'meetings.edit' => 'Edit meetings',
            'meetings.delete' => 'Delete meetings',
            'meetings.join' => 'Join meetings',
            'meetings.manage_participants' => 'Manage participants'
        ];

        // Create permissions for Analytics
        $analyticsPermissions = [
            'analytics.view' => 'View analytics',
            'analytics.export' => 'Export analytics',
            'analytics.reports' => 'Generate reports'
        ];

        // Create permissions for System management
        $systemPermissions = [
            'system.settings' => 'Manage system settings',
            'system.backup' => 'Manage backups',
            'system.logs' => 'View system logs',
            'system.maintenance' => 'Maintenance mode'
        ];

        // Combine all permissions
        $allPermissions = array_merge(
            $userPermissions,
            $membershipPermissions,
            $packagePermissions,
            $contentPermissions,
            $quranPermissions,
            $homeworkPermissions,
            $meetingPermissions,
            $analyticsPermissions,
            $systemPermissions
        );

        // Create permissions
        $this->command->info('📝 Creating permissions...');
        foreach ($allPermissions as $permission => $description) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // Create roles for web guard (Users)
        $studentRole = Role::create([
            'name' => 'student',
            'guard_name' => 'web'
        ]);

        $studentPermissions = [
            'quran.view',
            'quran.progress.view',
            'homework.view',
            'homework.submit',
            'meetings.view',
            'meetings.join',
            'content.view'
        ];

        $studentRole->givePermissionTo($studentPermissions);

        // Teacher Role
        $teacherRole = Role::create([
            'name' => 'teacher',
            'guard_name' => 'web'
        ]);

        $teacherPermissions = [
            'quran.view',
            'quran.progress.view',
            'quran.progress.edit',
            'quran.assessments.create',
            'quran.assessments.edit',
            'quran.assessments.delete',
            'homework.view',
            'homework.create',
            'homework.edit',
            'homework.grade',
            'meetings.view',
            'meetings.create',
            'meetings.edit',
            'meetings.manage_participants',
            'content.view',
            'content.create',
            'content.edit',
            'content.upload_media'
        ];

        $teacherRole->givePermissionTo($teacherPermissions);

        // Admin Role
        $adminRole = Role::create([
            'name' => 'admin',
            'guard_name' => 'web'
        ]);

        // Admin gets all permissions
        $adminRole->givePermissionTo(Permission::all());

        // Super Admin Role
        $superAdminRole = Role::create([
            'name' => 'super_admin',
            'guard_name' => 'web'
        ]);

        // Super Admin gets all permissions
        $superAdminRole->givePermissionTo(Permission::all());

        // Create roles for admin guard (Admins)
        $adminSuperAdminRole = Role::create([
            'name' => 'super_admin',
            'guard_name' => 'admin'
        ]);

        $adminRole = Role::create([
            'name' => 'admin',
            'guard_name' => 'admin'
        ]);

        $moderatorRole = Role::create([
            'name' => 'moderator',
            'guard_name' => 'admin'
        ]);

        // Content Manager Role
        $contentManagerRole = Role::create([
            'name' => 'content_manager',
            'guard_name' => 'web'
        ]);

        $contentManagerPermissions = [
            'content.view',
            'content.create',
            'content.edit',
            'content.delete',
            'content.publish',
            'content.schedule',
            'content.upload_media',
            'quran.view',
            'homework.view'
        ];

        $contentManagerRole->givePermissionTo($contentManagerPermissions);

        // Financial Manager Role
        $financialManagerRole = Role::create([
            'name' => 'financial_manager',
            'guard_name' => 'web'
        ]);

        $financialManagerPermissions = [
            'memberships.view',
            'memberships.create',
            'memberships.edit',
            'memberships.verify_payment',
            'memberships.view_statistics',
            'packages.view',
            'packages.create',
            'packages.edit',
            'analytics.view',
            'analytics.reports'
        ];

        $financialManagerRole->givePermissionTo($financialManagerPermissions);

        $this->command->info('✅ Permissions and roles seeded successfully!');
        $this->command->info('📊 Created ' . count($allPermissions) . ' permissions');
        $this->command->info('📊 Created ' . Role::count() . ' roles');
    }
}
