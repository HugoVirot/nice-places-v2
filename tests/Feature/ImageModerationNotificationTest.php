<?php

namespace Tests\Feature;

use App\Models\Image;
use App\Models\Notification;
use App\Models\Place;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ImageModerationNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_accepting_an_image_notifies_its_uploader(): void
    {
        [$admin, $uploader, $image] = $this->createAdminUploaderAndImage();

        $this->actingAs($admin, 'sanctum')
            ->putJson("/api/images/{$image->id}", ['status' => 'validée'])
            ->assertCreated()
            ->assertJsonPath('data.status', 'validée');

        $notification = Notification::where('user_id', $uploader->id)->firstOrFail();
        $this->assertSame($image->place->name, $notification->place->name);
        $this->assertStringContainsString('Bonjour Uploader,', $notification->message);
        $this->assertStringContainsString('a été acceptée', $notification->message);
        $this->assertStringContainsString('Cordialement,<br>L’administration.', $notification->message);
    }

    public function test_refusing_an_image_notifies_its_uploader(): void
    {
        [$admin, $uploader, $image] = $this->createAdminUploaderAndImage();

        $this->actingAs($admin, 'sanctum')
            ->putJson("/api/images/{$image->id}", ['status' => 'refusée'])
            ->assertCreated()
            ->assertJsonPath('data.status', 'refusée');

        $notification = Notification::where('user_id', $uploader->id)->firstOrFail();
        $this->assertStringContainsString('n’a pas été acceptée', $notification->message);
        $this->assertStringContainsString('Cordialement,<br>L’administration.', $notification->message);
    }

    public function test_saving_an_image_without_changing_its_moderation_status_sends_no_notification(): void
    {
        [$admin, , $image] = $this->createAdminUploaderAndImage();

        $this->actingAs($admin, 'sanctum')
            ->putJson("/api/images/{$image->id}", ['cover' => true])
            ->assertCreated();

        $this->assertDatabaseCount('notifications', 0);
    }

    private function createAdminUploaderAndImage(): array
    {
        DB::table('roles')->insert([
            ['name' => 'user', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'admin', 'created_at' => now(), 'updated_at' => now()],
        ]);
        $regionId = DB::table('regions')->insertGetId([
            'name' => 'Région de test',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $departmentId = DB::table('departments')->insertGetId([
            'name' => 'Département de test',
            'code' => '085',
            'region_id' => $regionId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $categoryId = DB::table('categories')->insertGetId([
            'name' => 'Catégorie test',
            'icon' => 'fa-solid fa-mountain',
            'color' => '#000000',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $uploader = $this->createUser('Uploader', 'uploader@example.test', 1, $departmentId);
        $admin = $this->createUser('Admin', 'admin@example.test', 2, $departmentId);
        $place = Place::forceCreate([
            'name' => 'Lieu de test partagé',
            'description' => 'Une description suffisamment longue pour le lieu de test.',
            'latitude' => 46.5,
            'longitude' => -0.7,
            'note' => 5,
            'time' => 2,
            'difficulty' => 'famille',
            'adress' => '15 route du test',
            'postcode' => '85000',
            'city' => 'Mervent',
            'status' => 'validé',
            'user_id' => $uploader->id,
            'category_id' => $categoryId,
            'department_id' => $departmentId,
        ]);
        $image = Image::create([
            'name' => 'image-test.jpg',
            'status' => 'en attente',
            'user_id' => $uploader->id,
            'place_id' => $place->id,
        ]);

        return [$admin, $uploader, $image];
    }

    private function createUser(string $name, string $email, int $roleId, int $departmentId): User
    {
        return User::forceCreate([
            'name' => $name,
            'email' => $email,
            'password' => 'password',
            'role_id' => $roleId,
            'department_id' => $departmentId,
        ]);
    }
}
