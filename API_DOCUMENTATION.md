# Enterprise REST API Documentation

## Base URL
```
http://your-domain.com/api
```

## Authentication

All protected routes require authentication using Laravel Sanctum. Include the token in the Authorization header:

```
Authorization: Bearer {token}
```

## Standard Response Format

### Success Response
```json
{
  "success": true,
  "message": "Descriptive message",
  "data": {},
  "meta": {}
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message",
  "errors": {}
}
```

## Authentication Endpoints

### Register
**POST** `/api/register`

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "Client"
}
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
      "role": "Staff"
    },
    "token": "1|xxxxxxxxxxxx",
    "token_type": "Bearer"
  }
}
```

### Login
**POST** `/api/login`

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "token": "1|xxxxxxxxxxxx",
    "token_type": "Bearer"
  }
}
```

### Logout
**POST** `/api/logout`

**Headers:**
```
Authorization: Bearer {token}
```

### Get Authenticated User
**GET** `/api/me`

**Headers:**
```
Authorization: Bearer {token}
```

## User Endpoints

### List Users
**GET** `/api/users?per_page=15&page=1`

**Query Parameters:**
- `per_page` (optional): Number of items per page (default: 15)
- `page` (optional): Page number

### Create User
**POST** `/api/users`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "name": "Jane Doe",
  "email": "jane@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "Staff",
  "status": "active",
  "phone": "+1234567890",
  "address": "123 Main St",
  "city": "New York",
  "state": "NY",
  "zip": "10001",
  "country": "USA"
}
```

### Get User
**GET** `/api/users/{id}`

### Update User
**PUT** `/api/users/{id}`

**Request Body:**
```json
{
  "name": "Jane Updated",
  "email": "jane.updated@example.com"
}
```

### Delete User
**DELETE** `/api/users/{id}`

**Required Role:** Admin

### Assign Role
**POST** `/api/users/{id}/assign-role`

**Request Body:**
```json
{
  "role": "Manager"
}
```

**Required Role:** Admin

### Assign Status
**POST** `/api/users/{id}/assign-status`

**Request Body:**
```json
{
  "status": "inactive"
}
```

**Required Role:** Admin, Staff

### Upload Profile Image
**POST** `/api/users/{id}/upload-image`

**Request:** Multipart form data
- `image`: Image file (jpeg, png, jpg, gif, max 2MB)

### Add Note
**POST** `/api/users/{id}/add-note`

**Request Body:**
```json
{
  "note": "User note here"
}
```

### Search Users
**GET** `/api/users/search?query=john&per_page=15`

### Get Users by Role
**GET** `/api/users/role/{role}`

### Get Users by Status
**GET** `/api/users/status/{status}`

## Leave Endpoints

### List Leaves
**GET** `/api/leaves?start_date=2024-01-01&end_date=2024-12-31&status=pending&user_id=1&per_page=15`

**Query Parameters:**
- `start_date` (optional): Filter by start date
- `end_date` (optional): Filter by end date
- `status` (optional): Filter by status (pending, approved, rejected, cancelled)
- `user_id` (optional): Filter by user ID
- `type` (optional): Filter by type (vacation, sick, personal, other)
- `per_page` (optional): Items per page

### Create Leave Request
**POST** `/api/leaves`

**Request Body:**
```json
{
  "name": "Annual Leave",
  "start_date": "2024-06-01",
  "end_date": "2024-06-05",
  "type": "vacation",
  "reason": "Family vacation",
  "description": "Taking time off for family trip",
  "notes": "Will be available for emergencies"
}
```

### Get Leave
**GET** `/api/leaves/{id}`

### Update Leave
**PUT** `/api/leaves/{id}`

**Request Body:**
```json
{
  "status": "approved",
  "notes": "Approved by staff"
}
```

### Delete Leave
**DELETE** `/api/leaves/{id}`

### Approve Leave
**POST** `/api/leaves/{id}/approve`

**Request Body:**
```json
{
  "remarks": "Approved. Enjoy your vacation!"
}
```

**Required Role:** Admin, Staff

### Reject Leave
**POST** `/api/leaves/{id}/reject`

**Request Body:**
```json
{
  "remarks": "Cannot approve due to project deadline"
}
```

**Required Role:** Admin, Staff

### Add Note
**POST** `/api/leaves/{id}/add-note`

**Request Body:**
```json
{
  "note": "Additional note here"
}
```

### Search Leaves
**GET** `/api/leaves/search?query=annual&per_page=15`

## Tool Endpoints

### List Tools
**GET** `/api/tools?per_page=15`

### Create Tool
**POST** `/api/tools`

**Request:** Multipart form data
- `name`: Tool name
- `description`: Tool description
- `image`: Image file (optional)
- `url`: Tool URL (optional)
- `category`: Category name
- `subcategory`: Subcategory (optional)
- `tags`: Array of tags (optional)
- `status`: Status (optional)
- `user_ids`: Array of user IDs to assign (optional)

**Example JSON:**
```json
{
  "name": "Project Management Tool",
  "description": "Tool for managing projects",
  "url": "https://example.com",
  "category": "Productivity",
  "subcategory": "Project Management",
  "tags": ["project", "management", "productivity"],
  "status": "active",
  "user_ids": [1, 2, 3]
}
```

### Get Tool
**GET** `/api/tools/{id}`

### Update Tool
**PUT** `/api/tools/{id}`

### Delete Tool
**DELETE** `/api/tools/{id}`

**Required Role:** Admin

### Assign Users
**POST** `/api/tools/{id}/assign-users`

**Request Body:**
```json
{
  "user_ids": [1, 2, 3, 4]
}
```

**Required Role:** Admin, Staff

### Add Note
**POST** `/api/tools/{id}/add-note`

**Request Body:**
```json
{
  "note": "Tool note here"
}
```

### Add Cost
**POST** `/api/tools/{id}/add-cost`

**Request Body:**
```json
{
  "name": "Monthly Subscription",
  "description": "Monthly subscription cost",
  "amount": 99.99,
  "currency": "USD",
  "date": "2024-01-01"
}
```

### Add Billing
**POST** `/api/tools/{id}/add-billing`

**Request Body:**
```json
{
  "name": "January Billing",
  "description": "Billing for January 2024",
  "amount": 99.99,
  "currency": "USD",
  "billing_date": "2024-01-31",
  "billing_period": "January 2024"
}
```

### Get Tools by Category
**GET** `/api/tools/category/{category}`

### Get Tools by Status
**GET** `/api/tools/status/{status}`

### Get Tools by User
**GET** `/api/tools/user/{userId}`

### Search Tools
**GET** `/api/tools/search?query=project&per_page=15`

## Content Endpoints

### List Content
**GET** `/api/content?per_page=15`

### Upload Content
**POST** `/api/content`

**Request:** Multipart form data
- `file`: File to upload (max 10MB)
- `category_id`: Category ID (optional)

### Get Content
**GET** `/api/content/{id}`

### Download Content
**GET** `/api/content/{id}/download`

### Share Content
**POST** `/api/content/{id}/share`

**Request Body:**
```json
{
  "shared_with_user_id": 2
}
```

### Delete Content
**DELETE** `/api/content/{id}`

### Get Content by User
**GET** `/api/content/user/{userId}`

### Get Content by Category
**GET** `/api/content/category/{categoryId}`

### Get Content by File Type
**GET** `/api/content/file-type/{fileType}`

### Search Content
**GET** `/api/content/search?query=document&per_page=15`

## HTTP Status Codes

- `200` - Success
- `201` - Created
- `204` - No Content
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

## Role-Based Access Control

### Admin
- Full access to all endpoints
- Can manage users, roles, and statuses
- Can approve/reject leaves
- Can manage all tools and content

### Staff
- Can view and manage most resources
- Can approve/reject leaves
- Can assign users to tools
- Can create and manage users (except delete)
- Cannot delete users or tools

### Client
- Can create and manage own leaves
- Can upload and manage own content
- Can view assigned tools
- Limited access to user management
- Can only view and update own profile

## Pagination

All list endpoints support pagination:

```
GET /api/users?per_page=20&page=2
```

Response includes pagination metadata:
```json
{
  "success": true,
  "message": "Success",
  "data": [...],
  "meta": {
    "pagination": {
      "current_page": 2,
      "per_page": 20,
      "total": 100,
      "last_page": 5,
      "from": 21,
      "to": 40
    }
  }
}
```

## Error Handling

All errors follow the standard format:

```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field": ["Error message for field"]
  }
}
```

## Rate Limiting

API requests are rate-limited. Check response headers for rate limit information:
- `X-RateLimit-Limit`: Maximum requests allowed
- `X-RateLimit-Remaining`: Remaining requests
- `Retry-After`: Seconds to wait before retrying

