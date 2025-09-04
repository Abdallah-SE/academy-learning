<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class RoleControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $superAdmin;
    protected $admin;
    protected $teacher;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create permissions and roles
        $this->seed(\Database\Seeders\PermissionsAndRolesSeeder::class);
        
        // Create users with roles
        $this->superAdmin = User::factory()->create(['role' => 'super_admin']);
        $this->superAdmin->assignRole('super_admin');
        
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->admin->assignRole('admin');
        
        $this->teacher = User::factory()->create(['role' => 'teacher']);
        $this->teacher->assignRole('teacher');
    }

    /** @test */
    public function admin_can_view_all_roles()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/roles');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data' => [
                        '*' => [
                            'id', 'name', 'display_name', 'description', 'level'
                        ]
                    ]
                ],
                'message'
            ]);
    }

    /** @test */
    public function admin_can_create_role()
    {
        $roleData = [
            'name' => 'moderator',
            'display_name' => 'Moderator',
            'description' => 'Content moderation role',
            'level' => 2,
            'permissions' => ['content.view', 'content.edit']
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/roles', $roleData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id', 'name', 'display_name', 'description', 'level'
                ],
                'message'
            ]);

        $this->assertDatabaseHas('roles', [
            'name' => 'moderator',
            'display_name' => 'Moderator'
        ]);
    }

    /** @test */
    public function admin_can_view_specific_role()
    {
        $role = Role::where('name', 'teacher')->first();

        $response = $this->actingAs($this->admin)
            ->getJson("/api/roles/{$role->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $role->id,
                    'name' => 'teacher'
                ]
            ]);
    }

    /** @test */
    public function admin_can_update_role()
    {
        $role = Role::where('name', 'teacher')->first();
        
        $updateData = [
            'display_name' => 'Updated Teacher Role',
            'description' => 'Updated description'
        ];

        $response = $this->actingAs($this->admin)
            ->putJson("/api/roles/{$role->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'display_name' => 'Updated Teacher Role',
                    'description' => 'Updated description'
                ]
            ]);
    }

    /** @test */
    public function admin_can_delete_role_without_users()
    {
        $role = Role::create([
            'name' => 'test_role',
            'display_name' => 'Test Role',
            'description' => 'Test role for deletion',
            'level' => 1,
            'guard_name' => 'web'
        ]);

        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/roles/{$role->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    /** @test */
    public function admin_cannot_delete_system_roles()
    {
        $role = Role::where('name', 'student')->first();

        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/roles/{$role->id}");

        $response->assertStatus(400);
        $this->assertDatabaseHas('roles', ['id' => $role->id]);
    }

    /** @test */
    public function admin_can_view_role_permissions()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/roles/permissions');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'name', 'description']
                ],
                'message'
            ]);
    }

    /** @test */
    public function admin_can_assign_permissions_to_role()
    {
        $role = Role::where('name', 'teacher')->first();
        $permissions = ['content.create', 'content.edit'];

        $response = $this->actingAs($this->admin)
            ->postJson("/api/roles/{$role->id}/permissions", [
                'permissions' => $permissions
            ]);

        $response->assertStatus(200);
        
        $role->refresh();
        $this->assertTrue($role->hasPermissionTo('content.create'));
        $this->assertTrue($role->hasPermissionTo('content.edit'));
    }

    /** @test */
    public function admin_can_view_role_statistics()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/roles/statistics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'total_roles',
                    'roles_by_level',
                    'roles_with_users'
                ],
                'message'
            ]);
    }

    /** @test */
    public function admin_can_duplicate_role()
    {
        $role = Role::where('name', 'teacher')->first();

        $response = $this->actingAs($this->admin)
            ->postJson("/api/roles/{$role->id}/duplicate");

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id', 'name', 'display_name'
                ],
                'message'
            ]);

        $this->assertDatabaseHas('roles', [
            'name' => 'teacher_copy_' . time(),
            'status' => 'inactive'
        ]);
    }

    /** @test */
    public function can_filter_roles_by_level()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/roles?level=2');

        $response->assertStatus(200);
        
        $roles = $response->json('data.data');
        foreach ($roles as $role) {
            $this->assertEquals(2, $role['level']);
        }
    }

    /** @test */
    public function can_search_roles()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/roles?search=teacher');

        $response->assertStatus(200);
        
        $roles = $response->json('data.data');
        $this->assertGreaterThan(0, count($roles));
    }

    /** @test */
    public function non_admin_cannot_view_roles()
    {
        $response = $this->actingAs($this->teacher)
            ->getJson('/api/roles');

        $response->assertStatus(403);
    }

    /** @test */
    public function non_admin_cannot_create_roles()
    {
        $roleData = [
            'name' => 'unauthorized_role',
            'display_name' => 'Unauthorized Role',
            'description' => 'This should fail',
            'level' => 1
        ];

        $response = $this->actingAs($this->teacher)
            ->postJson('/api/roles', $roleData);

        $response->assertStatus(403);
    }

    /** @test */
    public function validation_fails_with_invalid_data()
    {
        $invalidData = [
            'name' => '',
            'display_name' => '',
            'level' => 'invalid-level'
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/roles', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'display_name', 'level']);
    }

    /** @test */
    public function cannot_create_role_with_duplicate_name()
    {
        $roleData = [
            'name' => 'student', // Already exists
            'display_name' => 'Duplicate Student',
            'description' => 'This should fail',
            'level' => 1
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/roles', $roleData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function role_level_must_be_within_valid_range()
    {
        $roleData = [
            'name' => 'test_role',
            'display_name' => 'Test Role',
            'description' => 'Test role',
            'level' => 15 // Invalid level
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/roles', $roleData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['level']);
    }

    /** @test */
    public function permissions_must_exist_when_assigning()
    {
        $role = Role::where('name', 'teacher')->first();
        $invalidPermissions = ['invalid.permission', 'another.invalid'];

        $response = $this->actingAs($this->admin)
            ->postJson("/api/roles/{$role->id}/permissions", [
                'permissions' => $invalidPermissions
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['permissions.0', 'permissions.1']);
    }

    /** @test */
    public function roles_are_properly_paginated()
    {
        // Create additional roles for pagination
        Role::create([
            'name' => 'role1',
            'display_name' => 'Role 1',
            'level' => 1,
            'guard_name' => 'web'
        ]);
        Role::create([
            'name' => 'role2',
            'display_name' => 'Role 2',
            'level' => 1,
            'guard_name' => 'web'
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/roles?per_page=5');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertLessThanOrEqual(5, count($data['data']));
        $this->assertArrayHasKey('current_page', $data);
    }

    /** @test */
    public function role_permissions_are_synced_correctly()
    {
        $role = Role::where('name', 'teacher')->first();
        $originalPermissions = $role->permissions->pluck('name')->toArray();
        
        $newPermissions = ['content.view', 'content.create'];

        $response = $this->actingAs($this->admin)
            ->postJson("/api/roles/{$role->id}/permissions", [
                'permissions' => $newPermissions
            ]);

        $response->assertStatus(200);
        
        $role->refresh();
        $this->assertEquals($newPermissions, $role->permissions->pluck('name')->toArray());
        $this->assertNotEquals($originalPermissions, $role->permissions->pluck('name')->toArray());
    }

    /** @test */
    public function role_statistics_include_permission_distribution()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/roles/statistics');

        $response->assertStatus(200);
        
        $stats = $response->json('data');
        $this->assertArrayHasKey('permission_distribution', $stats);
        
        // Check that each role has permission information
        foreach ($stats['permission_distribution'] as $roleName => $data) {
            $this->assertArrayHasKey('permission_count', $data);
            $this->assertArrayHasKey('permissions', $data);
        }
    }

    /** @test */
    public function cannot_edit_role_with_higher_level()
    {
        $highLevelRole = Role::where('name', 'super_admin')->first();
        
        $response = $this->actingAs($this->admin)
            ->putJson("/api/roles/{$highLevelRole->id}", [
                'display_name' => 'Hacked'
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function cannot_delete_role_with_higher_level()
    {
        $highLevelRole = Role::where('name', 'super_admin')->first();
        
        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/roles/{$highLevelRole->id}");

        $response->assertStatus(403);
    }
}
