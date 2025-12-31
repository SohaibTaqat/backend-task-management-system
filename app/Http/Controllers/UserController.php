<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group User Management
 *
 * APIs for managing users (Admin only)
 */
class UserController extends Controller
{
    use ApiResponse;

    /**
     * List all users
     *
     * Returns a paginated list of all users. Admin only.
     *
     * @authenticated
     *
     * @queryParam page integer Page number. Example: 1
     * @queryParam per_page integer Items per page (default: 15). Example: 15
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Users retrieved successfully",
     *   "data": {
     *     "data": [
     *       {
     *         "id": 1,
     *         "name": "Admin User",
     *         "email": "admin@example.com",
     *         "role": "admin",
     *         "created_at": "2024-01-01T00:00:00.000000Z",
     *         "updated_at": "2024-01-01T00:00:00.000000Z"
     *       }
     *     ],
     *     "links": {...},
     *     "meta": {...}
     *   }
     * }
     * @response 403 {
     *   "success": false,
     *   "message": "Unauthorized. Admin access required."
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $users = User::latest()->paginate($perPage);

        return $this->successResponse(
            UserResource::collection($users)->response()->getData(true),
            'Users retrieved successfully'
        );
    }

    /**
     * Get a single user
     *
     * Returns the details of a specific user. Admin only.
     *
     * @authenticated
     *
     * @urlParam user integer required The user ID. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "message": "User retrieved successfully",
     *   "data": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "john@example.com",
     *     "role": "member",
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     * @response 403 {
     *   "success": false,
     *   "message": "Unauthorized. Admin access required."
     * }
     */
    public function show(User $user): JsonResponse
    {
        return $this->successResponse(
            new UserResource($user),
            'User retrieved successfully'
        );
    }

    /**
     * Delete a user
     *
     * Deletes a user and all their associated tasks. Admin only.
     *
     * @authenticated
     *
     * @urlParam user integer required The user ID. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "message": "User deleted successfully",
     *   "data": null
     * }
     * @response 403 {
     *   "success": false,
     *   "message": "Unauthorized. Admin access required."
     * }
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return $this->successResponse(null, 'User deleted successfully');
    }
}
