# Task Management System API

A RESTful API for task management with role-based access control, built with Laravel 11.

## Features

- **User Authentication**: Register, login, and logout with Laravel Sanctum token-based authentication
- **Role-Based Access Control**: Admin and Member roles with different permissions
- **Task Management**: Full CRUD operations for tasks
- **Task Status Tracking**: Track tasks as pending, in_progress, or completed
- **API Documentation**: Auto-generated documentation with Scribe

## Tech Stack

- **Framework**: Laravel 11
- **Database**: MariaDB
- **Authentication**: Laravel Sanctum
- **Containerization**: Docker (Laravel Sail)
- **Documentation**: Scribe

## Requirements

- Docker Desktop
- Git

## Installation

### 1. Clone the repository

```bash
git clone <repository-url>
cd backend-task-management-system
```

### 2. Copy environment file

```bash
cp .env.example .env
```

### 3. Install dependencies and start containers

```bash
# On Windows (PowerShell)
docker run --rm -v ${PWD}:/app -w /app composer:latest composer install --ignore-platform-reqs

# On Linux/Mac
docker run --rm -v $(pwd):/app -w /app composer:latest composer install --ignore-platform-reqs
```

### 4. Start Docker containers

```bash
./vendor/bin/sail up -d
```

### 5. Generate application key

```bash
./vendor/bin/sail artisan key:generate
```

### 6. Run migrations and seed database

```bash
./vendor/bin/sail artisan migrate:fresh --seed
```

### 7. Generate API documentation

```bash
./vendor/bin/sail artisan scribe:generate
```

## Usage

The API is available at `http://localhost:8000/api`

### Test Credentials

| Role   | Email              | Password |
|--------|-------------------|----------|
| Admin  | admin@example.com | password |
| Member | member@example.com| password |

### API Documentation

After running `scribe:generate`, documentation is available at:
- HTML: `http://localhost:8000/docs`
- OpenAPI: `http://localhost:8000/docs/openapi.yaml`
- Postman: `http://localhost:8000/docs/collection.json`

## API Endpoints

### Authentication

| Method | Endpoint      | Description         | Access        |
|--------|---------------|---------------------|---------------|
| POST   | /api/register | Register new user   | Public        |
| POST   | /api/login    | User login          | Public        |
| POST   | /api/logout   | User logout         | Authenticated |
| GET    | /api/me       | Get current user    | Authenticated |

### Tasks

| Method | Endpoint              | Description         | Access         |
|--------|-----------------------|---------------------|----------------|
| GET    | /api/tasks            | List all tasks      | Authenticated  |
| GET    | /api/tasks/{id}       | View single task    | Authenticated  |
| POST   | /api/tasks            | Create task         | Authenticated  |
| PUT    | /api/tasks/{id}       | Update task         | Owner or Admin |
| DELETE | /api/tasks/{id}       | Delete task         | Owner or Admin |
| PATCH  | /api/tasks/{id}/status| Update task status  | Owner or Admin |

### Users (Admin Only)

| Method | Endpoint         | Description         | Access |
|--------|------------------|---------------------|--------|
| GET    | /api/users       | List all users      | Admin  |
| GET    | /api/users/{id}  | View single user    | Admin  |
| DELETE | /api/users/{id}  | Delete user         | Admin  |

## API Response Format

### Success Response

```json
{
    "success": true,
    "message": "Operation successful",
    "data": { ... }
}
```

### Error Response

```json
{
    "success": false,
    "message": "Error description",
    "errors": { ... }
}
```

## Running Tests

```bash
./vendor/bin/sail artisan test
```

## Common Commands

```bash
# Start containers
./vendor/bin/sail up -d

# Stop containers
./vendor/bin/sail down

# Run migrations
./vendor/bin/sail artisan migrate

# Fresh migration with seeding
./vendor/bin/sail artisan migrate:fresh --seed

# Run tests
./vendor/bin/sail artisan test

# Generate API documentation
./vendor/bin/sail artisan scribe:generate

# Clear cache
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan route:clear

# Access container shell
./vendor/bin/sail shell

# View logs
./vendor/bin/sail logs
```

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php      # Authentication endpoints
│   │   ├── TaskController.php      # Task CRUD operations
│   │   └── UserController.php      # User management (admin)
│   ├── Middleware/
│   │   └── RoleMiddleware.php      # Role-based access control
│   ├── Requests/                   # Form validation
│   └── Resources/                  # API response transformers
├── Models/
│   ├── Task.php
│   └── User.php
├── Policies/
│   └── TaskPolicy.php              # Task authorization
└── Traits/
    └── ApiResponse.php             # Consistent API responses

database/
├── factories/                      # Model factories for testing
├── migrations/                     # Database migrations
└── seeders/                        # Database seeders

routes/
└── api.php                         # API routes

tests/Feature/                      # Feature tests
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
