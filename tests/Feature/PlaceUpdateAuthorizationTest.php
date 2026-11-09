<?php

namespace Tests\Feature;

use App\Models\Place;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PlaceUpdateAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_edit_their_place_and_resubmit_a_requested_change(): void
    {
        [$owner, , $place] = $this->createPlaceAndUsers('à modifier');

        $response = $this->actingAs($owner, 'sanctum')->putJson("/api/places/{$place->id}", [
            'name' => 'Un lieu modifié avec soin',
            'description' => 'Une description suffisamment longue pour être acceptée.',
            'latitude' => 46.5,
            'longitude' => -0.7,
            'category_id' => $place->category_id,
            'time' => 2,
            'difficulty' => 'famille',
            'kilometres' => null,
            'department_id' => $place->department_id,
            'adress' => '15 route du test',
            'postcode' => '85000',
            'city' => 'Mervent',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.status', 'en attente')
            ->assertJsonPath('data.comment', null);

        $this->assertDatabaseHas('places', [
            'id' => $place->id,
            'name' => 'Un lieu modifié avec soin',
            'status' => 'en attente',
            'comment' => null,
        ]);
    }

    public function test_user_cannot_edit_another_users_place(): void
    {
        [, $otherUser, $place] = $this->createPlaceAndUsers();

        $this->actingAs($otherUser, 'sanctum')
            ->putJson("/api/places/{$place->id}", ['name' => 'Un nom de lieu interdit'])
            ->assertForbidden();

        $this->assertDatabaseHas('places', [
            'id' => $place->id,
            'name' => $place->name,
        ]);
    }

    private function createPlaceAndUsers(string $status = 'validé'): array
    {
        $userRoleId = DB::table('roles')->insertGetId([
            'name' => 'user',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('roles')->insert([
            'name' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
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

        $owner = $this->createUser('owner', $userRoleId, $departmentId);
        $otherUser = $this->createUser('other', $userRoleId, $departmentId);
        $place = Place::forceCreate([
            'name' => 'Lieu de test partagé',
            'description' => 'Une description de lieu suffisamment longue.',
            'latitude' => 46.5,
            'longitude' => -0.7,
            'note' => 5,
            'time' => 2,
            'difficulty' => 'famille',
            'adress' => '15 route du test',
            'postcode' => '85000',
            'city' => 'Mervent',
            'status' => $status,
            'user_id' => $owner->id,
            'category_id' => $categoryId,
            'department_id' => $departmentId,
            'comment' => $status === 'à modifier' ? 'Merci de préciser ce lieu.' : null,
        ]);

        return [$owner, $otherUser, $place];
    }

    private function createUser(string $name, int $roleId, int $departmentId): User
    {
        return User::forceCreate([
            'name' => $name,
            'email' => "{$name}@example.test",
            'password' => 'password',
            'role_id' => $roleId,
            'department_id' => $departmentId,
        ]);
    }
}
