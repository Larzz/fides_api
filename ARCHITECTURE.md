# Enterprise REST API Architecture

## Overview

This is a production-ready enterprise REST API built with Laravel 11, following SOLID principles, clean architecture, and best practices.

## Architecture Layers

### 1. Models Layer (`app/Models/`)
All Eloquent models with relationships, soft deletes, and traits:
- **User Module**: `User`, `UserRole`, `UserStatus`, `UserNote`, `UserImage`
- **Leave Module**: `Leave`, `LeaveStatus`, `LeaveNote`
- **Tool Module**: `Tool`, `ToolCategory`, `ToolStatus`, `ToolTag`, `ToolUser`, `ToolNote`, `ToolCost`, `ToolBilling`
- **Content Module**: `UserContentUpload`, `UserContentDownload`, `UserContentShare`, `UserContentCategory`

### 2. Traits Layer (`app/Traits/`)
Reusable functionality:
- `HasActivityLog`: Automatic activity logging
- `HasNotifications`: Notification system

### 3. Repository Layer (`app/Repositories/`)
Data access abstraction:
- `BaseRepository`: Base repository with common methods
- `UserRepository`: User-specific queries
- `LeaveRepository`: Leave-specific queries with filters
- `ToolRepository`: Tool-specific queries
- `ContentRepository`: Content-specific queries

### 4. Service Layer (`app/Services/`)
Business logic layer:
- `UserService`: User management operations
- `LeaveService`: Leave management operations
- `ToolService`: Tool management operations
- `ContentService`: Content management operations

### 5. Controller Layer (`app/Http/Controllers/Api/`)
HTTP request handling:
- `AuthController`: Authentication endpoints
- `UserController`: User CRUD operations
- `LeaveController`: Leave CRUD operations
- `ToolController`: Tool CRUD operations
- `ContentController`: Content CRUD operations

### 6. Request Validation (`app/Http/Requests/`)
Form request validators:
- User: `StoreUserRequest`, `UpdateUserRequest`, `AssignRoleRequest`, `UploadImageRequest`, `AddNoteRequest`
- Leave: `StoreLeaveRequest`, `UpdateLeaveRequest`, `ApproveLeaveRequest`, `AddNoteRequest`
- Tool: `StoreToolRequest`, `UpdateToolRequest`, `AssignUsersRequest`, `AddCostRequest`
- Content: `UploadContentRequest`, `ShareContentRequest`

### 7. Resource Layer (`app/Http/Resources/`)
API response transformation:
- `UserResource`, `UserNoteResource`, `UserImageResource`
- `LeaveResource`, `LeaveNoteResource`
- `ToolResource`, `ToolTagResource`, `ToolNoteResource`, `ToolCostResource`, `ToolBillingResource`
- `ContentResource`, `ContentCategoryResource`

### 8. Policy Layer (`app/Policies/`)
Authorization policies:
- `UserPolicy`: User access control
- `LeavePolicy`: Leave access control
- `ToolPolicy`: Tool access control
- `ContentPolicy`: Content access control

### 9. Events & Listeners (`app/Events/`, `app/Listeners/`)
Event-driven architecture:
- `LeaveStatusChanged` event → `HandleLeaveStatusChanged` listener
- `ToolStatusChanged` event → `HandleToolStatusChanged` listener

### 10. Middleware (`app/Http/Middleware/`)
Request filtering:
- `EnsureRole`: Role-based access control
- `EnsureAdmin`: Admin-only access
- `EnsureStaff`: Staff+ access

### 11. Response Layer (`app/Http/Responses/`)
Standardized API responses:
- `ApiResponse`: Static methods for consistent responses

## Key Features

### Authentication
- Laravel Sanctum for API token authentication
- Role-based access control (Admin, Staff, Client)
- Protected routes with middleware

### Activity Logging
- Automatic activity logging via traits
- Tracks user actions with metadata
- Stores IP address, device info, timestamps

### Notifications
- System-wide notification system
- Read/unread tracking
- Module-specific notifications

