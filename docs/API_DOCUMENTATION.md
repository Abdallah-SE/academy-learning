# Arabic Academy - API Documentation
**Complete API Reference for Backend Services**  
**Version**: 1.0 | **Base URL**: `https://api.arabicacademy.com`  

## 🎯 Overview

The Arabic Academy API provides a comprehensive set of endpoints for managing users, authentication, Quran progress, content management, and administrative functions. All endpoints return JSON responses and use standard HTTP status codes.

## 🔐 Authentication

### JWT Token Authentication
The API uses JWT (JSON Web Tokens) for authentication. Include the token in the Authorization header:

```
Authorization: Bearer {your_jwt_token}
```

### Token Management
- **Access Token**: Valid for 60 minutes
- **Refresh Token**: Valid for 14 days
- **Token Refresh**: Use refresh token to get new access token

## 📊 Response Format

### Success Response
```json
{
  "success": true,
  "data": {
    // Response data
  },
  "message": "Operation completed successfully",
  "timestamp": "2024-01-01T00:00:00Z"
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error description",
  "code": 400,
  "errors": {
    "field": ["Error message"]
  },
  "timestamp": "2024-01-01T00:00:00Z"
}
```

### Pagination Response
```json
{
  "success": true,
  "data": {
    "items": [],
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 100,
      "last_page": 7,
      "from": 1,
      "to": 15
    }
  }
}
```

## 🔑 Authentication Endpoints

### User Registration
```http
POST /api/auth/register
```

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "SecurePassword123",
  "password_confirmation": "SecurePassword123",
  "phone": "+1234567890",
  "role": "student",
  "country": "United States",
  "timezone": "America/New_York"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "student",
      "status": "active",
      "email_verified_at": null
    },
    "message": "Registration successful. Please check your email for verification."
  }
}
```

**Validation Rules:**
- `name`: Required, string, max 255 characters
- `email`: Required, email, unique
- `password`: Required, string, min 8 characters, mixed case + numbers
- `phone`: Optional, string, max 20 characters
- `role`: Required, enum: student, teacher, admin
- `country`: Optional, string, max 100 characters
- `timezone`: Optional, string, valid timezone

### User Login
```http
POST /api/auth/login
```

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "SecurePassword123"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "student"
    },
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "bearer",
    "expires_in": 3600
  }
}
```

### Refresh Token
```http
POST /api/auth/refresh
```

**Request Body:**
```json
{
  "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "bearer",
    "expires_in": 3600
  }
}
```

### User Logout
```http
POST /api/auth/logout
```

**Headers:**
```
Authorization: Bearer {access_token}
```

**Response:**
```json
{
  "success": true,
  "message": "Successfully logged out"
}
```

### Email Verification
```http
POST /api/auth/verify-email
```

**Request Body:**
```json
{
  "token": "verification_token_from_email"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Email verified successfully"
}
```

### Forgot Password
```http
POST /api/auth/forgot-password
```

**Request Body:**
```json
{
  "email": "john@example.com"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Password reset link sent to your email"
}
```

### Reset Password
```http
POST /api/auth/reset-password
```

**Request Body:**
```json
{
  "token": "reset_token_from_email",
  "email": "john@example.com",
  "password": "NewPassword123",
  "password_confirmation": "NewPassword123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Password reset successfully"
}
```

## 👤 User Management Endpoints

### Get Current User Profile
```http
GET /api/users/profile
```

**Headers:**
```
Authorization: Bearer {access_token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "+1234567890",
      "role": "student",
      "status": "active",
      "avatar": "https://example.com/avatars/user1.jpg",
      "date_of_birth": "1990-01-01",
      "gender": "male",
      "country": "United States",
      "timezone": "America/New_York",
      "preferences": {
        "language": "en",
        "theme": "light",
        "notifications": true
      },
      "email_verified_at": "2024-01-01T00:00:00Z",
      "whatsapp_verified": false,
      "created_at": "2024-01-01T00:00:00Z",
      "updated_at": "2024-01-01T00:00:00Z"
    }
  }
}
```

### Update User Profile
```http
PUT /api/users/profile
```

**Headers:**
```
Authorization: Bearer {access_token}
```

