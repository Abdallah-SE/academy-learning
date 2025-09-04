<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserMembership;
use App\Models\MembershipPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_user()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'student',
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertEquals('student', $user->role);
    }

    /** @test */
    public function it_can_check_user_roles()
    {
        $admin = User::factory()->admin()->create();
        $teacher = User::factory()->teacher()->create();
        $student = User::factory()->student()->create();

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isTeacher());
        $this->assertFalse($admin->isStudent());

        $this->assertFalse($teacher->isAdmin());
        $this->assertTrue($teacher->isTeacher());
        $this->assertFalse($teacher->isStudent());

        $this->assertFalse($student->isAdmin());
        $this->assertFalse($student->isTeacher());
        $this->assertTrue($student->isStudent());
    }

    /** @test */
    public function it_can_check_user_status()
    {
        $activeUser = User::factory()->create(['status' => 'active']);
        $inactiveUser = User::factory()->create(['status' => 'inactive']);
        $suspendedUser = User::factory()->create(['status' => 'suspended']);

        $this->assertTrue($activeUser->isActive());
        $this->assertFalse($inactiveUser->isActive());
        $this->assertFalse($suspendedUser->isActive());
    }

    /** @test */
    public function it_can_have_memberships()
    {
        $user = User::factory()->create();
        $package = MembershipPackage::factory()->create();
        
        $membership = UserMembership::factory()->create([
            'user_id' => $user->id,
            'package_id' => $package->id,
        ]);

        $this->assertInstanceOf(UserMembership::class, $user->memberships->first());
        $this->assertEquals($membership->id, $user->memberships->first()->id);
    }

    /** @test */
    public function it_can_have_active_membership()
    {
        $user = User::factory()->create();
        $package = MembershipPackage::factory()->create();
        
        UserMembership::factory()->active()->create([
            'user_id' => $user->id,
            'package_id' => $package->id,
        ]);

        $this->assertTrue($user->hasActiveMembership());
        $this->assertInstanceOf(UserMembership::class, $user->activeMembership);
    }

    /** @test */
    public function it_can_check_membership_status()
    {
        $user = User::factory()->create();
        $package = MembershipPackage::factory()->create();
        
        // No membership
        $this->assertFalse($user->hasActiveMembership());

        // Expired membership
        UserMembership::factory()->expired()->create([
            'user_id' => $user->id,
            'package_id' => $package->id,
        ]);

        $this->assertFalse($user->hasActiveMembership());

        // Active membership
        UserMembership::factory()->active()->create([
            'user_id' => $user->id,
            'package_id' => $package->id,
        ]);

        $this->assertTrue($user->hasActiveMembership());
    }

    /** @test */
    public function it_casts_attributes_correctly()
    {
        $user = User::factory()->create([
            'whatsapp_verified' => true,
            'date_of_birth' => '1990-01-01',
            'preferences' => ['theme' => 'dark', 'language' => 'en'],
        ]);

        $this->assertIsBool($user->whatsapp_verified);
        $this->assertTrue($user->whatsapp_verified);
        $this->assertInstanceOf(\Carbon\Carbon::class, $user->date_of_birth);
        $this->assertIsArray($user->preferences);
        $this->assertEquals('dark', $user->preferences['theme']);
    }

    /** @test */
    public function it_can_have_whatsapp_verification()
    {
        $verifiedUser = User::factory()->whatsappVerified()->create();
        $unverifiedUser = User::factory()->create(['whatsapp_verified' => false]);

        $this->assertTrue($verifiedUser->whatsapp_verified);
        $this->assertFalse($unverifiedUser->whatsapp_verified);
    }
}
