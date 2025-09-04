# API Endpoints Documentation

## Base URL
```
http://localhost:8000/api
```

## Authentication Endpoints

### Login
```
POST /auth/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password123"
}
```

### Register
```
POST /auth/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "user@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

### Refresh Token
```
POST /auth/refresh
Authorization: Bearer {token}
```

### Logout
```
POST /auth/logout
Authorization: Bearer {token}
```

### Get Current User
```
GET /auth/me
Authorization: Bearer {token}
```

### Get Profile
```
GET /auth/profile
Authorization: Bearer {token}
```

### Update Profile
```
PUT /auth/profile
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "John Doe",
    "email": "newemail@example.com"
}
```

### Check Email
```
POST /auth/check-email
Content-Type: application/json

{
    "email": "user@example.com"
}
```

## User Management Endpoints

### Get All Users
```
GET /users
Authorization: Bearer {token}
```

### Get User by ID
```
GET /users/{id}
Authorization: Bearer {token}
```

### Create User
```
POST /users
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "John Doe",
    "email": "user@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "user"
}
```

### Update User
```
PUT /users/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "John Doe",
    "email": "newemail@example.com"
}
```

### Delete User
```
DELETE /users/{id}
Authorization: Bearer {token}
```

### Find User by Name
```
GET /users/find/name?name=John
Authorization: Bearer {token}
```

### Upload Avatar
```
POST /users/avatar
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
    "avatar": [file]
}
```

### Delete Avatar
```
DELETE /users/avatar
Authorization: Bearer {token}
```

### Get User Profile
```
GET /users/profile
Authorization: Bearer {token}
```

### Update User Profile
```
PUT /users/profile
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "John Doe",
    "email": "newemail@example.com"
}
```

## Admin Dashboard Endpoints

### Dashboard Overview
```
GET /admin/dashboard
Authorization: Bearer {token}
```

### System Statistics
```
GET /admin/statistics
Authorization: Bearer {token}
```

### System Settings
```
GET /admin/system-settings
Authorization: Bearer {token}
```

### Analytics
```
GET /admin/analytics?period=month&type=overview
Authorization: Bearer {token}
```

### System Logs
```
GET /admin/system-logs?level=error&limit=100
Authorization: Bearer {token}
```

### Notifications
```
GET /admin/notifications
Authorization: Bearer {token}
```

### Export System Data
```
POST /admin/export-data
Authorization: Bearer {token}
Content-Type: application/json

{
    "type": "users",
    "format": "json",
    "filters": {}
}
```

## Admin User Management Endpoints

### Get All Users (Admin)
```
GET /admin/users?search=john&filters[role]=user&filters[status]=active&page=1&per_page=15
Authorization: Bearer {token}
```

### Get User Statistics
```
GET /admin/users/statistics
Authorization: Bearer {token}
```

### Export User Data
```
GET /admin/users/export?format=json&filters[role]=user
Authorization: Bearer {token}
```

### Bulk User Operations
```
POST /admin/users/bulk-operations
Authorization: Bearer {token}
Content-Type: application/json

{
    "action": "activate",
    "user_ids": [1, 2, 3],
    "role": "user"
}
```

### Get User Details (Admin)
```
GET /admin/users/{id}
Authorization: Bearer {token}
```

### Create User (Admin)
```
POST /admin/users
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "John Doe",
    "email": "user@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "user",
    "status": "active",
    "phone": "+1234567890",
    "address": "123 Main St",
    "bio": "User bio"
}
```

### Update User (Admin)
```
PUT /admin/users/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "John Doe",
    "email": "newemail@example.com",
    "role": "moderator",
    "status": "active"
}
```

### Delete User (Admin)
```
DELETE /admin/users/{id}
Authorization: Bearer {token}
```

### Get User Activity Log
```
GET /admin/users/{id}/activity-log
Authorization: Bearer {token}
```

## Admin Membership Management Endpoints

### Get All Packages (Admin)
```
GET /admin/memberships/packages?search=premium&filters[status]=active&page=1&per_page=15
Authorization: Bearer {token}
```

### Get Package Statistics
```
GET /admin/memberships/packages/statistics
Authorization: Bearer {token}
```

### Bulk Package Operations
```
POST /admin/memberships/packages/bulk-operations
Authorization: Bearer {token}
Content-Type: application/json