**Request Body:**
```json
{
  "name": "John Smith",
  "phone": "+1234567890",
  "country": "Canada",
  "timezone": "America/Toronto",
  "preferences": {
    "language": "ar",
    "theme": "dark",
    "notifications": false
  }
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "John Smith",
      "phone": "+1234567890",
      "country": "Canada",
      "timezone": "America/Toronto",
      "preferences": {
        "language": "ar",
        "theme": "dark",
        "notifications": false
      }
    },
    "message": "Profile updated successfully"
  }
}
```

### Upload Avatar
```http
POST /api/users/avatar
```

**Headers:**
```
Authorization: Bearer {access_token}
Content-Type: multipart/form-data
```

**Request Body:**
```
avatar: [image file]
```

**Response:**
```json
{
  "success": true,
  "data": {
    "avatar_url": "https://example.com/avatars/user1_new.jpg",
    "message": "Avatar uploaded successfully"
  }
}
```

### WhatsApp Verification
```http
POST /api/users/verify-whatsapp
```

**Headers:**
```
Authorization: Bearer {access_token}
```

**Request Body:**
```json
{
  "phone": "+1234567890",
  "verification_code": "123456"
}
```

**Response:**
```json
{
  "success": true,
  "message": "WhatsApp verified successfully"
}
```

## 🔐 Role & Permission Endpoints

### Get All Roles
```http
GET /api/roles
```

**Headers:**
```
Authorization: Bearer {access_token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "roles": [
      {
        "id": 1,
        "name": "Student",
        "slug": "student",
        "description": "Basic student access",
        "level": 1,
        "permissions": [
          {
            "id": 1,
            "name": "View Quran Lessons",
            "slug": "quran.view"
          }
        ]
      }
    ]
  }
}
```

### Get Role Details
```http
GET /api/roles/{id}
```

**Headers:**
```
Authorization: Bearer {access_token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "role": {
      "id": 1,
      "name": "Student",
      "slug": "student",
      "description": "Basic student access",
      "level": 1,
      "permissions": [],
      "created_at": "2024-01-01T00:00:00Z",
      "updated_at": "2024-01-01T00:00:00Z"
    }
  }
}
```

### Get All Permissions
```http
GET /api/permissions
```

**Headers:**
```
Authorization: Bearer {access_token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "permissions": [
      {
        "id": 1,
        "name": "View Quran Lessons",
        "slug": "quran.view",
        "description": "Can view Quran lessons",
        "module": "quran"
      }
    ]
  }
}
```

## 📚 Quran Progress Endpoints

### Get All Surahs
```http
GET /api/quran/surahs
```

**Headers:**
```
Authorization: Bearer {access_token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "surahs": [
      {
        "id": 1,
        "name_arabic": "الفاتحة",
        "name_english": "Al-Fatiha",
        "name_transliteration": "Al-Fatiha",
        "number": 1,
        "verses_count": 7,
        "revelation_type": "Meccan"
      }
    ]
  }
}
```

### Get Surah Details
```http
GET /api/quran/surahs/{id}
```

**Headers:**
```
Authorization: Bearer {access_token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "surah": {
      "id": 1,
      "name_arabic": "الفاتحة",
      "name_english": "Al-Fatiha",
      "name_transliteration": "Al-Fatiha",
      "number": 1,
      "verses_count": 7,
      "revelation_type": "Meccan",
      "verses": [
        {
          "id": 1,
          "verse_number": 1,
          "text_arabic": "بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ",
          "text_translation": "In the name of Allah, the Entirely Merciful, the Especially Merciful"
        }
      ]
    }
  }
}
```

### Get User Progress
```http
GET /api/quran/progress
```

**Headers:**
```
Authorization: Bearer {access_token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "progress": [
      {
        "id": 1,
        "surah": {
          "id": 1,
          "name_english": "Al-Fatiha"
        },
        "memorization_status": "completed",
        "verses_memorized": 7,
        "total_verses": 7,
        "last_assessment_date": "2024-01-01T00:00:00Z",
        "teacher_notes": "Excellent memorization"
      }
    ]
  }
}
```

### Update Progress
```http
PUT /api/quran/progress/{id}
```

**Headers:**
```
Authorization: Bearer {access_token}
```

**Request Body:**
```json
{
  "memorization_status": "in_progress",
  "verses_memorized": 5,
  "teacher_notes": "Good progress, needs more practice"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "progress": {
      "id": 1,
      "memorization_status": "in_progress",
      "verses_memorized": 5,
      "teacher_notes": "Good progress, needs more practice"
    },
    "message": "Progress updated successfully"
  }
}
```

