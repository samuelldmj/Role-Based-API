# RBAC API

A Laravel-based Role-Based Access Control (RBAC) API that provides comprehensive user management, role assignment, and permission control functionality with OAuth2 authentication via Laravel Passport.

## Features

-   User registration and authentication
-   Role-based access control (RBAC)
-   Permission management system
-   OAuth2 authentication with Laravel Passport
-   RESTful API endpoints
-   Comprehensive user, role, and permission management
-   External API integration capabilities
-   Database seeding for initial setup

## Requirements

-   PHP 8.2 or higher
-   Composer
-   Node.js and npm
-   SQLite (default) or MySQL/PostgreSQL

## Installation

1. Clone the repository:

```bash
git clone <repository-url>
cd rbac-api
```

2. Install PHP dependencies:

```bash
composer install
```

3. Install Node.js dependencies:

```bash
npm install
```

4. Copy environment configuration:

```bash
cp .env.example .env
```

5. Generate application key:

```bash
php artisan key:generate
```

6. Run database migrations:

```bash
php artisan migrate
```

7. Install Laravel Passport:

```bash
php artisan passport:install
```

8. Seed the database with initial data:

```bash
php artisan db:seed
```

## Configuration

### Database

The application uses SQLite by default. To use a different database:

1. Update the `.env` file with your database credentials:

```env
DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=rbac_api
# DB_USERNAME=your_username
# DB_PASSWORD=your_password
```

### Laravel Passport

OAuth2 keys are automatically generated during installation. The keys are stored in the `storage` directory.

## Usage

### Starting the Development Server

```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

### API Endpoints

#### Authentication

-   `POST /api/register` - Register a new user
-   `POST /api/login` - User login
-   `POST /api/logout` - User logout (requires authentication)
-   `GET /api/profile` - Get user profile (requires authentication)

#### User Management

-   `GET /api/users` - List all users (requires `view-users` permission)
-   `POST /api/users` - Create a new user (requires `create-users` permission)
-   `GET /api/users/{id}` - Get user details (requires `view-users` permission)
-   `PUT /api/users/{id}` - Update user (requires `edit-users` permission)
-   `DELETE /api/users/{id}` - Delete user (requires `delete-users` permission)
-   `POST /api/users/{id}/assign-roles` - Assign roles to user (requires `assign-roles` permission)

#### Role Management

-   `GET /api/roles` - List all roles (requires `view-roles` permission)
-   `POST /api/roles` - Create a new role (requires `create-roles` permission)
-   `GET /api/roles/{id}` - Get role details (requires `view-roles` permission)
-   `PUT /api/roles/{id}` - Update role (requires `edit-roles` permission)
-   `DELETE /api/roles/{id}` - Delete role (requires `delete-roles` permission)
-   `POST /api/roles/{id}/assign-permissions` - Assign permissions to role (requires `assign-permissions` permission)

#### Permission Management

-   `GET /api/permissions` - List all permissions (requires `view-permissions` permission)
-   `POST /api/permissions` - Create a new permission (requires `create-permissions` permission)
-   `GET /api/permissions/{id}` - Get permission details (requires `view-permissions` permission)
-   `PUT /api/permissions/{id}` - Update permission (requires `edit-permissions` permission)
-   `DELETE /api/permissions/{id}` - Delete permission (requires `delete-permissions` permission)

#### External API

-   `GET /api/external/users` - Get external users (requires authentication)

### Authentication

All protected endpoints require a Bearer token in the Authorization header:

```
Authorization: Bearer your_access_token
```

### Request/Response Format

All API responses follow this structure:

```json
{
    "success": true,
    "code": 200,
    "data": {},
    "message": "Success message"
}
```

## Architecture & Design

### RBAC Approach

This API implements a flexible Role-Based Access Control system using a many-to-many relationship model:

-   **Users** can have multiple **Roles**
-   **Roles** can have multiple **Permissions**
-   **Permissions** are checked at the endpoint level using middleware
-   **Inheritance**: Users inherit all permissions from their assigned roles

### Database Structure & ERD

```
┌─────────────┐       ┌─────────────┐       ┌─────────────┐
│    users    │       │ user_role   │       │    roles    │
├─────────────┤       ├─────────────┤       ├─────────────┤
│ id (PK)     │◄─────►│ user_id (FK)│◄─────►│ id (PK)     │
│ name        │       │ role_id (FK)│       │ name        │
│ email       │       │ timestamps  │       │ slug        │
│ password    │       └─────────────┘       │ description │
│ phone       │                             │ timestamps  │
│ is_active   │                             └─────────────┘
│ timestamps  │                                     │
└─────────────┘                                     │
                                                    │
                      ┌─────────────┐              │
                      │role_permiss-│              │
                      │ion          │              │
                      ├─────────────┤              │
                      │ role_id (FK)│◄─────────────┘
                      │ permission_ │
                      │ id (FK)     │◄─────────────┐
                      │ timestamps  │              │
                      └─────────────┘              │
                                                   │
