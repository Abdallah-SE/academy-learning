# Arabic Academy - Technical Specifications

## System Architecture

### Backend Architecture (Laravel 10)
```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │   API Gateway   │    │   Laravel       │
│   (Next.js)     │◄──►│   (Laravel)     │◄──►│   Modules      │
└─────────────────┘    └─────────────────┘    └─────────────────┘
                                │
                       ┌─────────────────┐
                       │   Database      │
                       │   (MySQL)       │
                       └─────────────────┘
```

### Module Structure
- **Core Module**: Base functionality and shared services
- **Auth Module**: Authentication and authorization
- **User Module**: User management and profiles
- **Admin Module**: Administrative functions
- **Quran Module**: Quran memorization and progress
- **Arabic Module**: Arabic language learning
- **Islamic Module**: Islamic studies content
- **Content Module**: Media and activity management
- **Payment Module**: Membership and payment processing

## Database Schema

### Core Tables
```sql
-- Users table with role-based access
users
├── id (primary key)
├── name
├── email (unique)
├── email_verified_at
├── phone
├── whatsapp_verified
├── role (student, teacher, admin)
├── status (active, inactive, suspended)
├── created_at
└── updated_at

-- User profiles with additional information
user_profiles
├── id (primary key)
├── user_id (foreign key)
├── avatar
├── date_of_birth
├── gender
├── country
├── timezone
├── preferences (JSON)
└── created_at

-- Membership packages
membership_packages
├── id (primary key)
├── name
├── description
├── price
├── duration_days
├── features (JSON)
├── status (active, inactive)
└── created_at

-- User memberships
user_memberships
├── id (primary key)
├── user_id (foreign key)
├── package_id (foreign key)
├── start_date
├── end_date
├── status (active, expired, cancelled)
├── payment_method
├── payment_status
├── admin_verified
└── created_at

-- Quran surahs
quran_surahs
├── id (primary key)
├── name_arabic
├── name_english
├── name_transliteration
├── number
├── verses_count
├── revelation_type
└── created_at

-- Student progress tracking
student_progress
├── id (primary key)
├── user_id (foreign key)
├── surah_id (foreign key)
├── memorization_status (not_started, in_progress, completed)
├── verses_memorized
├── total_verses
├── last_assessment_date
├── teacher_notes
└── created_at

-- Progress assessments
progress_assessments
├── id (primary key)
├── student_progress_id (foreign key)
├── teacher_id (foreign key)
├── assessment_date
├── score
├── feedback
├── status (pending, completed, failed)
└── created_at

-- Content activities
content_activities
├── id (primary key)
├── title
├── description
├── type (video, image, document, link)
├── content_url
├── thumbnail
├── scheduled_at
├── expires_at
├── teacher_id (foreign key)
├── module_id (foreign key)
├── status (draft, published, archived)
└── created_at

-- Homework assignments
homework_assignments
├── id (primary key)
├── title
├── description
├── due_date
├── teacher_id (foreign key)
├── module_id (foreign key)
├── attachments (JSON)
├── status (active, completed, expired)
└── created_at

-- Homework submissions
homework_submissions
├── id (primary key)
├── assignment_id (foreign key)
├── student_id (foreign key)
├── submission_date
├── content
├── attachments (JSON)
├── grade
├── feedback
├── status (submitted, graded, late)
└── created_at

-- Meetings
meetings
├── id (primary key)
├── title
├── description
├── meeting_date
├── duration_minutes
├── meeting_type (internal, external)
├── meeting_url
├── teacher_id (foreign key)
├── max_participants
├── status (scheduled, active, completed, cancelled)
└── created_at

-- Meeting participants
meeting_participants
├── id (primary key)
├── meeting_id (foreign key)
├── user_id (foreign key)
├── role (host, participant)
├── joined_at
├── left_at
└── created_at
```

## API Endpoints

### Authentication Endpoints
```
POST   /api/auth/register          - User registration
POST   /api/auth/login             - User login
POST   /api/auth/logout            - User logout
POST   /api/auth/refresh           - Refresh JWT token
POST   /api/auth/verify-email      - Email verification
POST   /api/auth/forgot-password   - Password reset request
POST   /api/auth/reset-password    - Password reset
```