## 📱 Content Management Endpoints

### Get Activities
```http
GET /api/content/activities
```

**Headers:**
```
Authorization: Bearer {access_token}
```

**Query Parameters:**
- `type`: Filter by content type (video, image, document, link)
- `module`: Filter by module (quran, arabic, islamic)
- `status`: Filter by status (draft, published, archived)
- `page`: Page number for pagination
- `per_page`: Items per page (default: 15)

**Response:**
```json
{
  "success": true,
  "data": {
    "activities": [
      {
        "id": 1,
        "title": "Quran Recitation Lesson 1",
        "description": "Learn proper Quran recitation",
        "type": "video",
        "content_url": "https://example.com/video1.mp4",
        "thumbnail": "https://example.com/thumb1.jpg",
        "scheduled_at": "2024-01-01T10:00:00Z",
        "expires_at": "2024-01-31T23:59:59Z",
        "teacher": {
          "id": 2,
          "name": "Teacher Name"
        },
        "module": "quran",
        "status": "published"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 50,
      "last_page": 4
    }
  }
}
```

### Create Activity (Teachers Only)
```http
POST /api/content/activities
```

**Headers:**
```
Authorization: Bearer {access_token}
```

**Request Body:**
```json
{
  "title": "New Lesson",
  "description": "Lesson description",
  "type": "video",
  "content_url": "https://example.com/video.mp4",
  "thumbnail": "https://example.com/thumb.jpg",
  "scheduled_at": "2024-01-01T10:00:00Z",
  "expires_at": "2024-01-31T23:59:59Z",
  "module_id": 1
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "activity": {
      "id": 2,
      "title": "New Lesson",
      "description": "Lesson description",
      "type": "video",
      "content_url": "https://example.com/video.mp4",
      "status": "draft"
    },
    "message": "Activity created successfully"
  }
}
```

### Upload Media
```http
POST /api/content/upload
```

**Headers:**
```
Authorization: Bearer {access_token}
Content-Type: multipart/form-data
```

**Request Body:**
```
file: [media file]
type: video|image|document
module_id: 1
```

**Response:**
```json
{
  "success": true,
  "data": {
    "file_url": "https://example.com/uploads/video.mp4",
    "file_name": "video.mp4",
    "file_size": 1048576,
    "file_type": "video/mp4"
  }
}
```

## 💳 Membership Endpoints

### Get Membership Packages
```http
GET /api/membership/packages
```

**Response:**
```json
{
  "success": true,
  "data": {
    "packages": [
      {
        "id": 1,
        "name": "Basic Package",
        "description": "Access to basic features",
        "price": "19.99",
        "duration_days": 30,
        "features": {
          "quran_access": true,
          "arabic_lessons": false,
          "islamic_studies": false,
          "homework_support": false,
          "meeting_access": false,
          "progress_tracking": true
        },
        "status": "active"
      }
    ]
  }
}
```

### Subscribe to Package
```http
POST /api/membership/subscribe
```

**Headers:**
```
Authorization: Bearer {access_token}
```

**Request Body:**
```json
{
  "package_id": 1,
  "payment_method": "paypal"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "membership": {
      "id": 1,
      "package": {
        "name": "Basic Package"
      },
      "start_date": "2024-01-01",
      "end_date": "2024-01-31",
      "status": "active",
      "payment_status": "pending"
    },
    "message": "Subscription created successfully"
  }
}
```

### Get User Membership Status
```http
GET /api/membership/status
```

**Headers:**
```
Authorization: Bearer {access_token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "membership": {
      "id": 1,
      "package": {
        "name": "Basic Package",
        "features": {
          "quran_access": true,
          "progress_tracking": true
        }
      },
      "start_date": "2024-01-01",
      "end_date": "2024-01-31",
      "status": "active",
      "days_remaining": 15
    }
  }
}
```

## 🎓 Homework Endpoints

### Get Homework Assignments
```http
GET /api/homework/assignments
```

**Headers:**
```
Authorization: Bearer {access_token}
```

**Query Parameters:**
- `status`: Filter by status (active, completed, expired)
- `module_id`: Filter by module
- `page`: Page number
- `per_page`: Items per page

