<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\MembershipPackage;
use App\Models\UserMembership;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class UserMembershipControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $financialManager;
    protected $student;
    protected $teacher;
    protected $package;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create permissions and roles
        $this->seed(\Database\Seeders\PermissionsAndRolesSeeder::class);
        
        // Create users with roles
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->admin->assignRole('admin');
        
        $this->financialManager = User::factory()->create(['role' => 'financial_manager']);
        $this->financialManager->assignRole('financial_manager');
        
        $this->student = User::factory()->create(['role' => 'student']);
        $this->student->assignRole('student');
        
        $this->teacher = User::factory()->create(['role' => 'teacher']);
        $this->teacher->assignRole('teacher');
        
        $this->package = MembershipPackage::factory()->create();
    }

    /** @test */
    public function admin_can_view_all_memberships()
    {
        UserMembership::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)
            ->getJson('/api/memberships');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data' => [
                        '*' => [
                            'id', 'user_id', 'package_id', 'status', 'payment_status'
                        ]
                    ]
                ],
                'message'
            ]);
    }

    /** @test */
    public function admin_can_create_membership()
    {
        $membershipData = [
            'user_id' => $this->student->id,
            'package_id' => $this->package->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(30)->toDateString(),
            'payment_method' => 'paypal'
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/memberships', $membershipData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id', 'user_id', 'package_id', 'status', 'payment_status'
                ],
                'message'
            ]);

        $this->assertDatabaseHas('user_memberships', [
            'user_id' => $this->student->id,
            'package_id' => $this->package->id
        ]);
    }

    /** @test */
    public function admin_can_view_specific_membership()
    {
        $membership = UserMembership::factory()->create();

        $response = $this->actingAs($this->admin)
            ->getJson("/api/memberships/{$membership->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $membership->id
                ]
            ]);
    }

    /** @test */
    public function admin_can_update_membership()
    {
        $membership = UserMembership::factory()->create();
        
        $updateData = [
            'status' => 'expired',
            'payment_status' => 'completed'
        ];

        $response = $this->actingAs($this->admin)
            ->putJson("/api/memberships/{$membership->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'status' => 'expired',
                    'payment_status' => 'completed'
                ]
            ]);
    }

    /** @test */
    public function admin_can_delete_membership()
    {
        $membership = UserMembership::factory()->create();

        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/memberships/{$membership->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('user_memberships', ['id' => $membership->id]);
    }

    /** @test */
    public function user_can_view_own_membership()
    {
        $membership = UserMembership::factory()->create([
            'user_id' => $this->student->id,
            'package_id' => $this->package->id
        ]);

        $response = $this->actingAs($this->student)
            ->getJson('/api/memberships/my-membership');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $membership->id
                ]
            ]);
    }

    /** @test */
    public function user_can_view_own_membership_history()
    {
        UserMembership::factory()->count(3)->create([
            'user_id' => $this->student->id,
            'package_id' => $this->package->id
        ]);

        $response = $this->actingAs($this->student)
            ->getJson('/api/memberships/my-history');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'package_id', 'status']
                ],
                'message'
            ]);
    }

    /** @test */
    public function user_can_subscribe_to_package()
    {
        $subscriptionData = [
            'package_id' => $this->package->id,
            'payment_method' => 'whatsapp'
        ];

        $response = $this->actingAs($this->student)
            ->postJson('/api/memberships/subscribe', $subscriptionData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id', 'user_id', 'package_id', 'status'
                ],
                'message'
            ]);

        $this->assertDatabaseHas('user_memberships', [
            'user_id' => $this->student->id,
            'package_id' => $this->package->id,
            'status' => 'active'
        ]);
    }

    /** @test */
    public function user_cannot_subscribe_if_already_has_active_membership()
    {
        // Create active membership
        UserMembership::factory()->create([
            'user_id' => $this->student->id,
            'package_id' => $this->package->id,
            'status' => 'active'
        ]);

        $subscriptionData = [
            'package_id' => $this->package->id,
            'payment_method' => 'paypal'
        ];

        $response = $this->actingAs($this->student)
            ->postJson('/api/memberships/subscribe', $subscriptionData);

        $response->assertStatus(400);
    }

    /** @test */
    public function user_can_cancel_own_membership()
    {
        $membership = UserMembership::factory()->create([
            'user_id' => $this->student->id,
            'package_id' => $this->package->id,
            'status' => 'active'
        ]);

        $response = $this->actingAs($this->student)
            ->postJson("/api/memberships/{$membership->id}/cancel");

        $response->assertStatus(200);
        
        $membership->refresh();
        $this->assertEquals('cancelled', $membership->status);
    }

    /** @test */
    public function user_cannot_cancel_other_membership()
    {
        $membership = UserMembership::factory()->create([
            'user_id' => $this->teacher->id,
            'package_id' => $this->package->id,
            'status' => 'active'
        ]);

        $response = $this->actingAs($this->student)
            ->postJson("/api/memberships/{$membership->id}/cancel");

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_verify_payment()
    {
        $membership = UserMembership::factory()->create([
            'payment_status' => 'pending'
        ]);

        $verificationData = [
            'verification_status' => 'approved',
            'notes' => 'Payment confirmed'
        ];

        $response = $this->actingAs($this->admin)
            ->postJson("/api/memberships/{$membership->id}/verify-payment", $verificationData);

        $response->assertStatus(200);
        
        $membership->refresh();
        $this->assertEquals('completed', $membership->payment_status);
        $this->assertTrue($membership->admin_verified);
    }

    /** @test */
    public function admin_can_view_membership_statistics()
    {
        UserMembership::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)
            ->getJson('/api/memberships/statistics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'total_memberships',
                    'active_memberships',
                    'pending_payments',
                    'completed_payments',
                    'revenue_stats'
                ],
                'message'
            ]);
    }

    /** @test */
    public function can_filter_memberships_by_status()
    {
        UserMembership::factory()->create(['status' => 'active']);
        UserMembership::factory()->create(['status' => 'expired']);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/memberships?status=active');

        $response->assertStatus(200);
        
        $memberships = $response->json('data.data');
        foreach ($memberships as $membership) {
            $this->assertEquals('active', $membership['status']);
        }
    }

    /** @test */
    public function can_filter_memberships_by_payment_method()
    {
        UserMembership::factory()->create(['payment_method' => 'paypal']);
        UserMembership::factory()->create(['payment_method' => 'whatsapp']);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/memberships?payment_method=paypal');

        $response->assertStatus(200);
        
        $memberships = $response->json('data.data');
        foreach ($memberships as $membership) {
            $this->assertEquals('paypal', $membership['payment_method']);
        }
    }

    /** @test */
    public function can_filter_memberships_by_date_range()
    {
        $startDate = now()->subDays(30);
        $endDate = now()->addDays(30);

        UserMembership::factory()->create([
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson("/api/memberships?start_date_from={$startDate->toDateString()}");

        $response->assertStatus(200);
        
        $memberships = $response->json('data.data');
        $this->assertGreaterThan(0, count($memberships));
    }

    /** @test */
    public function non_admin_cannot_view_all_memberships()
    {
        $response = $this->actingAs($this->student)
            ->getJson('/api/memberships');

        $response->assertStatus(403);
    }

    /** @test */
    public function non_admin_cannot_create_memberships()
    {
        $membershipData = [
            'user_id' => $this->teacher->id,
            'package_id' => $this->package->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(30)->toDateString(),
            'payment_method' => 'paypal'
        ];

        $response = $this->actingAs($this->student)
            ->postJson('/api/memberships', $membershipData);

        $response->assertStatus(403);
    }

    /** @test */
    public function validation_fails_with_invalid_data()
    {
        $invalidData = [
            'user_id' => 99999, // Non-existent user
            'package_id' => 99999, // Non-existent package
            'start_date' => 'invalid-date',
            'end_date' => '2020-01-01', // Past date
            'payment_method' => 'invalid-method'
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/memberships', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['user_id', 'package_id', 'start_date', 'end_date', 'payment_method']);
    }

    /** @test */
    public function cannot_create_membership_with_end_date_before_start_date()
    {
        $membershipData = [
            'user_id' => $this->student->id,
            'package_id' => $this->package->id,
            'start_date' => now()->addDays(30)->toDateString(),
            'end_date' => now()->toDateString(),
            'payment_method' => 'paypal'
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/memberships', $membershipData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['end_date']);
    }

    /** @test */
    public function user_cannot_cancel_inactive_membership()
    {
        $membership = UserMembership::factory()->create([
            'user_id' => $this->student->id,
            'package_id' => $this->package->id,
            'status' => 'expired'
        ]);

        $response = $this->actingAs($this->student)
            ->postJson("/api/memberships/{$membership->id}/cancel");

        $response->assertStatus(400);
    }

    /** @test */
    public function payment_verification_requires_valid_status()
    {
        $membership = UserMembership::factory()->create();

        $invalidData = [
            'verification_status' => 'invalid-status'
        ];

        $response = $this->actingAs($this->admin)
            ->postJson("/api/memberships/{$membership->id}/verify-payment", $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['verification_status']);
    }

    /** @test */
    public function memberships_are_properly_paginated()
    {
        UserMembership::factory()->count(25)->create();

        $response = $this->actingAs($this->admin)
            ->getJson('/api/memberships?per_page=10');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertEquals(10, count($data['data']));
        $this->assertArrayHasKey('current_page', $data);
    }

    /** @test */
    public function revenue_statistics_are_calculated_correctly()
    {
        $package1 = MembershipPackage::factory()->create(['price' => 100]);
        $package2 = MembershipPackage::factory()->create(['price' => 200]);

        // Create completed memberships
        UserMembership::factory()->create([
            'package_id' => $package1->id,
            'payment_status' => 'completed'
        ]);
        UserMembership::factory()->create([
            'package_id' => $package2->id,
            'payment_status' => 'completed'
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/memberships/statistics');

        $response->assertStatus(200);
        
        $stats = $response->json('data');
        $this->assertEquals(300, $stats['revenue_stats']['total_revenue']);
        $this->assertEquals(150, $stats['revenue_stats']['average_membership_value']);
    }
}
