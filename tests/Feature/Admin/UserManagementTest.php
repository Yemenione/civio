<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        // Create an admin user
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_view_users_list()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Users')
            ->has('users')
        );
    }

    public function test_admin_can_toggle_user_role()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($this->admin)->put(route('admin.users.toggleRole', $user));

        $response->assertRedirect();
        $this->assertEquals('admin', $user->fresh()->role);
    }

    public function test_admin_can_toggle_user_status()
    {
        $user = User::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->admin)->put(route('admin.users.toggleActive', $user));

        $response->assertRedirect();
        $this->assertFalse((bool)$user->fresh()->is_active);
    }

    public function test_admin_can_impersonate_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)->post(route('admin.users.impersonate', $user));

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->assertTrue(session()->has('impersonated_by'));
    }

    public function test_non_admin_cannot_access_user_management()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get(route('admin.users.index'));

        // Assuming 'admin' middleware redirects or aborts 403
        // Breeze/Standard usually redirects to home or login, or 403
        // We'll check for 403 or redirect
        if ($response->status() === 403) {
            $response->assertStatus(403);
        } else {
            $response->assertRedirect();
        }
    }
}
