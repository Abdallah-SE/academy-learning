# Arabic Academy - Learning Management Platform

A comprehensive online platform for teaching Arabic, Quran memorization, and Islamic studies with progress tracking and interactive learning features.

## 🎯 Project Overview

The Arabic Academy is designed to provide a modern, accessible platform for Islamic education, combining traditional learning methods with contemporary technology. The platform serves three main user types: students, teachers, and administrators.

### Core Services

1. **Quran Memorization & Progress Tracking**
   - Surah-based learning progression
   - Teacher assessment and verification
   - Visual progress charts and analytics

2. **Arabic Language Learning**
   - Beginner-friendly curriculum
   - Interactive lessons for kids and beginners
   - Skill assessment and evaluation

3. **Islamic Studies**
   - Comprehensive Islamic topics
   - Age-appropriate content
   - Cultural integration

## 🚀 Features

### User Management
- **Registration & Authentication**: Secure user registration with email verification
- **Role-based Access**: Student, Teacher, and Admin roles with appropriate permissions
- **Profile Management**: Comprehensive user profiles with preferences
- **WhatsApp Verification**: Alternative verification method for users

### Learning Tools
- **Progress Tracking**: Monitor student advancement through Quran and lessons
- **Assessment System**: Teacher evaluation and testing capabilities
- **Homework Management**: Assignment creation, submission, and grading
- **Meeting Integration**: In-platform and external meeting support

### Content Management
- **Activity Publishing**: Teachers can publish videos, images, and links
- **Scheduled Content**: Time-based content availability
- **Media Library**: Downloadable and explorable content
- **Content Scheduling**: Automated display management

### Membership System
- **Package Tiers**: Multiple membership levels (Basic, Standard, Premium)
- **Payment Options**: PayPal integration and WhatsApp payment
- **Admin Verification**: Manual payment confirmation system
- **Access Control**: Feature restrictions based on membership level

### Analytics & Reporting
- **Progress Charts**: Visual representation of student advancement
- **Performance Metrics**: Comprehensive learning analytics
- **Teacher Insights**: Detailed student progress reports

## 🏗️ Technical Architecture

### Backend (Laravel 10)
- **API-First Design**: RESTful API architecture
- **Modular Structure**: Laravel modules for scalability
- **Authentication**: JWT-based secure authentication
- **Database**: MySQL with migrations and seeders
- **Testing**: Comprehensive unit and feature tests

### Frontend (Next.js)
- **Modern UI/UX**: Responsive and accessible design
- **Component-based**: Reusable and maintainable code
- **State Management**: Efficient data handling
- **Performance**: Optimized loading and rendering

### Database Design
- **Users & Profiles**: Comprehensive user information management
- **Memberships**: Subscription and payment tracking
- **Progress Tracking**: Student advancement monitoring
- **Content Management**: Media and activity organization

## 📋 Sprint Planning (Agile Scrum)

### Sprint 1: Core Authentication & User Management ✅
- [x] Custom exception handling system
- [x] Enhanced user model with roles and profiles
- [x] Database migrations and schema
- [x] User factories and seeders
- [x] Unit tests for models
- [ ] JWT authentication implementation
- [ ] API endpoints for authentication
- [ ] Email verification system

### Sprint 2: User Roles & Permissions (Next)
- [ ] Role-based access control
- [ ] Permission management system
- [ ] Admin user creation
- [ ] User status management

### Sprint 3: Quran Progress Tracking
- [ ] Surah management system
- [ ] Student progress tracking
- [ ] Teacher assessment tools
- [ ] Progress visualization

### Sprint 4: Content Management
- [ ] Media upload system
- [ ] Content scheduling
- [ ] Activity publishing
- [ ] Content organization

### Sprint 5: Membership & Payment
- [ ] PayPal integration
- [ ] WhatsApp payment verification
- [ ] Admin payment verification
- [ ] Access control implementation

## 🛠️ Installation & Setup

### Prerequisites
- PHP 8.1+
- Composer
- MySQL 8.0+
- Node.js 18+
- npm or yarn

### Backend Setup
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

### Frontend Setup
```bash
cd frontend
npm install
npm run dev
```

### Environment Configuration
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=arabic_academy
DB_USERNAME=your_username
DB_PASSWORD=your_password

JWT_SECRET=your_jwt_secret
JWT_TTL=60
JWT_REFRESH_TTL=20160

MAIL_MAILER=smtp
MAIL_HOST=your_mail_host
MAIL_PORT=587
MAIL_USERNAME=your_mail_username
MAIL_PASSWORD=your_mail_password
MAIL_ENCRYPTION=tls
```

## 🧪 Testing

### Running Tests
```bash
# Backend tests
cd backend
php artisan test

# Frontend tests
cd frontend
npm test
```

### Test Coverage
- **Backend**: 90%+ code coverage target
- **API Endpoints**: 100% endpoint coverage
- **Critical Paths**: 100% business logic coverage

## 📚 API Documentation

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

## 🔒 Security Features

- **JWT Authentication**: Secure token-based authentication
- **Role-based Access Control**: Granular permissions
- **Input Validation**: Comprehensive data sanitization
- **Rate Limiting**: API abuse prevention
- **Data Encryption**: Sensitive data protection
- **Audit Logging**: System activity tracking

## 📊 Performance Requirements

- **API Response Time**: < 200ms average
- **Database Queries**: < 100ms average
- **File Uploads**: < 5MB/s upload speed
- **Page Load**: < 3 seconds initial load
- **Concurrent Users**: Support 1000+ simultaneous users

## 🚀 Deployment

### Production Environment
- **Web Server**: Nginx with PHP-FPM
- **Database**: MySQL 8.0+
- **Cache**: Redis for session and data caching
- **File Storage**: Local storage with backup strategy
- **SSL**: HTTPS encryption for all communications

### CI/CD Pipeline
- **Automated Testing**: Run tests on every commit
- **Code Quality**: Static analysis and linting
- **Automated Deployment**: Deploy to staging/production
- **Monitoring**: Application performance monitoring

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 📞 Support

For support and questions:
- **Email**: support@arabicacademy.com
- **WhatsApp**: +1-234-567-8900
- **Documentation**: [docs/](docs/) directory

## 🙏 Acknowledgments

- Laravel team for the excellent framework
- Next.js team for the React framework
- All contributors and supporters of this project

---

**Built with ❤️ for the Islamic community**
