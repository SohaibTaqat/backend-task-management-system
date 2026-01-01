# Task Management System - Tester Guide

A RESTful API for task management with role-based access control, built with Laravel 11.

---

## Table of Contents

1. [Getting Started](#getting-started)
2. [System Overview](#system-overview)
3. [User Roles](#user-roles)
4. [Test Credentials](#test-credentials)
5. [API Endpoints](#api-endpoints)
6. [Features Explained](#features-explained)
7. [Testing Scenarios](#testing-scenarios)
8. [Response Format](#response-format)
9. [Common Commands](#common-commands)

---

## Getting Started

### Prerequisites

- Docker Desktop installed and running

### Starting the Project

1. **Start Docker containers:**
   ```bash
   docker-compose up -d
   ```

2. **Verify containers are running:**
   ```bash
   docker-compose ps
   ```
   You should see `laravel.test` and `mariadb` containers running.

3. **Run database migrations and seed test data:**
   ```bash
   docker-compose exec laravel.test php artisan migrate:fresh --seed
   ```

4. **The API is now available at:** `http://localhost:8000/api`

### Stopping the Project

```bash
docker-compose down
```

---

## System Overview

This is a **Task Management API** that allows users to:

- Register and authenticate using tokens
- Create, view, update, and delete tasks
- Track task status (pending, in progress, completed)
- Manage users (admin only)

The system uses **token-based authentication** - after logging in, you receive a token that must be included in all subsequent requests.

---

## User Roles

| Role | Description |
|------|-------------|
| **Admin** | Full access to all features. Can manage all tasks and users. |
| **Member** | Can create tasks, view all tasks, but can only update/delete their own tasks. Cannot access user management. |

### Permission Matrix

| Action | Admin | Member |
|--------|-------|--------|
| Register/Login | Yes | Yes |
| View all tasks | Yes | Yes |
| Create tasks | Yes | Yes |
| Update own tasks | Yes | Yes |
| Update others' tasks | Yes | No |
| Delete own tasks | Yes | Yes |
| Delete others' tasks | Yes | No |
| View users | Yes | No |
| Delete users | Yes | No |

---

## Test Credentials

After running the database seeder, these accounts are available:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | password |
| Member | member@example.com | password |

---

## API Endpoints

### Base URL
```
http://localhost:8000/api
```

### Authentication Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/register` | Create a new user account | No |
| POST | `/login` | Login and get access token | No |
| POST | `/logout` | Logout and invalidate token | Yes |
| GET | `/me` | Get current user info | Yes |

### Task Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/tasks` | List all tasks (paginated) | Yes |
| GET | `/tasks/{id}` | View a specific task | Yes |
| POST | `/tasks` | Create a new task | Yes |
| PUT | `/tasks/{id}` | Update a task | Yes (Owner/Admin) |
| DELETE | `/tasks/{id}` | Delete a task | Yes (Owner/Admin) |
| PATCH | `/tasks/{id}/status` | Update task status only | Yes (Owner/Admin) |

### User Management Endpoints (Admin Only)

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/users` | List all users (paginated) | Yes (Admin) |
| GET | `/users/{id}` | View a specific user | Yes (Admin) |
| DELETE | `/users/{id}` | Delete a user | Yes (Admin) |

---

## Features Explained

### 1. User Registration

Create a new account (default role is "member").

**Request:**
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "member"
    },
    "token": "1|abc123..."
  }
}
```

### 2. User Login

Authenticate and receive an access token.

**Request:**
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Admin User",
      "email": "admin@example.com",
      "role": "admin"
    },
    "token": "2|xyz789..."
  }
}
```

### 3. Using the Access Token

Include the token in the `Authorization` header for all authenticated requests:

```bash
curl -X GET http://localhost:8000/api/tasks \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### 4. Creating a Task

**Request:**
```bash
curl -X POST http://localhost:8000/api/tasks \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "title": "Complete project documentation",
    "description": "Write user guide and API docs",
    "status": "pending",
    "due_date": "2026-02-15"
  }'
```

**Task Status Values:**
- `pending` - Task not yet started
- `in_progress` - Task is being worked on
- `completed` - Task is finished

**Validation Rules:**
- `title` - Required, max 255 characters
- `description` - Optional
- `status` - Required, must be one of: pending, in_progress, completed
- `due_date` - Optional, must be today or a future date

### 5. Listing Tasks

Tasks are paginated (15 per page by default).

**Request:**
```bash
# Get first page
curl -X GET "http://localhost:8000/api/tasks" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Get specific page with custom page size
curl -X GET "http://localhost:8000/api/tasks?page=2&per_page=5" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 6. Updating a Task

Only the task owner or an admin can update a task.

**Request:**
```bash
curl -X PUT http://localhost:8000/api/tasks/1 \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "title": "Updated title",
    "status": "in_progress"
  }'
```

### 7. Updating Task Status Only

A quick way to just change the status.

**Request:**
```bash
curl -X PATCH http://localhost:8000/api/tasks/1/status \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "status": "completed"
  }'
```

### 8. Deleting a Task

Only the task owner or an admin can delete a task.

**Request:**
```bash
curl -X DELETE http://localhost:8000/api/tasks/1 \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 9. User Management (Admin Only)

**List all users:**
```bash
curl -X GET http://localhost:8000/api/users \
  -H "Accept: application/json" \
  -H "Authorization: Bearer ADMIN_TOKEN"
```

**View specific user:**
```bash
curl -X GET http://localhost:8000/api/users/2 \
  -H "Accept: application/json" \
  -H "Authorization: Bearer ADMIN_TOKEN"
```

**Delete a user:**
```bash
curl -X DELETE http://localhost:8000/api/users/2 \
  -H "Accept: application/json" \
  -H "Authorization: Bearer ADMIN_TOKEN"
```

Note: Deleting a user also deletes all their tasks. Admins cannot delete their own account.

### 10. Logout

Invalidates the current access token.

**Request:**
```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## Testing Scenarios

### Scenario 1: Basic User Flow

1. Register a new user
2. Login with the new user
3. Create a task
4. View the task list
5. Update the task status to "completed"
6. Delete the task
7. Logout

### Scenario 2: Permission Testing (Member)

1. Login as member (member@example.com)
2. Create a task
3. Try to update another user's task (should fail with 403)
4. Try to delete another user's task (should fail with 403)
5. Try to access /api/users (should fail with 403)

### Scenario 3: Admin Capabilities

1. Login as admin (admin@example.com)
2. Update any user's task (should succeed)
3. Delete any user's task (should succeed)
4. View user list
5. Delete a user (and verify their tasks are also deleted)
6. Try to delete own account (should fail with 400)

### Scenario 4: Validation Testing

1. Try to register with invalid email format
2. Try to register with password less than 8 characters
3. Try to create a task without title (should fail)
4. Try to create a task with invalid status
5. Try to create a task with past due date

### Scenario 5: Authentication Testing

1. Try to access /api/tasks without token (should get 401)
2. Login and use token to access /api/tasks (should succeed)
3. Logout
4. Try to use the same token again (should fail with 401)

---

## Response Format

### Success Response

```json
{
  "success": true,
  "message": "Operation description",
  "data": { ... }
}
```

### Error Response

```json
{
  "success": false,
  "message": "Error description"
}
```

### Validation Error Response (422)

```json
{
  "success": false,
  "message": "The title field is required. (and 1 more error)",
  "errors": {
    "title": ["The title field is required."],
    "status": ["The status field is required."]
  }
}
```

### HTTP Status Codes

| Code | Meaning |
|------|---------|
| 200 | Success |
| 201 | Created (new resource) |
| 400 | Bad Request (e.g., trying to delete own account) |
| 401 | Unauthenticated (missing or invalid token) |
| 403 | Unauthorized (no permission for this action) |
| 404 | Resource not found |
| 422 | Validation error |

---

## Common Commands

### Run Tests
```bash
docker-compose exec laravel.test php artisan test
```

### Reset Database with Fresh Data
```bash
docker-compose exec laravel.test php artisan migrate:fresh --seed
```

### View Container Logs
```bash
docker-compose logs -f laravel.test
```

### Access Container Shell
```bash
docker-compose exec laravel.test bash
```

### Clear Application Cache
```bash
docker-compose exec laravel.test php artisan cache:clear
docker-compose exec laravel.test php artisan config:clear
```

---

## Quick Reference Card

### Login and Save Token
```bash
# Login as admin
TOKEN=$(curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"admin@example.com","password":"password"}' | grep -o '"token":"[^"]*' | cut -d'"' -f4)

echo $TOKEN
```

### Use Token in Requests
```bash
curl -X GET http://localhost:8000/api/tasks \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"
```

---

## Troubleshooting

### "Unauthenticated" Error
- Ensure you include the `Authorization: Bearer <token>` header
- Check if your token has expired or been invalidated
- Try logging in again to get a fresh token

### "Unauthorized" Error (403)
- You don't have permission for this action
- Members cannot modify other users' tasks
- Only admins can access user management endpoints

### "Resource not found" Error (404)
- The task or user ID doesn't exist
- Check the ID in your request

### Container Not Starting
- Ensure Docker Desktop is running
- Try `docker-compose down` then `docker-compose up -d`
- Check logs with `docker-compose logs`
