# Enterprise REST API Setup Guide

## Prerequisites

- PHP 8.2 or higher
- Composer
- MySQL/PostgreSQL
- Laravel 11

## Installation Steps

### 1. Install Dependencies

```bash
composer require laravel/sanctum
```

### 2. Publish Sanctum Configuration

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### 3. Run Migrations

```bash
php artisan migrate
```

### 4. Configure Sanctum

Update `config/sanctum.php` if needed. The default configuration should work for most cases.

### 5. Update User Model

The User model has already been updated to use Sanctum's `HasApiTokens` trait.

### 6. Configure API Routes

API routes are already configured in `routes/api.php`.

### 7. Set Up Storage Link

```bash
php artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public` for file access.

## Environment Configuration

Add to your `.env` file:

```env
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1,127.0.0.1:8000,::1
SESSION_DRIVER=cookie
```

## Database Seeding (Optional)

Create seeders for initial data:

```bash
php artisan make:seeder UserRoleSeeder
php artisan make:seeder UserStatusSeeder
php artisan make:seeder LeaveStatusSeeder
```

## Testing the API

### Using cURL

```bash
# Register
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "Staff"
  }'

# Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'

# Get Users (with token)
curl -X GET http://localhost:8000/api/users \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Using Postman

1. Import the API collection (see Postman examples below)
2. Set up environment variables:
   - `base_url`: http://localhost:8000/api
   - `token`: (will be set after login)

## Postman Collection Examples

### Authentication Collection

**Register Request:**
- Method: POST
- URL: `{{base_url}}/register`
- Body (raw JSON):
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "Staff"
}
```

**Login Request:**
- Method: POST
- URL: `{{base_url}}/login`
- Body (raw JSON):
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```
- Tests (to save token):
```javascript
if (pm.response.code === 200) {
    var jsonData = pm.response.json();
    pm.environment.set("token", jsonData.data.token);
}
```

**Get Me:**
- Method: GET
- URL: `{{base_url}}/me`
- Headers:
  - Authorization: Bearer {{token}}

### User Management Collection

**List Users:**
- Method: GET
- URL: `{{base_url}}/users?per_page=15`
- Headers:
  - Authorization: Bearer {{token}}

**Create User:**
- Method: POST
- URL: `{{base_url}}/users`
- Headers:
  - Authorization: Bearer {{token}}
  - Content-Type: application/json
- Body (raw JSON):
```json
{
  "name": "Jane Doe",
  "email": "jane@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "Staff",
  "status": "active"
}
```

**Update User:**
- Method: PUT
- URL: `{{base_url}}/users/1`
- Headers:
  - Authorization: Bearer {{token}}
  - Content-Type: application/json
- Body (raw JSON):
```json
{
  "name": "Jane Updated",
  "email": "jane.updated@example.com"
}
```

**Upload Profile Image:**
- Method: POST
- URL: `{{base_url}}/users/1/upload-image`
- Headers:
  - Authorization: Bearer {{token}}
- Body (form-data):
  - image: [Select File]

### Leave Management Collection

**Create Leave:**
- Method: POST
- URL: `{{base_url}}/leaves`
- Headers:
  - Authorization: Bearer {{token}}
  - Content-Type: application/json
- Body (raw JSON):
```json
{
  "name": "Annual Leave",
  "start_date": "2024-06-01",
  "end_date": "2024-06-05",
  "type": "vacation",
  "reason": "Family vacation",
  "description": "Taking time off for family trip"
}
```

**Approve Leave:**
- Method: POST
- URL: `{{base_url}}/leaves/1/approve`
- Headers:
  - Authorization: Bearer {{token}}
  - Content-Type: application/json
- Body (raw JSON):
```json
{
  "remarks": "Approved. Enjoy your vacation!"
}
```

**Filter Leaves:**
- Method: GET
- URL: `{{base_url}}/leaves?start_date=2024-01-01&end_date=2024-12-31&status=pending&user_id=1`
- Headers:
  - Authorization: Bearer {{token}}

### Tool Management Collection

**Create Tool:**
- Method: POST
- URL: `{{base_url}}/tools`
- Headers:
  - Authorization: Bearer {{token}}
  - Content-Type: application/json
- Body (raw JSON):
```json
{
  "name": "Project Management Tool",
  "description": "Tool for managing projects",
  "url": "https://example.com",
  "category": "Productivity",
  "subcategory": "Project Management",
  "tags": ["project", "management"],
  "status": "active",
  "user_ids": [1, 2, 3]
}
```

**Assign Users to Tool:**
- Method: POST
- URL: `{{base_url}}/tools/1/assign-users`
- Headers:
  - Authorization: Bearer {{token}}
  - Content-Type: application/json
- Body (raw JSON):
```json
{
  "user_ids": [1, 2, 3, 4]
}
```

**Add Tool Cost:**
- Method: POST
- URL: `{{base_url}}/tools/1/add-cost`
- Headers:
  - Authorization: Bearer {{token}}
  - Content-Type: application/json
- Body (raw JSON):
```json
{
  "name": "Monthly Subscription",
  "description": "Monthly subscription cost",
  "amount": 99.99,
  "currency": "USD",
  "date": "2024-01-01"
}
```

### Content Management Collection

**Upload Content:**
- Method: POST
- URL: `{{base_url}}/content`
- Headers:
  - Authorization: Bearer {{token}}
- Body (form-data):
  - file: [Select File]
  - category_id: 1 (optional)

**Download Content:**
- Method: GET
- URL: `{{base_url}}/content/1/download`
- Headers:
  - Authorization: Bearer {{token}}

**Share Content:**
- Method: POST
- URL: `{{base_url}}/content/1/share`
- Headers:
  - Authorization: Bearer {{token}}
  - Content-Type: application/json
- Body (raw JSON):
```json
{
  "shared_with_user_id": 2
}
```

## Performance Optimization

### Database Indexes

Consider adding indexes to frequently queried columns:

```php
// In migrations
$table->index('user_id');
$table->index('status');
$table->index('created_at');
```

### Caching (Optional)

For production, consider implementing Redis caching:

```bash
composer require predis/predis
```

Update `.env`:
```env
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## Security Considerations

1. **HTTPS**: Always use HTTPS in production
2. **Rate Limiting**: Configure rate limiting in `app/Http/Kernel.php`
3. **CORS**: Configure CORS settings if needed
4. **File Upload**: Validate file types and sizes
5. **SQL Injection**: Use Eloquent ORM (already implemented)
6. **XSS**: Sanitize user input (Laravel handles this)

## Troubleshooting

### Token Not Working
- Check if Sanctum is properly installed
- Verify token is included in Authorization header
- Check token expiration settings

### File Upload Issues
- Ensure storage link is created: `php artisan storage:link`
- Check file permissions on storage directory
- Verify file size limits in PHP configuration

### Database Issues
- Run migrations: `php artisan migrate`
- Check database connection in `.env`
- Verify foreign key constraints

## Next Steps

1. Set up database seeders for initial data
2. Configure email notifications (optional)
3. Set up queue workers for background jobs
4. Configure logging and monitoring
5. Set up API documentation (Swagger/OpenAPI)

