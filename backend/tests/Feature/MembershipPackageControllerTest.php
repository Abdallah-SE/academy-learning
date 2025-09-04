<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\MembershipPackage;
use App\Models\UserMembership;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class MembershipPackageControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $financialManager;
    protected $student;

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
    }

    /** @test */
    public function anyone_can_view_packages()
    {
        MembershipPackage::factory()->count(3)->create();

        $response = $this->getJson('/api/packages');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data' => [
                        '*' => [
                            'id', 'name', 'description', 'price', 'duration_days', 'features'
                        ]
                    ]
                ],
                'message'
            ]);
    }

    /** @test */
    public function anyone_can_view_specific_package()
    {
        $package = MembershipPackage::factory()->create();

        $response = $this->getJson("/api/packages/{$package->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $package->id,
                    'name' => $package->name
                ]
            ]);
    }

    /** @test */
    public function admin_can_create_package()
    {
        $packageData = [
            'name' => 'Premium Package',
            'description' => 'Best package for serious students',
            'price' => 99.99,
            'duration_days' => 365,
            'features' => [
                'quran_access' => true,
                'arabic_lessons' => true,
                'islamic_studies' => true,
                'homework_support' => true,
                'meeting_access' => true,
                'progress_tracking' => true
            ]
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/packages', $packageData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id', 'name', 'description', 'price', 'duration_days', 'features'
                ],
                'message'
            ]);

        $this->assertDatabaseHas('membership_packages', [
            'name' => 'Premium Package',
            'price' => 99.99
        ]);
    }

    /** @test */
    public function admin_can_update_package()
    {
        $package = MembershipPackage::factory()->create();
        
        $updateData = [
            'name' => 'Updated Package Name',
            'price' => 149.99
        ];

        $response = $this->actingAs($this->admin)
            ->putJson("/api/packages/{$package->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'Updated Package Name',
                    'price' => 149.99
                ]
            ]);
    }

    /** @test */
    public function admin_can_delete_package_without_active_memberships()
    {
        $package = MembershipPackage::factory()->create();

        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/packages/{$package->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('membership_packages', ['id' => $package->id]);
    }

    /** @test */
    public function admin_cannot_delete_package_with_active_memberships()
    {
        $package = MembershipPackage::factory()->create();
        
        // Create active membership
        UserMembership::factory()->create([
            'package_id' => $package->id,
            'status' => 'active'
        ]);

        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/packages/{$package->id}");

        $response->assertStatus(400);
        $this->assertDatabaseHas('membership_packages', ['id' => $package->id]);
    }

    /** @test */
    public function anyone_can_view_public_packages()
    {
        MembershipPackage::factory()->create(['status' => 'active']);
        MembershipPackage::factory()->create(['status' => 'inactive']);

        $response = $this->getJson('/api/packages/public');

        $response->assertStatus(200);
        
        $packages = $response->json('data');
        foreach ($packages as $package) {
            $this->assertEquals('active', $package['status']);
        }
    }

    /** @test */
    public function anyone_can_compare_packages()
    {
        $package1 = MembershipPackage::factory()->create();
        $package2 = MembershipPackage::factory()->create();

        $response = $this->postJson('/api/packages/compare', [
            'package_ids' => [$package1->id, $package2->id]
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'name', 'price', 'features']
                ],
                'message'
            ]);
    }

    /** @test */
    public function admin_can_view_package_statistics()
    {
        MembershipPackage::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)
            ->getJson('/api/packages/statistics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'total_packages',
                    'active_packages',
                    'inactive_packages',
                    'average_price',
                    'price_range'
                ],
                'message'
            ]);
    }

    /** @test */
    public function admin_can_duplicate_package()
    {
        $package = MembershipPackage::factory()->create();

        $response = $this->actingAs($this->admin)
            ->postJson("/api/packages/{$package->id}/duplicate");

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id', 'name', 'description', 'price', 'features'
                ],
                'message'
            ]);

        $this->assertDatabaseHas('membership_packages', [
            'name' => $package->name . ' (Copy)',
            'status' => 'inactive'
        ]);
    }

    /** @test */
    public function can_filter_packages_by_status()
    {
        MembershipPackage::factory()->create(['status' => 'active']);
        MembershipPackage::factory()->create(['status' => 'inactive']);

        $response = $this->getJson('/api/packages?status=active');

        $response->assertStatus(200);
        
        $packages = $response->json('data.data');
        foreach ($packages as $package) {
            $this->assertEquals('active', $package['status']);
        }
    }

    /** @test */
    public function can_filter_packages_by_price_range()
    {
        MembershipPackage::factory()->create(['price' => 50]);
        MembershipPackage::factory()->create(['price' => 100]);
        MembershipPackage::factory()->create(['price' => 200]);

        $response = $this->getJson('/api/packages?min_price=75&max_price=150');

        $response->assertStatus(200);
        
        $packages = $response->json('data.data');
        foreach ($packages as $package) {
            $this->assertGreaterThanOrEqual(75, $package['price']);
            $this->assertLessThanOrEqual(150, $package['price']);
        }
    }

    /** @test */
    public function can_search_packages()
    {
        $package = MembershipPackage::factory()->create(['name' => 'Special Package']);

        $response = $this->getJson('/api/packages?search=Special');

        $response->assertStatus(200);
        
        $packages = $response->json('data.data');
        $this->assertGreaterThan(0, count($packages));
    }

    /** @test */
    public function non_admin_cannot_create_package()
    {
        $packageData = [
            'name' => 'Unauthorized Package',
            'description' => 'This should fail',
            'price' => 50,
            'duration_days' => 30,
            'features' => ['quran_access' => true]
        ];

        $response = $this->actingAs($this->student)
            ->postJson('/api/packages', $packageData);

        $response->assertStatus(403);
    }

    /** @test */
    public function non_admin_cannot_update_package()
    {
        $package = MembershipPackage::factory()->create();
        
        $response = $this->actingAs($this->student)
            ->putJson("/api/packages/{$package->id}", ['name' => 'Hacked']);

        $response->assertStatus(403);
    }

    /** @test */
    public function non_admin_cannot_delete_package()
    {
        $package = MembershipPackage::factory()->create();

        $response = $this->actingAs($this->student)
            ->deleteJson("/api/packages/{$package->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function validation_fails_with_invalid_data()
    {
        $invalidData = [
            'name' => '',
            'price' => 'invalid-price',
            'duration_days' => -1,
            'features' => 'not-array'
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/packages', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'price', 'duration_days', 'features']);
    }

    /** @test */
    public function package_features_are_properly_validated()
    {
        $packageData = [
            'name' => 'Test Package',
            'description' => 'Test Description',
            'price' => 50,
            'duration_days' => 30,
            'features' => [
                'quran_access' => 'not-boolean',
                'invalid_feature' => true
            ]
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/packages', $packageData);

        $response->assertStatus(422);
    }

    /** @test */
    public function packages_are_ordered_by_price_asc_in_public_view()
    {
        MembershipPackage::factory()->create(['price' => 100, 'status' => 'active']);
        MembershipPackage::factory()->create(['price' => 50, 'status' => 'active']);
        MembershipPackage::factory()->create(['price' => 75, 'status' => 'active']);

        $response = $this->getJson('/api/packages/public');

        $response->assertStatus(200);
        
        $packages = $response->json('data');
        $this->assertEquals(50, $packages[0]['price']);
        $this->assertEquals(75, $packages[1]['price']);
        $this->assertEquals(100, $packages[2]['price']);
    }

    /** @test */
    public function package_comparison_requires_minimum_packages()
    {
        $response = $this->postJson('/api/packages/compare', [
            'package_ids' => [1]
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function package_comparison_limits_maximum_packages()
    {
        $packages = MembershipPackage::factory()->count(5)->create();
        $packageIds = $packages->pluck('id')->toArray();

        $response = $this->postJson('/api/packages/compare', [
            'package_ids' => $packageIds
        ]);

        $response->assertStatus(422);
    }
}
