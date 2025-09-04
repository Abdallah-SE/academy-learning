<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class PermissionControllerTest extends TestCase
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
    public function admin_can_view_all_permissions()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/permissions');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data' => [
                        '*' => [
                            'id', 'name', 'description', 'guard_name'
                        ]
                    ]
                ],
                'message'
            ]);
    }

    /** @test */
    public function admin_can_create_permission()
    {
        $permissionData = [
            'name' => 'custom.permission',
            'description' => 'Custom permission for testing',
            'guard_name' => 'web'
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/permissions', $permissionData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id', 'name', 'description', 'guard_name'
                ],
                'message'
            ]);

        $this->assertDatabaseHas('permissions', [
            'name' => 'custom.permission',
            'description' => 'Custom permission for testing'
        ]);
    }

    /** @test */
    public function admin_can_view_specific_permission()
    {
        $permission = Permission::where('name', 'users.view')->first();

        $response = $this->actingAs($this->admin)
            ->getJson("/api/permissions/{$permission->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $permission->id,
                    'name' => 'users.view'
                ]
            ]);
    }

    /** @test */
    public function admin_can_update_permission()
    {
        $permission = Permission::where('name', 'users.view')->first();
        
        $updateData = [
            'description' => 'Updated description for viewing users'
        ];

        $response = $this->actingAs($this->admin)
            ->putJson("/api/permissions/{$permission->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'description' => 'Updated description for viewing users'
                ]
            ]);
    }

    /** @test */
    public function admin_can_delete_unused_permission()
    {
        $permission = Permission::create([
            'name' => 'test.permission',
            'description' => 'Test permission for deletion',
            'guard_name' => 'web'
        ]);

        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/permissions/{$permission->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('permissions', ['id' => $permission->id]);
    }

    /** @test */
    public function admin_cannot_delete_system_permissions()
    {
        $permission = Permission::where('name', 'users.view')->first();

        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/permissions/{$permission->id}");

        $response->assertStatus(400);
        $this->assertDatabaseHas('permissions', ['id' => $permission->id]);
    }

    /** @test */
    public function admin_cannot_delete_permission_assigned_to_roles()
    {
        $permission = Permission::where('name', 'users.view')->first();
        $role = Role::where('name', 'admin')->first();
        $role->givePermissionTo($permission);

        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/permissions/{$permission->id}");

        $response->assertStatus(400);
        $this->assertDatabaseHas('permissions', ['id' => $permission->id]);
    }

    /** @test */
    public function admin_can_view_permissions_by_module()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/permissions/by-module');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'users' => [
                        '*' => ['id', 'name', 'description']
                    ],
                    'content' => [
                        '*' => ['id', 'name', 'description']
                    ]
                ],
                'message'
            ]);
    }

    /** @test */
    public function admin_can_view_permission_statistics()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/permissions/statistics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'total_permissions',
                    'permissions_by_module',
                    'permissions_with_roles',
                    'unused_permissions'
                ],
                'message'
            ]);
    }

    /** @test */
    public function admin_can_bulk_create_permissions()
    {
        $permissionsData = [
            [
                'name' => 'bulk.permission1',
                'description' => 'First bulk permission'
            ],
            [
                'name' => 'bulk.permission2',
                'description' => 'Second bulk permission'
            ]
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/permissions/bulk-create', [
                'permissions' => $permissionsData
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'name', 'description']
                ],
                'message'
            ]);

        $this->assertDatabaseHas('permissions', [
            'name' => 'bulk.permission1'
        ]);
        $this->assertDatabaseHas('permissions', [
            'name' => 'bulk.permission2'
        ]);
    }

    /** @test */
    public function admin_can_sync_permissions_with_roles()
    {
        $role = Role::where('name', 'teacher')->first();
        $permissions = ['content.create', 'content.edit'];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/permissions/sync-with-roles', [
                'role_id' => $role->id,
                'permissions' => $permissions
            ]);

        $response->assertStatus(200);
        
        $role->refresh();
        $this->assertTrue($role->hasPermissionTo('content.create'));
        $this->assertTrue($role->hasPermissionTo('content.edit'));
    }

    /** @test */
    public function can_filter_permissions_by_module()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/permissions?module=users');

        $response->assertStatus(200);
        
        $permissions = $response->json('data.data');
        foreach ($permissions as $permission) {
            $this->assertStringStartsWith('users.', $permission['name']);
        }
    }

    /** @test */
    public function can_search_permissions()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/permissions?search=view');

        $response->assertStatus(200);
        
        $permissions = $response->json('data.data');
        $this->assertGreaterThan(0, count($permissions));
    }

    /** @test */
    public function non_admin_cannot_view_permissions()
    {
        $response = $this->actingAs($this->teacher)
            ->getJson('/api/permissions');

        $response->assertStatus(403);
    }

    /** @test */
    public function non_admin_cannot_create_permissions()
    {
        $permissionData = [
            'name' => 'unauthorized.permission',
            'description' => 'This should fail'
        ];

        $response = $this->actingAs($this->teacher)
            ->postJson('/api/permissions', $permissionData);

        $response->assertStatus(403);
    }

    /** @test */
    public function validation_fails_with_invalid_data()
    {
        $invalidData = [
            'name' => '',
            'description' => ''
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/permissions', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'description']);
    }

    /** @test */
    public function cannot_create_permission_with_duplicate_name()
    {
        $permissionData = [
            'name' => 'users.view', // Already exists
            'description' => 'Duplicate permission'
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/permissions', $permissionData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function bulk_create_requires_minimum_permissions()
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/permissions/bulk-create', [
                'permissions' => []
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['permissions']);
    }

    /** @test */
    public function bulk_create_validates_each_permission()
    {
        $permissionsData = [
            [
                'name' => 'valid.permission',
                'description' => 'Valid permission'
            ],
            [
                'name' => '', // Invalid name
                'description' => 'Invalid permission'
            ]
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/permissions/bulk-create', [
                'permissions' => $permissionsData
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['permissions.1.name']);
    }

    /** @test */
    public function sync_with_roles_requires_valid_role()
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/permissions/sync-with-roles', [
                'role_id' => 99999, // Non-existent role
                'permissions' => ['content.view']
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['role_id']);
    }

    /** @test */
    public function sync_with_roles_requires_valid_permissions()
    {
        $role = Role::where('name', 'teacher')->first();

        $response = $this->actingAs($this->admin)
            ->postJson('/api/permissions/sync-with-roles', [
                'role_id' => $role->id,
                'permissions' => ['invalid.permission']
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['permissions.0']);
    }

    /** @test */
    public function permissions_are_properly_paginated()
    {
        // Create additional permissions for pagination
        Permission::create([
            'name' => 'test.permission1',
            'description' => 'Test permission 1',
            'guard_name' => 'web'
        ]);
        Permission::create([
            'name' => 'test.permission2',
            'description' => 'Test permission 2',
            'guard_name' => 'web'
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/permissions?per_page=10');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertLessThanOrEqual(10, count($data['data']));
        $this->assertArrayHasKey('current_page', $data);
    }

    /** @test */
    public function permission_statistics_include_module_breakdown()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/permissions/statistics');

        $response->assertStatus(200);
        
        $stats = $response->json('data');
        $this->assertArrayHasKey('permissions_by_module', $stats);
        
        // Check that modules are properly counted
        foreach ($stats['permissions_by_module'] as $module => $count) {
            $this->assertIsInt($count);
            $this->assertGreaterThan(0, $count);
        }
    }

    /** @test */
    public function permissions_by_module_are_properly_grouped()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/permissions/by-module');

        $response->assertStatus(200);
        
        $modules = $response->json('data');
        
        // Check that permissions are grouped by module
        foreach ($modules as $moduleName => $permissions) {
            $this->assertIsArray($permissions);
            
            foreach ($permissions as $permission) {
                $this->assertStringStartsWith($moduleName . '.', $permission['name']);
            }
        }
    }

    /** @test */
    public function only_super_admin_can_edit_system_permissions()
    {
        $permission = Permission::where('name', 'users.view')->first();
        
        // Regular admin cannot edit system permission
        $response = $this->actingAs($this->admin)
            ->putJson("/api/permissions/{$permission->id}", [
                'description' => 'Hacked description'
            ]);

        $response->assertStatus(403);
        
        // Super admin can edit system permission
        $response = $this->actingAs($this->superAdmin)
            ->putJson("/api/permissions/{$permission->id}", [
                'description' => 'Updated by super admin'
            ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function guard_name_defaults_to_web()
    {
        $permissionData = [
            'name' => 'test.guard',
            'description' => 'Test permission without guard'
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/permissions', $permissionData);

        $response->assertStatus(201);
        
        $this->assertDatabaseHas('permissions', [
            'name' => 'test.guard',
            'guard_name' => 'web'
        ]);
    }
}