### Database Transactions
- All write operations wrapped in transactions
- Ensures data consistency
- Automatic rollback on errors

### Soft Deletes
- Implemented for `Leave`, `Tool`, and `UserContentUpload`
- Allows data recovery
- Maintains referential integrity

### Eager Loading
- Prevents N+1 query problems
- Optimized relationship loading
- Configurable via query parameters

### Filtering & Search
- Advanced filtering on list endpoints
- Full-text search capabilities
- Date range filtering for leaves
- Status and category filtering

### File Management
- Secure file uploads
- File validation and storage
- Download tracking
- Sharing capabilities

## API Standards

### RESTful Design
- Resource-based URLs
- Standard HTTP methods (GET, POST, PUT, DELETE)
- Proper status codes
- Consistent naming conventions

### Response Format
```json
{
  "success": true,
  "message": "Descriptive message",
  "data": {},
  "meta": {}
}
```

### Error Handling
- Global exception handler
- Validation error formatting
- Consistent error responses
- Proper HTTP status codes

### Pagination
- All list endpoints support pagination
- Configurable page size
- Metadata included in responses

## Security Features

1. **Authentication**: Sanctum token-based auth
2. **Authorization**: Policy-based access control
3. **Validation**: Form request validation
4. **SQL Injection**: Eloquent ORM protection
5. **XSS**: Laravel's built-in protection
6. **CSRF**: API token-based (no CSRF needed)
7. **Rate Limiting**: Configurable per route

## Performance Optimizations

1. **Eager Loading**: Prevents N+1 queries
2. **Database Indexes**: On foreign keys and frequently queried columns
3. **Query Scopes**: Reusable query logic
4. **Caching Ready**: Redis support (optional)

## File Structure

```
app/
├── Events/              # Domain events
├── Http/
│   ├── Controllers/
│   │   └── Api/         # API controllers
│   ├── Middleware/      # Custom middleware
│   ├── Requests/        # Form request validators
│   ├── Resources/       # API resources
│   └── Responses/       # Response helpers
├── Interfaces/          # Repository interfaces
├── Listeners/            # Event listeners
├── Models/               # Eloquent models
├── Policies/             # Authorization policies
├── Providers/            # Service providers
├── Repositories/         # Data access layer
├── Services/             # Business logic layer
└── Traits/               # Reusable traits

routes/
└── api.php               # API routes

database/
└── migrations/           # Database migrations
```

## Database Design

### Core Tables
- `users`, `user_roles`, `user_statuses`, `user_notes`, `user_images`
- Activity and notification tables for each module

### Leave Management
- `leave`, `leave_statuses`, `leave_notes`
- Activity and notification tracking

### Tool Management
- `tools`, `tool_categories`, `tool_statuses`, `tool_tags`
- `tool_users` (pivot), `tool_notes`, `tool_cost`, `tool_billings`
- Activity and notification tracking

### Content System
- `user_content_upload`, `user_content_download`, `user_content_share`
- `user_content_category`

## Next Steps for Production

1. **Install Sanctum**: `composer require laravel/sanctum`
2. **Run Migrations**: `php artisan migrate`
3. **Create Seeders**: Initial data for roles, statuses
4. **Configure Caching**: Redis for production
5. **Set Up Queue**: For background jobs
6. **Configure Logging**: Centralized logging
7. **API Documentation**: Swagger/OpenAPI
8. **Testing**: Unit and integration tests
9. **Monitoring**: Error tracking (Sentry)
10. **Deployment**: CI/CD pipeline

## Testing Recommendations

1. Unit tests for services
2. Integration tests for API endpoints
3. Feature tests for workflows
4. Policy tests for authorization
5. Repository tests for data access

## Scalability Considerations

1. **Horizontal Scaling**: Stateless API design
2. **Database**: Read replicas for read-heavy operations
3. **Caching**: Redis for frequently accessed data
4. **Queue**: Background job processing
5. **CDN**: For file storage and delivery
6. **Load Balancing**: Multiple API instances