### User Management Endpoints
```
GET    /api/users                  - Get users (admin only)
GET    /api/users/{id}             - Get user profile
PUT    /api/users/{id}             - Update user profile
DELETE /api/users/{id}             - Delete user (admin only)
POST   /api/users/verify-whatsapp  - WhatsApp verification
GET    /api/users/profile          - Get current user profile
PUT    /api/users/profile          - Update current user profile
```

### Quran Progress Endpoints
```
GET    /api/quran/surahs           - Get all surahs
GET    /api/quran/surahs/{id}     - Get specific surah
GET    /api/quran/progress         - Get user progress
POST   /api/quran/progress         - Create progress entry
PUT    /api/quran/progress/{id}    - Update progress
GET    /api/quran/assessments      - Get assessments
POST   /api/quran/assessments      - Create assessment
PUT    /api/quran/assessments/{id} - Update assessment
```

### Content Management Endpoints
```
GET    /api/content/activities     - Get activities
POST   /api/content/activities     - Create activity (teacher)
PUT    /api/content/activities/{id} - Update activity
DELETE /api/content/activities/{id} - Delete activity
GET    /api/content/scheduled      - Get scheduled content
POST   /api/content/upload         - Upload media
```

### Homework Endpoints
```
GET    /api/homework/assignments   - Get assignments
POST   /api/homework/assignments   - Create assignment (teacher)
PUT    /api/homework/assignments/{id} - Update assignment
DELETE /api/homework/assignments/{id} - Delete assignment
GET    /api/homework/submissions   - Get submissions
POST   /api/homework/submissions   - Submit homework
PUT    /api/homework/submissions/{id} - Grade submission (teacher)
```

### Meeting Endpoints
```
GET    /api/meetings               - Get meetings
POST   /api/meetings               - Create meeting (teacher)
PUT    /api/meetings/{id}          - Update meeting
DELETE /api/meetings/{id}          - Delete meeting
POST   /api/meetings/{id}/join     - Join meeting
POST   /api/meetings/{id}/leave    - Leave meeting
GET    /api/meetings/{id}/participants - Get participants
```

### Membership Endpoints
```
GET    /api/membership/packages    - Get packages
POST   /api/membership/subscribe   - Subscribe to package
GET    /api/membership/status      - Get user membership status
POST   /api/membership/verify      - Verify payment (admin)
```

## Security Implementation

### Authentication & Authorization
- **JWT Tokens**: Secure token-based authentication
- **Role-based Access Control**: Granular permissions
- **API Rate Limiting**: Prevent abuse and attacks
- **Input Validation**: Sanitize all user inputs
- **CORS Configuration**: Secure cross-origin requests

### Data Protection
- **Password Hashing**: Bcrypt with salt
- **Data Encryption**: Sensitive data encryption at rest
- **Audit Logging**: Track all system activities
- **GDPR Compliance**: Data privacy and user rights

## Testing Strategy

### Test Types
1. **Unit Tests**: Individual component testing
2. **Feature Tests**: API endpoint testing
3. **Integration Tests**: Module interaction testing
4. **Performance Tests**: Load and stress testing

### Test Coverage Goals
- **Backend**: 90%+ code coverage
- **API Endpoints**: 100% endpoint coverage
- **Critical Paths**: 100% business logic coverage

### Testing Tools
- **PHPUnit**: Backend testing framework
- **Laravel Testing**: Built-in testing utilities
- **Database Factories**: Test data generation
- **Mocking**: External service simulation

## Performance Requirements

### Response Times
- **API Endpoints**: < 200ms average response time
- **Database Queries**: < 100ms average query time
- **File Uploads**: < 5MB/s upload speed
- **Page Load**: < 3 seconds initial load

### Scalability
- **Concurrent Users**: Support 1000+ simultaneous users
- **Database**: Handle 10,000+ records efficiently
- **File Storage**: Support 100GB+ media content
- **Caching**: Redis-based caching for performance

## Deployment & DevOps

### Environment Setup
- **Development**: Local development environment
- **Staging**: Pre-production testing environment
- **Production**: Live production environment

### CI/CD Pipeline
- **Automated Testing**: Run tests on every commit
- **Code Quality**: Static analysis and linting
- **Automated Deployment**: Deploy to staging/production
- **Monitoring**: Application performance monitoring

### Infrastructure
- **Web Server**: Nginx with PHP-FPM
- **Database**: MySQL 8.0+
- **Cache**: Redis for session and data caching
- **File Storage**: Local storage with backup strategy
- **SSL**: HTTPS encryption for all communications
