<?php

namespace Modules\Admin\Tests\Feature;

use Tests\TestCase;
use Modules\Admin\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminAuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $adminData;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin permissions and roles if they don't exist
        $this->createAdminPermissions();
        
        // Create test admin data
        $this->adminData = [
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'username' => 'testadmin',
            'password' => Hash::make('password123'),
            'status' => 'active'
        ];
        
        // Create admin user
        $this->admin = Admin::create($this->adminData);
        $this->admin->assignRole('admin');
    }

    protected function createAdminPermissions()
    {
        // Create admin role if it doesn't exist
        if (!Role::where('name', 'admin')->exists()) {
            Role::create(['name' => 'admin']);
        }
        
        if (!Role::where('name', 'super_admin')->exists()) {
            Role::create(['name' => 'super_admin']);
        }
    }

    /** @test */
    public function admin_can_login_with_valid_credentials()
    {
        $credentials = [
            'email' => 'admin@test.com',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/v1/admin/auth/login', $credentials);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'admin' => [
                        'id', 'name', 'email', 'username', 'avatar', 'status'
                    ],
                    'token',
                    'token_type',
                    'expires_in'
                ],
                'message'
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Login successful'
            ]);
    }

    /** @test */
    public function admin_login_fails_with_invalid_credentials()
    {
        $credentials = [
            'email' => 'admin@test.com',
            'password' => 'wrongpassword'
        ];

        $response = $this->postJson('/api/v1/admin/auth/login', $credentials);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid credentials'
            ]);
    }

    /** @test */
    public function admin_login_fails_with_nonexistent_email()
    {
        $credentials = [
            'email' => 'nonexistent@test.com',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/v1/admin/auth/login', $credentials);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid credentials or account is inactive'
            ]);
    }

    /** @test */
    public function admin_login_fails_with_inactive_account()
    {
        // Create inactive admin
        $inactiveAdmin = Admin::create([
            'name' => 'Inactive Admin',
            'email' => 'inactive@test.com',
            'username' => 'inactiveadmin',
            'password' => Hash::make('password123'),
            'status' => 'inactive'
        ]);

        $credentials = [
            'email' => 'inactive@test.com',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/v1/admin/auth/login', $credentials);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid credentials or account is inactive'
            ]);
    }

    /** @test */
    public function admin_can_access_profile_with_valid_token()
    {
        // Login to get token
        $loginResponse = $this->postJson('/api/v1/admin/auth/login', [
            'email' => 'admin@test.com',
            'password' => 'password123'
        ]);

        $token = $loginResponse->json('data.token');

        // Access profile with token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/v1/admin/auth/profile');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'admin' => [
                        'id', 'name', 'email', 'username', 'avatar', 'status',
                        'roles', 'permissions'
                    ]
                ]
            ])
            ->assertJson([
                'success' => true
            ]);
    }

    /** @test */
    public function admin_profile_access_fails_without_token()
    {
        $response = $this->getJson('/api/v1/admin/auth/profile');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
    }

    /** @test */
    public function admin_profile_access_fails_with_invalid_token()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid_token'
        ])->getJson('/api/v1/admin/auth/profile');

        $response->assertStatus(401);
    }

    /** @test */
    public function admin_can_update_profile()
    {
        // Login to get token
        $loginResponse = $this->postJson('/api/v1/admin/auth/login', [
            'email' => 'admin@test.com',
            'password' => 'password123'
        ]);

        $token = $loginResponse->json('data.token');

        $updateData = [
            'name' => 'Updated Admin Name',
            'email' => 'updated@test.com',
            'username' => 'updatedadmin'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->putJson('/api/v1/admin/auth/profile', $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'admin' => [
                        'id', 'name', 'email', 'username', 'avatar', 'status',
                        'roles', 'permissions'
                    ]
                ],
                'message'
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Profile updated successfully'
            ]);

        // Verify database was updated
        $this->assertDatabaseHas('admins', [
            'id' => $this->admin->id,
            'name' => 'Updated Admin Name',
            'email' => 'updated@test.com',
            'username' => 'updatedadmin'
        ]);
    }

    /** @test */
    public function admin_can_update_password()
    {
        // Login to get token
        $loginResponse = $this->postJson('/api/v1/admin/auth/login', [
            'email' => 'admin@test.com',
            'password' => 'password123'
        ]);

        $token = $loginResponse->json('data.token');

        $updateData = [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->putJson('/api/v1/admin/auth/profile', $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Profile updated successfully'
            ]);

        // Verify password was updated
        $this->admin->refresh();
        $this->assertTrue(Hash::check('newpassword123', $this->admin->password));
    }

    /** @test */
    public function admin_can_logout()
    {
        // Login to get token
        $loginResponse = $this->postJson('/api/v1/admin/auth/login', [
            'email' => 'admin@test.com',
            'password' => 'password123'
        ]);

        $token = $loginResponse->json('data.token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/v1/admin/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Logout successful'
            ]);

        // Verify token is invalidated
        $profileResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/v1/admin/auth/profile');

        $profileResponse->assertStatus(401);
    }

    /** @test */
    public function admin_can_refresh_token()
    {
        // Login to get token
        $loginResponse = $this->postJson('/api/v1/admin/auth/login', [
            'email' => 'admin@test.com',
            'password' => 'password123'
        ]);

        $token = $loginResponse->json('data.token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/v1/admin/auth/refresh');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'admin' => [
                        'id', 'name', 'email', 'username', 'avatar', 'status'
                    ],
                    'token',
                    'token_type',
                    'expires_in'
                ],
                'message'
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Token refreshed successfully'
            ]);

        // Verify new token works
        $newToken = $response->json('data.token');
        $profileResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $newToken
        ])->getJson('/api/v1/admin/auth/profile');

        $profileResponse->assertStatus(200);
    }

    /** @test */
    public function admin_can_upload_avatar()
    {
        // Login to get token
        $loginResponse = $this->postJson('/api/v1/admin/auth/login', [
            'email' => 'admin@test.com',
            'password' => 'password123'
        ]);

        $token = $loginResponse->json('data.token');

        // Create a fake image file
        $file = \Illuminate\Http\UploadedFile::fake()->image('avatar.jpg', 200, 200);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/v1/admin/auth/avatar', [
            'avatar' => $file
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'admin' => [
                        'id', 'name', 'email', 'username', 'avatar', 'status'
                    ]
                ],
                'message'
            ])
            ->assertJson([
                'success' => true
            ]);
    }

    /** @test */
    public function admin_can_delete_avatar()
    {
        // Login to get token
        $loginResponse = $this->postJson('/api/v1/admin/auth/login', [
            'email' => 'admin@test.com',
            'password' => 'password123'
        ]);

        $token = $loginResponse->json('data.token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->deleteJson('/api/v1/admin/auth/avatar');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'admin' => [
                        'id', 'name', 'email', 'username', 'avatar', 'status'
                    ]
                ],
                'message'
            ])
            ->assertJson([
                'success' => true
            ]);
    }

    /** @test */
    public function health_check_endpoint_returns_success()
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'timestamp',
                'version'
            ])
            ->assertJson([
                'status' => 'healthy',
                'version' => '1.0.0'
            ]);
    }

    /** @test */
    public function v2_health_check_endpoint_returns_success()
    {
        $response = $this->getJson('/api/v2/health');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'timestamp',
                'version'
            ])
            ->assertJson([
                'status' => 'healthy',
                'version' => '2.0.0'
            ]);
    }

    /** @test */
    public function nonexistent_endpoint_returns_404()
    {
        $response = $this->getJson('/api/v1/admin/nonexistent');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'API endpoint not found',
                'code' => 404
            ]);
    }
}
