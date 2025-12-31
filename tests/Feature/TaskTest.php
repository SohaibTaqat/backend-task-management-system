<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_list_tasks(): void
    {
        $user = User::factory()->create();
        Task::factory()->count(3)->create();

        $response = $this->actingAs($user)->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'data' => [
                        '*' => ['id', 'title', 'description', 'status', 'due_date'],
                    ],
                ],
            ]);
    }

    public function test_unauthenticated_user_cannot_list_tasks(): void
    {
        $response = $this->getJson('/api/tasks');

        $response->assertStatus(401);
    }

    public function test_user_can_create_task(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/tasks', [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'pending',
            'due_date' => now()->addDays(7)->format('Y-m-d'),
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Task created successfully',
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'user_id' => $user->id,
        ]);
    }

    public function test_user_cannot_create_task_without_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/tasks', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'status']);
    }

    public function test_user_cannot_create_task_with_invalid_status(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/tasks', [
            'title' => 'Test Task',
            'status' => 'invalid_status',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_user_cannot_create_task_with_past_due_date(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/tasks', [
            'title' => 'Test Task',
            'status' => 'pending',
            'due_date' => now()->subDays(1)->format('Y-m-d'),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['due_date']);
    }

    public function test_user_can_view_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();

        $response = $this->actingAs($user)->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $task->id,
                    'title' => $task->title,
                ],
            ]);
    }

    public function test_owner_can_update_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->putJson("/api/tasks/{$task->id}", [
            'title' => 'Updated Title',
            'status' => 'in_progress',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Task updated successfully',
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title',
            'status' => 'in_progress',
        ]);
    }

    public function test_non_owner_cannot_update_task(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->member()->create();
        $task = Task::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($otherUser)->putJson("/api/tasks/{$task->id}", [
            'title' => 'Hacked Title',
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_any_task(): void
    {
        $admin = User::factory()->admin()->create();
        $task = Task::factory()->create();

        $response = $this->actingAs($admin)->putJson("/api/tasks/{$task->id}", [
            'title' => 'Admin Updated',
            'status' => 'completed',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Admin Updated',
        ]);
    }

    public function test_owner_can_delete_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Task deleted successfully',
            ]);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_non_owner_cannot_delete_task(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->member()->create();
        $task = Task::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($otherUser)->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    public function test_admin_can_delete_any_task(): void
    {
        $admin = User::factory()->admin()->create();
        $task = Task::factory()->create();

        $response = $this->actingAs($admin)->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_user_can_update_task_status(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->pending()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->patchJson("/api/tasks/{$task->id}/status", [
            'status' => 'completed',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Task status updated successfully',
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'completed',
        ]);
    }

    public function test_non_owner_cannot_update_task_status(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->member()->create();
        $task = Task::factory()->pending()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($otherUser)->patchJson("/api/tasks/{$task->id}/status", [
            'status' => 'completed',
        ]);

        $response->assertStatus(403);
    }

    public function test_tasks_are_paginated(): void
    {
        $user = User::factory()->create();
        Task::factory()->count(20)->create();

        $response = $this->actingAs($user)->getJson('/api/tasks?per_page=5');

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
