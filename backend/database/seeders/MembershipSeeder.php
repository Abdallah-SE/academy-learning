<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MembershipPackage;
use App\Models\UserMembership;
use App\Models\User;

class MembershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📦 Creating membership packages...');
        
        // Create membership packages
        $basicPackage = MembershipPackage::factory()->basic()->create();
        $standardPackage = MembershipPackage::factory()->standard()->create();
        $premiumPackage = MembershipPackage::factory()->premium()->create();

        // Create some additional random packages
        MembershipPackage::factory()->count(3)->create();

        // Get some students to assign memberships
        $students = User::role('student')->take(10)->get();

        // Assign memberships to some students
        $students->each(function ($student) use ($basicPackage, $standardPackage, $premiumPackage) {
            $package = fake()->randomElement([$basicPackage, $standardPackage, $premiumPackage]);
            
            UserMembership::factory()->create([
                'user_id' => $student->id,
                'package_id' => $package->id,
            ]);
        });

        // Create some expired and pending memberships
        UserMembership::factory()->expired()->count(5)->create();
        UserMembership::factory()->pendingVerification()->count(3)->create();

        $this->command->info('✅ Membership data seeded successfully!');
        $this->command->info('📊 Created ' . MembershipPackage::count() . ' packages, ' . UserMembership::count() . ' memberships');
    }
}