{
    "action": "activate",
    "package_ids": [1, 2, 3]
}
```

### Get Package Details (Admin)
```
GET /admin/memberships/packages/{id}
Authorization: Bearer {token}
```

### Create Package (Admin)
```
POST /admin/memberships/packages
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Premium Package",
    "description": "Premium features",
    "price": 99.99,
    "duration_days": 30,
    "features": ["Feature 1", "Feature 2"],
    "status": "active",
    "is_featured": true,
    "max_users": 1000,
    "discount_percentage": 10
}
```

### Update Package (Admin)
```
PUT /admin/memberships/packages/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Updated Package",
    "price": 149.99,
    "is_featured": true
}
```

### Delete Package (Admin)
```
DELETE /admin/memberships/packages/{id}
Authorization: Bearer {token}
```

### Get User Memberships
```
GET /admin/memberships/user-memberships?search=john&filters[status]=active&page=1&per_page=15
Authorization: Bearer {token}
```

### Assign Membership to User
```
POST /admin/memberships/assign-membership
Authorization: Bearer {token}
Content-Type: application/json

{
    "user_id": 1,
    "package_id": 1,
    "start_date": "2024-01-15",
    "end_date": "2024-02-15",
    "amount": 99.99,
    "notes": "Manual assignment"
}
```

### Update User Membership
```
PUT /admin/memberships/user-memberships/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "status": "active",
    "expires_at": "2024-03-15",
    "amount": 149.99,
    "notes": "Updated membership"
}
```

### Cancel User Membership
```
POST /admin/memberships/user-memberships/{id}/cancel
Authorization: Bearer {token}
```

## Admin Endpoints

### Get Dashboard Data
```
GET /admin/dashboard
Authorization: Bearer {token}
```

### Get Admin Index
```
GET /admin
Authorization: Bearer {token}
```

## Membership Package Endpoints

### Get All Packages
```
GET /admin/memberships
Authorization: Bearer {token}
```

### Get Package by ID
```
GET /admin/memberships/{id}
Authorization: Bearer {token}
```

### Create Package
```
POST /admin/memberships
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Premium Package",
    "description": "Premium features",
    "price": 99.99,
    "duration_days": 30,
    "features": ["Feature 1", "Feature 2"],
    "status": "active"
}
```

### Update Package
```
PUT /admin/memberships/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Updated Package",
    "price": 149.99
}
```

### Delete Package
```
DELETE /admin/memberships/{id}
Authorization: Bearer {token}
```

### Find Package by Name
```
GET /admin/memberships/find/{name}
Authorization: Bearer {token}
```

### Upload Package Image
```
POST /admin/memberships/{id}/image
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
    "image": [file]
}
```

### Delete Package Image
```
DELETE /admin/memberships/{id}/image
Authorization: Bearer {token}
```

## Query Parameters

### Pagination
```
?page=1&per_page=15
```

### Search
```
?search=keyword
```

### Filters
```
?filters[role]=user&filters[status]=active&filters[date_from]=2024-01-01&filters[date_to]=2024-12-31
```

### Sorting
```
?sort_by=created_at&sort_direction=desc
```

## Response Format

### Success Response
```json
{
    "success": true,
    "message": "Operation completed successfully",
    "timestamp": "2024-01-15T10:30:00.000000Z",
    "status_code": 200,
    "data": {
        // Response data
    }
}
```

### Error Response
```json
{
    "success": false,
    "message": "Error message",
    "timestamp": "2024-01-15T10:30:00.000000Z",
    "status_code": 400,
    "errors": {
        "field": ["Error message"]
    }
}
```

### Paginated Response
```json
{
    "success": true,
    "message": "Data retrieved successfully",
    "timestamp": "2024-01-15T10:30:00.000000Z",
    "status_code": 200,
    "data": [
        // Items array
    ],
    "pagination": {
        "current_page": 1,
        "per_page": 15,
        "total": 100,
        "last_page": 7,
        "from": 1,
        "to": 15,
        "has_more_pages": true
    }
}
```

## Authentication

All protected endpoints require a Bearer token in the Authorization header:

```
Authorization: Bearer {your_jwt_token}
```

## File Upload

For file uploads, use `multipart/form-data` content type and include the file in the request body.

## Error Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Internal Server Error

## Rate Limiting

API requests are rate limited to 60 requests per minute per IP address.

## Admin Permissions

All admin endpoints require:
- Valid JWT authentication
- Admin role (`role:admin` middleware)
- Appropriate permissions (handled by Laravel policies)

## Bulk Operations

Bulk operations support the following actions:

### User Bulk Operations
- `activate` - Activate selected users
- `deactivate` - Deactivate selected users
- `suspend` - Suspend selected users
- `delete` - Delete selected users (with safety checks)
- `change_role` - Change role for selected users
- `export` - Export selected users data

### Package Bulk Operations
- `activate` - Activate selected packages
- `deactivate` - Deactivate selected packages
- `delete` - Delete selected packages (with safety checks)
- `feature` - Feature selected packages
- `unfeature` - Unfeature selected packages

## Data Export

Export functionality supports multiple formats:
- `json` - JSON format (default)
- `csv` - CSV format
- `xml` - XML format

Export requests can include filters and field selection for customized exports.