**Response:**
```json
{
  "success": true,
  "data": {
    "assignments": [
      {
        "id": 1,
        "title": "Quran Memorization Assignment",
        "description": "Memorize verses 1-5 of Al-Baqarah",
        "due_date": "2024-01-15T23:59:59Z",
        "teacher": {
          "id": 2,
          "name": "Teacher Name"
        },
        "module": "quran",
        "status": "active",
        "attachments": [
          {
            "name": "assignment.pdf",
            "url": "https://example.com/files/assignment.pdf"
          }
        ]
      }
    ]
  }
}
```

### Submit Homework
```http
POST /api/homework/submissions
```

**Headers:**
```
Authorization: Bearer {access_token}
```

**Request Body:**
```json
{
  "assignment_id": 1,
  "content": "I have completed the memorization assignment",
  "attachments": [
    {
      "name": "recording.mp3",
      "url": "https://example.com/files/recording.mp3"
    }
  ]
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "submission": {
      "id": 1,
      "assignment_id": 1,
      "content": "I have completed the memorization assignment",
      "status": "submitted",
      "submission_date": "2024-01-10T00:00:00Z"
    },
    "message": "Homework submitted successfully"
  }
}
```

## 🏠 Landing Page Endpoints

### Get Public Services
```http
GET /api/public/services
```

**Response:**
```json
{
  "success": true,
  "data": {
    "services": [
      {
        "id": 1,
        "name": "Quran Memorization",
        "description": "Learn to memorize the Quran with expert teachers",
        "icon": "quran-icon.svg",
        "features": [
          "Personalized learning plan",
          "Progress tracking",
          "Expert teacher guidance"
        ]
      }
    ]
  }
}
```

### Get Public Packages
```http
GET /api/public/packages
```

**Response:**
```json
{
  "success": true,
  "data": {
    "packages": [
      {
        "id": 1,
        "name": "Basic Package",
        "description": "Perfect for beginners",
        "price": "19.99",
        "duration": "30 days",
        "features": [
          "Quran access",
          "Progress tracking",
          "Basic support"
        ]
      }
    ]
  }
}
```

### Get News and Updates
```http
GET /api/public/news
```

**Query Parameters:**
- `category`: Filter by category (announcements, updates, events)
- `page`: Page number
- `per_page`: Items per page

**Response:**
```json
{
  "success": true,
  "data": {
    "news": [
      {
        "id": 1,
        "title": "New Quran Teacher Joins Platform",
        "content": "We're excited to welcome...",
        "category": "announcements",
        "published_at": "2024-01-01T00:00:00Z",
        "image": "https://example.com/news1.jpg"
      }
    ]
  }
}
```

## 🔧 Admin Endpoints

### Get All Users (Admin Only)
```http
GET /api/admin/users
```

**Headers:**
```
Authorization: Bearer {access_token}
```

**Query Parameters:**
- `role`: Filter by role
- `status`: Filter by status
- `search`: Search by name or email
- `page`: Page number
- `per_page`: Items per page

**Response:**
```json
{
  "success": true,
  "data": {
    "users": [
      {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "role": "student",
        "status": "active",
        "created_at": "2024-01-01T00:00:00Z",
        "last_login": "2024-01-01T00:00:00Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 100,
      "last_page": 7
    }
  }
}
```

### Update User Role (Admin Only)
```http
PUT /api/admin/users/{id}/role
```

**Headers:**
```
Authorization: Bearer {access_token}
```

**Request Body:**
```json
{
  "role": "teacher"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "role": "teacher"
    },
    "message": "User role updated successfully"
  }
}
```

### Update User Status (Admin Only)
```http
PUT /api/admin/users/{id}/status
```

**Headers:**
```
Authorization: Bearer {access_token}
```

**Request Body:**
```json
{
  "status": "suspended",
  "reason": "Violation of terms of service"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "status": "suspended"
    },
    "message": "User status updated successfully"
  }
}
```

### Verify Payment (Admin Only)
```http
POST /api/admin/payments/verify
```

**Headers:**
```
Authorization: Bearer {access_token}
```

**Request Body:**
```json
{
  "membership_id": 1,
  "verification_status": "approved",
  "notes": "Payment received via WhatsApp"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "membership": {
      "id": 1,
      "payment_status": "completed",
      "admin_verified": true,
      "verified_at": "2024-01-01T00:00:00Z"
    },
    "message": "Payment verified successfully"
  }
}
```

