<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AuthenticationSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_rejected_by_the_session_validation_endpoint(): void
    {
        $this->getJson('/api/user')->assertUnauthorized();
    }

    public function test_authenticated_user_can_validate_and_end_their_session(): void
    {
        $roleId = DB::table('roles')->insertGetId([
            'name' => 'user',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $user = User::forceCreate([
            'name' => 'session-user',
            'email' => 'session-user@example.test',
            'password' => 'password',
            'role_id' => $roleId,
        ]);

        $this->actingAs($user, 'web')
            ->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('name', 'session-user')
            ->assertJsonPath('role.name', 'user');

        $this->postJson('/api/logout')->assertOk();
        $this->app['auth']->forgetGuards();
        $this->getJson('/api/user')->assertUnauthorized();
    }

    public function test_browser_session_expires_after_two_hours_of_inactivity(): void
    {
        $this->assertSame(120, (int) config('session.lifetime'));
    }
}
