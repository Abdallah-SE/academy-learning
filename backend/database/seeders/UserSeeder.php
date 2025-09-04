<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('👥 Creating regular users...');
        
        // Create admin user
        $admin = User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@arabicacademy.com',
            'password' => bcrypt('password'),
        ]);

        // Assign admin role
        $admin->assignRole('admin');

        // Create teacher users
        $teachers = User::factory()->teacher()->count(5)->create();
        
        // Assign teacher role to all teachers
        $teachers->each(function ($teacher) {
            $teacher->assignRole('teacher');
        });

        // Create student users
        $students = User::factory()->student()->count(20)->create();
        
        // Assign student role to all students
        $students->each(function ($student) {
            $student->assignRole('student');
        });

        $this->command->info('✅ Regular users seeded successfully!');
        $this->command->info('📊 Created ' . $teachers->count() . ' teachers, ' . $students->count() . ' students');
    }
}