## 📊 Analytics Endpoints

### Get User Progress Analytics
```http
GET /api/analytics/progress
```

**Headers:**
```
Authorization: Bearer {access_token}
```

**Query Parameters:**
- `user_id`: Specific user ID (admin only)
- `date_from`: Start date for range
- `date_to`: End date for range
- `module_id`: Filter by module

**Response:**
```json
{
  "success": true,
  "data": {
    "analytics": {
      "total_surahs": 114,
      "completed_surahs": 15,
      "in_progress_surahs": 3,
      "completion_percentage": 13.16,
      "progress_chart": [
        {
          "date": "2024-01-01",
          "verses_memorized": 10
        }
      ]
    }
  }
}
```

### Get Teacher Performance Analytics
```http
GET /api/analytics/teacher-performance
```

**Headers:**
```
Authorization: Bearer {access_token}
```

**Query Parameters:**
- `teacher_id`: Specific teacher ID
- `date_from`: Start date for range
- `date_to`: End date for range

**Response:**
```json
{
  "success": true,
  "data": {
    "analytics": {
      "total_students": 25,
      "active_students": 20,
      "average_progress": 75.5,
      "student_progress": [
        {
          "student_id": 1,
          "student_name": "John Doe",
          "progress_percentage": 80,
          "last_assessment": "2024-01-01T00:00:00Z"
        }
      ]
    }
  }
}
```

## 🚨 Error Handling

### HTTP Status Codes
- **200**: Success
- **201**: Created
- **400**: Bad Request
- **401**: Unauthorized
- **403**: Forbidden
- **404**: Not Found
- **422**: Validation Error
- **429**: Too Many Requests
- **500**: Internal Server Error

### Common Error Responses

#### Validation Error (422)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  },
  "code": 422
}
```

#### Unauthorized (401)
```json
{
  "success": false,
  "message": "Unauthenticated",
  "code": 401
}
```

#### Forbidden (403)
```json
{
  "success": false,
  "message": "Insufficient permissions",
  "code": 403
}
```

#### Not Found (404)
```json
{
  "success": false,
  "message": "Resource not found",
  "code": 404
}
```

## 📝 Rate Limiting

### API Rate Limits
- **Authentication endpoints**: 5 requests per minute
- **User management**: 60 requests per hour
- **Content endpoints**: 100 requests per hour
- **Analytics endpoints**: 30 requests per hour

### Rate Limit Headers
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
X-RateLimit-Reset: 1640995200
```

## 🔒 Security

### Authentication
- JWT tokens with secure expiration
- Refresh token rotation
- Secure token storage

### Authorization
- Role-based access control
- Permission-based endpoints
- Resource ownership validation

### Data Protection
- Input validation and sanitization
- SQL injection prevention
- XSS protection
- CSRF protection

### HTTPS
- All endpoints require HTTPS
- SSL certificate validation
- Secure cookie settings

## 📱 SDKs and Libraries

### PHP/Laravel
```bash
composer require arabic-academy/api-client
```

### JavaScript/Node.js
```bash
npm install arabic-academy-api-client
```

### Python
```bash
pip install arabic-academy-api-client
```

## 🧪 Testing

### Test Environment
- **Base URL**: `https://staging-api.arabicacademy.com`
- **Test Data**: Available for all endpoints
- **Rate Limits**: Increased for testing

### Postman Collection
Download the complete Postman collection for testing all endpoints:
[Arabic Academy API Collection](https://example.com/postman-collection.json)

## 📞 Support

### API Support
- **Email**: api-support@arabicacademy.com
- **Documentation**: [docs.arabicacademy.com](https://docs.arabicacademy.com)
- **Status Page**: [status.arabicacademy.com](https://status.arabicacademy.com)

### Developer Resources
- **GitHub**: [github.com/arabic-academy/api](https://github.com/arabic-academy/api)
- **Issues**: [github.com/arabic-academy/api/issues](https://github.com/arabic-academy/api/issues)
- **Discussions**: [github.com/arabic-academy/api/discussions](https://github.com/arabic-academy/api/discussions)

---

**This API documentation is updated regularly. Check the version number and last updated date for the most current information.**