┌─────────────┐                                   │
│permissions  │                                   │
├─────────────┤                                   │
│ id (PK)     │◄──────────────────────────────────┘
│ name        │
│ slug        │
│ description │
│ timestamps  │
└─────────────┘
```

### Database Design Decisions

**1. Slug-Based Identification**

-   Both roles and permissions use `slug` fields for identification
-   Provides human-readable, URL-friendly identifiers
-   Enables easier permission checking in code

**2. Many-to-Many Relationships**

-   `user_role` pivot table: Users can have multiple roles
-   `role_permission` pivot table: Roles can have multiple permissions
-   Cascade deletion ensures data integrity

**3. Flexible Permission System**

-   Permissions are granular (e.g., `view-users`, `create-users`)
-   Middleware-based permission checking at route level
-   Trait-based permission methods for easy model integration

**4. User Status Management**

-   `is_active` boolean field for account activation/deactivation
-   Prevents login for deactivated users while preserving data

### External API Integration

**Service-Oriented Architecture**

-   Dedicated `ExternalApiService` class for third-party API calls
-   Dependency injection in controllers for testability
-   Configurable base URLs and timeouts

**Caching Strategy**

-   5-minute cache for external API responses
-   Reduces external API calls and improves performance
-   Cache key: `external_users`

**Error Handling**

-   Connection timeout handling
-   HTTP status code propagation
-   Comprehensive logging for debugging
-   Graceful fallback responses

**Implementation Details**

```php
// Cached external API call
Cache::remember('external_users', 300, function () {
    return Http::timeout(30)->get($baseUrl . '/users');
});
```

### Core Tables

-   `users` - User accounts with authentication data
-   `roles` - System roles (admin, user, etc.)
-   `permissions` - Granular permissions (view-users, create-roles, etc.)
-   `user_role` - User-role many-to-many relationships
-   `role_permission` - Role-permission many-to-many relationships

### Default Roles and Permissions

The system comes with predefined roles and permissions that are seeded during installation. Check the database seeders for the complete list.

## Development

### Running Tests

```bash
php artisan test
```

### Code Style

The project uses Laravel Pint for code formatting:

```bash
./vendor/bin/pint
```

### Development Environment

For a complete development environment with hot reloading:

```bash
composer run dev
```

This command starts the Laravel server, queue worker, log viewer, and Vite development server concurrently.

## Security

-   **Password Security**: All passwords are hashed using Laravel's built-in bcrypt hashing
-   **OAuth2 Authentication**: Laravel Passport provides secure token-based authentication
-   **Permission-Based Access Control**: Middleware enforces permissions at endpoint level
-   **Input Validation**: Comprehensive validation rules on all endpoints
-   **CSRF Protection**: Enabled for web routes
-   **Database Constraints**: Foreign key constraints ensure data integrity
-   **Cascade Deletion**: Automatic cleanup of relationships when parent records are deleted

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests and ensure code style compliance
5. Submit a pull request

## License

This project is licensed under the MIT License.
