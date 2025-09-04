<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Admin\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('👨‍💼 Creating admin users...');
        
        // Create Super Admin
        $superAdmin = Admin::firstOrCreate(
            ['email' => 'superadmin@arabicacademy.com'],
            [
                'name' => 'Super Administrator',
                'username' => 'superadmin',
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->assignRole('super_admin');

        // Create Admin
        $admin = Admin::firstOrCreate(
            ['email' => 'admin@arabicacademy.com'],
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // Create Moderator
        $moderator = Admin::firstOrCreate(
            ['email' => 'moderator@arabicacademy.com'],
            [
                'name' => 'Moderator',
                'username' => 'moderator',
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $moderator->assignRole('moderator');

        // Create additional 7 admins manually (total 10)
        $adminNames = [
            'Ahmed Hassan', 'Fatima Al-Zahra', 'Mohammed Ali', 'Aisha Rahman',
            'Omar Khalil', 'Zainab Ibrahim', 'Yusuf Ahmed'
        ];
        
        for ($i = 0; $i < 7; $i++) {
            $adminNumber = $i + 1;
            $admin = Admin::create([
                'name' => $adminNames[$i],
                'email' => 'admin' . $adminNumber . '@arabicacademy.com',
                'username' => 'admin' . $adminNumber,
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
            
            // Distribute roles: 4 admins, 3 moderators
            if ($i < 4) {
                $admin->assignRole('admin');
            } else {
                $admin->assignRole('moderator');
            }
        }

        $this->command->info('✅ Admin users seeded successfully!');
        $this->command->info('📊 Total admins created: ' . Admin::count());
        $this->command->info('🔑 Login: superadmin@arabicacademy.com / password');
    }
}
