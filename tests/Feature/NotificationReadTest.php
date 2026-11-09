<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class NotificationReadTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_mark_notification_as_read(): void
    {
        [$owner, , $notification] = $this->createUsersAndNotification();

        $this->actingAs($owner, 'sanctum')
            ->putJson("/api/notifications/{$notification->id}")
            ->assertOk()
            ->assertJsonPath('data.read', true);

        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'read' => true,
        ]);
    }

    public function test_user_cannot_mark_another_users_notification_as_read(): void
    {
        [, $otherUser, $notification] = $this->createUsersAndNotification();

        $this->actingAs($otherUser, 'sanctum')
            ->putJson("/api/notifications/{$notification->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'read' => false,
        ]);
    }

    private function createUsersAndNotification(): array
    {
        $roleId = DB::table('roles')->insertGetId([
            'name' => 'user',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $owner = $this->createUser('notification-owner', $roleId);
        $otherUser = $this->createUser('notification-other', $roleId);
        $notification = Notification::create([
            'title' => 'Notification de test',
            'message' => 'Bonjour, message. Cordialement, l’administrateur.',
            'user_id' => $owner->id,
        ]);

        return [$owner, $otherUser, $notification];
    }

    private function createUser(string $name, int $roleId): User
    {
        return User::forceCreate([
            'name' => $name,
            'email' => "{$name}@example.test",
            'password' => 'password',
            'role_id' => $roleId,
        ]);
    }
}
