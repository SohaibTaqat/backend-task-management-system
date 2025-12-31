<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Task Management
 *
 * APIs for managing tasks
 */
class TaskController extends Controller
{
    use ApiResponse;

    /**
     * List all tasks
     *
     * Returns a paginated list of all tasks in the system.
     *
     * @authenticated
     *
     * @queryParam page integer Page number. Example: 1
     * @queryParam per_page integer Items per page (default: 15). Example: 15
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Tasks retrieved successfully",
     *   "data": {
     *     "data": [
     *       {
     *         "id": 1,
     *         "title": "Complete project",
     *         "description": "Finish the task management API",
     *         "status": "in_progress",
     *         "due_date": "2024-12-31",
     *         "is_overdue": false,
     *         "user": {...},
     *         "created_at": "2024-01-01T00:00:00.000000Z",
     *         "updated_at": "2024-01-01T00:00:00.000000Z"
     *       }
     *     ],
     *     "links": {...},
     *     "meta": {...}
     *   }
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $tasks = Task::with('user')->latest()->paginate($perPage);

        return $this->successResponse(
            TaskResource::collection($tasks)->response()->getData(true),
            'Tasks retrieved successfully'
        );
    }

    /**
     * Get a single task
     *
     * Returns the details of a specific task.
     *
     * @authenticated
     *
     * @urlParam task integer required The task ID. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Task retrieved successfully",
     *   "data": {
     *     "id": 1,
     *     "title": "Complete project",
     *     "description": "Finish the task management API",
     *     "status": "in_progress",
     *     "due_date": "2024-12-31",
     *     "is_overdue": false,
     *     "user": {...},
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     */
    public function show(Task $task): JsonResponse
    {
        $task->load('user');

        return $this->successResponse(
            new TaskResource($task),
            'Task retrieved successfully'
        );
    }

    /**
     * Create a new task
     *
     * Creates a new task assigned to the authenticated user.
     *
     * @authenticated
     *
     * @bodyParam title string required The task title. Example: Complete project documentation
     * @bodyParam description string The task description. Example: Write comprehensive API documentation
     * @bodyParam status string required The task status (pending, in_progress, completed). Example: pending
     * @bodyParam due_date date The task due date (Y-m-d format). Example: 2024-12-31
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Task created successfully",
     *   "data": {
     *     "id": 1,
     *     "title": "Complete project documentation",
     *     "description": "Write comprehensive API documentation",
     *     "status": "pending",
     *     "due_date": "2024-12-31",
     *     "is_overdue": false,
     *     "user": {...},
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = Task::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'due_date' => $request->due_date,
        ]);

        $task->load('user');

        return $this->createdResponse(
            new TaskResource($task),
            'Task created successfully'
        );
    }

    /**
     * Update a task
     *
     * Updates an existing task. Only the task owner or admin can update.
     *
     * @authenticated
     *
     * @urlParam task integer required The task ID. Example: 1
     * @bodyParam title string The task title. Example: Updated task title
     * @bodyParam description string The task description. Example: Updated description
     * @bodyParam status string The task status (pending, in_progress, completed). Example: in_progress
     * @bodyParam due_date date The task due date (Y-m-d format). Example: 2024-12-31
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Task updated successfully",
     *   "data": {...}
     * }
     * @response 403 {
     *   "success": false,
     *   "message": "Unauthorized"
     * }
     */
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $task->update($request->validated());
        $task->load('user');

        return $this->successResponse(
            new TaskResource($task),
            'Task updated successfully'
        );
    }

    /**
     * Delete a task
     *
     * Deletes a task. Only the task owner or admin can delete.
     *
     * @authenticated
     *
     * @urlParam task integer required The task ID. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Task deleted successfully",
     *   "data": null
     * }
     * @response 403 {
     *   "success": false,
     *   "message": "Unauthorized"
     * }
     */
    public function destroy(Request $request, Task $task): JsonResponse
    {
        if ($request->user()->cannot('delete', $task)) {
            return $this->forbiddenResponse('Unauthorized');
        }

        $task->delete();

        return $this->successResponse(null, 'Task deleted successfully');
    }

    /**
     * Update task status
     *
     * Updates only the status field of a task. Only the task owner or admin can update.
     *
     * @authenticated
     *
     * @urlParam task integer required The task ID. Example: 1
     * @bodyParam status string required The new status (pending, in_progress, completed). Example: completed
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Task status updated successfully",
     *   "data": {...}
     * }
     * @response 403 {
     *   "success": false,
     *   "message": "Unauthorized"
     * }
     */
    public function updateStatus(UpdateTaskStatusRequest $request, Task $task): JsonResponse
    {
        $task->update(['status' => $request->status]);
        $task->load('user');

        return $this->successResponse(
            new TaskResource($task),
            'Task status updated successfully'
        );
    }
}
