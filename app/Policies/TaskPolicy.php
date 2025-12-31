<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view the task list
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        // All authenticated users can view any task
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // All authenticated users can create tasks
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        // Admin can update any task, members can only update their own
        return $user->isAdmin() || $user->id === $task->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        // Admin can delete any task, members can only delete their own
        return $user->isAdmin() || $user->id === $task->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return $user->isAdmin();
    }
}
