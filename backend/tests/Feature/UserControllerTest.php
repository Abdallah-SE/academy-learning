<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\MembershipPackage;
use App\Models\UserMembership;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $teacher;
    protected $student;
    protected $package;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create permissions and roles
        $this->seed(\Database\Seeders\PermissionsAndRolesSeeder::class);
        
        // Create users with roles
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->admin->assignRole('admin');
        
        $this->teacher = User::factory()->create(['role' => 'teacher']);
        $this->teacher->assignRole('teacher');
        
        $this->student = User::factory()->create(['role' => 'student']);
        $this->student->assignRole('student');
        
        $this->package = MembershipPackage::factory()->create();
    }

    /** @test */
    public function admin_can_view_all_users()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data' => [
                        '*' => [
                            'id', 'name', 'email', 'role', 'status'
                        ]
                    ]
                ],
                'message'
            ]);
    }

    /** @test */
    public function admin_can_create_user()
    {
        $userData = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
            'status' => 'active'
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/users', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id', 'name', 'email', 'role', 'status'
                ],
                'message'
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'role' => 'student'
        ]);
    }

    /** @test */
    public function admin_can_update_user()
    {
        $user = User::factory()->create(['role' => 'student']);
        
        $updateData = [
            'name' => 'Updated Name',
            'status' => 'inactive'
        ];

        $response = $this->actingAs($this->admin)
            ->putJson("/api/users/{$user->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'Updated Name',
                    'status' => 'inactive'
                ]
            ]);
    }

    /** @test */
    public function admin_can_delete_user()
    {
        $user = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /** @test */
    public function user_can_view_own_profile()
    {
        $response = $this->actingAs($this->student)
            ->getJson('/api/users/profile');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $this->student->id,
                    'name' => $this->student->name,
                    'email' => $this->student->email
                ]
            ]);
    }

    /** @test */
    public function user_can_update_own_profile()
    {
        $updateData = [
            'name' => 'Updated Student Name',
            'phone' => '+1234567890'
        ];

        $response = $this->actingAs($this->student)
            ->putJson('/api/users/profile', $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'Updated Student Name',
                    'phone' => '+1234567890'
                ]
            ]);
    }

    /** @test */
    public function user_can_upload_avatar()
    {
        Storage::fake('public');
        
        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($this->student)
            ->postJson('/api/users/avatar', ['avatar' => $file]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['avatar_url'],
                'message'
            ]);

        $this->student->refresh();
        $this->assertNotNull($this->student->avatar);
    }

    /** @test */
    public function user_can_verify_whatsapp()
    {
        $verificationData = [
            'phone' => '+1234567890',
            'verification_code' => '123456'
        ];

        $response = $this->actingAs($this->student)
            ->postJson('/api/users/whatsapp/verify', $verificationData);

        $response->assertStatus(200);
        
        $this->student->refresh();
        $this->assertTrue($this->student->whatsapp_verified);
    }

    /** @test */
    public function admin_can_view_user_statistics()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/users/statistics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'total_users',
                    'students',
                    'teachers',
                    'admins',
                    'active_users'
                ],
                'message'
            ]);
    }

    /** @test */
    public function non_admin_cannot_view_all_users()
    {
        $response = $this->actingAs($this->student)
            ->getJson('/api/users');

        $response->assertStatus(403);
    }

    /** @test */
    public function non_admin_cannot_create_users()
    {
        $userData = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
            'status' => 'active'
        ];

        $response = $this->actingAs($this->student)
            ->postJson('/api/users', $userData);

        $response->assertStatus(403);
    }

    /** @test */
    public function user_cannot_update_other_user_profile()
    {
        $otherUser = User::factory()->create(['role' => 'student']);
        
        $updateData = ['name' => 'Hacked Name'];

        $response = $this->actingAs($this->student)
            ->putJson("/api/users/{$otherUser->id}", $updateData);

        $response->assertStatus(403);
    }

    /** @test */
    public function validation_fails_with_invalid_data()
    {
        $invalidData = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'role' => 'invalid_role'
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/users', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password', 'role']);
    }

    /** @test */
    public function can_filter_users_by_role()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/users?role=student');

        $response->assertStatus(200);
        
        $users = $response->json('data.data');
        foreach ($users as $user) {
            $this->assertEquals('student', $user['role']);
        }
    }

    /** @test */
    public function can_search_users()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/users?search=' . $this->student->name);

        $response->assertStatus(200);
        
        $users = $response->json('data.data');
        $this->assertGreaterThan(0, count($users));
    }

    /** @test */
    public function can_paginate_users()
    {
        // Create more users for pagination
        User::factory()->count(25)->create();

        $response = $this->actingAs($this->admin)
            ->getJson('/api/users?per_page=10');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertEquals(10, count($data['data']));
        $this->assertArrayHasKey('current_page', $data);
    }

    /** @test */
    public function user_can_view_own_membership_status()
    {
        // Create membership for student
        UserMembership::factory()->create([
            'user_id' => $this->student->id,
            'package_id' => $this->package->id,
            'status' => 'active'
        ]);

        $this->student->refresh();
        
        $this->assertTrue($this->student->hasActiveMembership());
        $this->assertTrue($this->student->isActive());
    }

    /** @test */
    public function user_roles_are_correctly_assigned()
    {
        $this->assertTrue($this->admin->isAdmin());
        $this->assertTrue($this->teacher->isTeacher());
        $this->assertTrue($this->student->isStudent());
    }

    /** @test */
    public function user_can_access_appropriate_permissions()
    {
        $this->assertTrue($this->admin->hasPermissionTo('users.view'));
        $this->assertTrue($this->teacher->hasPermissionTo('content.create'));
        $this->assertTrue($this->student->hasPermissionTo('quran.view'));
    }

    /** @test */
    public function user_cannot_access_unauthorized_permissions()
    {
        $this->assertFalse($this->student->hasPermissionTo('users.create'));
        $this->assertFalse($this->teacher->hasPermissionTo('system.settings'));
    }
}
