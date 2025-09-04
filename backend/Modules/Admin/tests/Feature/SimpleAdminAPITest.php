<?php

namespace Modules\Admin\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SimpleAdminAPITest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function health_check_endpoint_is_working()
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'healthy'
            ]);
    }

    /** @test */
    public function admin_auth_endpoints_are_accessible()
    {
        // Test login endpoint exists (should return validation error, not 404)
        $response = $this->postJson('/api/v1/admin/auth/login', []);
        
        // Should return validation error, not 404 (which means endpoint exists)
        $response->assertStatus(422);
    }

    /** @test */
    public function admin_profile_endpoint_requires_authentication()
    {
        $response = $this->getJson('/api/v1/admin/auth/profile');

        // Should return 401 (unauthorized), not 404 (which means endpoint exists)
        $response->assertStatus(401);
    }

    /** @test */
    public function v2_health_check_endpoint_is_working()
    {
        $response = $this->getJson('/api/v2/health');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'healthy',
                'version' => '2.0.0'
            ]);
    }

    /** @test */
    public function nonexistent_endpoint_returns_404()
    {
        $response = $this->getJson('/api/v1/admin/nonexistent');

        $response->assertStatus(404);
    }
}
