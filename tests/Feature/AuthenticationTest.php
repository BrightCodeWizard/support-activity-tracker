<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/tracker/daily')->assertRedirect('/login');
        $this->get('/handover')->assertRedirect('/login');
        $this->get('/reports')->assertRedirect('/login');
    }

    public function test_user_can_register_and_is_authenticated(): void
    {
        $this->post('/register', [
            'name' => 'New Member',
            'email' => 'new@example.com',
            'staff_id' => 'STF-9999',
            'department' => 'Applications Support',
            'phone' => '+233-20-000-0000',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'new@example.com',
            'role' => 'member',
        ]);
    }

    public function test_user_can_login_and_logout(): void
    {
        $user = User::factory()->create(['password' => 'password']);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);

        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_login_with_invalid_credentials_is_rejected(): void
    {
        $this->post('/login', [
            'email' => 'nobody@example.com',
            'password' => 'wrong',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_member_cannot_access_activity_management(): void
    {
        $member = User::factory()->create(['role' => 'member']);

        $this->actingAs($member)
            ->get(route('activities.index'))
            ->assertForbidden();
    }

    public function test_admin_can_access_activity_management(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('activities.index'))
            ->assertOk()
            ->assertSee('Manage Activities');
    }
}
