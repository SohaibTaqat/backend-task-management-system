<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_users(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->count(5)->create();

        $response = $this->actingAs($admin)->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'data' => [
                        '*' => ['id', 'name', 'email', 'role'],
                    ],
                ],
            ]);
    }

    public function test_member_cannot_list_users(): void
    {
        $member = User::factory()->member()->create();

        $response = $this->actingAs($member)->getJson('/api/users');

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.',
            ]);
    }

    public function test_unauthenticated_user_cannot_list_users(): void
    {
        $response = $this->getJson('/api/users');

        $response->assertStatus(401);
    }

    public function test_admin_can_view_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($admin)->getJson("/api/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'email' => $user->email,
                ],
            ]);
    }

    public function test_member_cannot_view_user(): void
    {
        $member = User::factory()->member()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($member)->getJson("/api/users/{$user->id}");

        $response->assertStatus(403);
    }

    public function test_admin_can_delete_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($admin)->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'User deleted successfully',
            ]);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_member_cannot_delete_user(): void
    {
        $member = User::factory()->member()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($member)->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_deleting_user_also_deletes_their_tasks(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        $tasks = Task::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($admin)->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        foreach ($tasks as $task) {
            $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
        }
    }

    public function test_users_are_paginated(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->count(20)->create();

        $response = $this->actingAs($admin)->getJson('/api/users?per_page=5');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data.data')
            ->assertJsonStructure([
                'data' => [
                    'data',
                    'links',
                    'meta',
                ],
            ]);
    }
}
