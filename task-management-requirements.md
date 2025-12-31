# Task Management System

## Requirements Document

---

## 1. Project Overview

A simple task management system where teams can create, assign, and track tasks. The system provides role-based access control allowing admins to manage all resources while members can manage their own tasks.

---

## 2. Tech Stack

| Component | Technology |
|-----------|------------|
| Language | PHP |
| Framework | Laravel |
| Architecture | MVC (Model-View-Controller) |
| ORM | Eloquent |
| Database | MariaDB |
| Containerization | Docker |
| Package Manager | Composer |
| Version Control | Git |
| API Testing | Postman |
| Code Editor | VS Code |

---

## 3. User Roles

### Admin
- Can manage all tasks (create, read, update, delete)
- Can manage all users
- Can view all tasks in the system

### Member
- Can create new tasks
- Can view all tasks
- Can update and delete only their own tasks

---

## 4. Key Features

| Feature | Description |
|---------|-------------|
| CRUD Operations | Create, view, edit, delete tasks |
| Authentication | User register, login, logout |
| Authorization | Role-based permissions (Admin/Member) |
| Middleware | Protect routes from unauthorized access |
| Role-based Access | Restrict actions based on user role |
| Form Validation | Validate all input data |
| RESTful API | JSON-based API endpoints |

---

## 5. Database Design

### Users Table

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| name | varchar(255) | User's full name |
| email | varchar(255) | Unique email address |
| password | varchar(255) | Hashed password |
| role | enum('admin', 'member') | User role |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

### Tasks Table

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| user_id | bigint | Foreign key to users table |
| title | varchar(255) | Task title |
| description | text | Task description (optional) |
| status | enum | Task status |
| due_date | date | Task deadline (optional) |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

### Task Statuses

- `pending` — Task not yet started
- `in_progress` — Task currently being worked on
- `completed` — Task finished

---

## 6. API Endpoints

### Authentication

| Method | Endpoint | Description | Access |
|--------|----------|-------------|--------|
| POST | /api/register | Register new user | Public |
| POST | /api/login | User login | Public |
| POST | /api/logout | User logout | Authenticated |

### Tasks

| Method | Endpoint | Description | Access |
|--------|----------|-------------|--------|
| GET | /api/tasks | List all tasks | Authenticated |
| GET | /api/tasks/{id} | View single task | Authenticated |
| POST | /api/tasks | Create new task | Authenticated |
| PUT | /api/tasks/{id} | Update task | Owner or Admin |
| DELETE | /api/tasks/{id} | Delete task | Owner or Admin |
| PATCH | /api/tasks/{id}/status | Update task status | Owner or Admin |

### Users (Admin Only)

| Method | Endpoint | Description | Access |
|--------|----------|-------------|--------|
| GET | /api/users | List all users | Admin |
| GET | /api/users/{id} | View single user | Admin |
| DELETE | /api/users/{id} | Delete user | Admin |

---

## 7. Validation Rules

### Registration

| Field | Rules |
|-------|-------|
| name | required, string, max 255 characters |
| email | required, valid email, unique |
| password | required, minimum 8 characters, confirmed |

### Login

| Field | Rules |
|-------|-------|
| email | required, valid email |
| password | required |

### Task Creation/Update

| Field | Rules |
|-------|-------|
| title | required, string, max 255 characters |
| description | optional, string |
| status | required, must be: pending, in_progress, completed |
| due_date | optional, valid date, must be today or future |

---

## 8. API Response Format

### Success Response

```json
{
    "success": true,
    "message": "Operation successful",
    "data": { }
}
```

### Error Response

```json
{
    "success": false,
    "message": "Error description",
    "errors": { }
}
```

---

## 9. Middleware

| Middleware | Purpose |
|------------|---------|
| auth:sanctum | Verify user is authenticated |
| role:admin | Verify user has admin role |

---

## 10. Docker Setup

### Services Required

- **Laravel App** — PHP application container
- **MariaDB** — Database container

### Ports

| Service | Port |
|---------|------|
| Laravel | 8000 |
| MariaDB | 3306 |

---

## 11. Project Structure

```
task-management/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── TaskController.php
│   │   │   └── UserController.php
│   │   ├── Middleware/
│   │   │   └── RoleMiddleware.php
│   │   └── Requests/
│   │       ├── TaskRequest.php
│   │       └── RegisterRequest.php
│   └── Models/
│       ├── User.php
│       └── Task.php
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/
│   └── api.php
├── docker-compose.yml
├── .env
└── README.md
```

---

## 12. Future Enhancements (Optional)

- Task assignment to other users
- Task categories/labels
- Task comments
- Email notifications for due dates
- Task priority levels
- Search and filter tasks

---

## 13. Timeline

| Phase | Tasks | Duration |
|-------|-------|----------|
| Phase 1 | Environment setup, Docker, Laravel install | 1 day |
| Phase 2 | Database design, migrations, models | 1 day |
| Phase 3 | Authentication system | 1 day |
| Phase 4 | Task CRUD operations | 2 days |
| Phase 5 | Role-based access, middleware | 1 day |
| Phase 6 | Testing with Postman | 1 day |
| Phase 7 | Documentation | 1 day |

**Total Estimated Time: 8 days**

---
