# Admin Module

This module provides a complete admin authentication and management system for the Arabic Academy application.

## 🏗️ **Architecture**

The Admin module follows the recommended Laravel module structure with proper versioning:

```
Modules/Admin/
├── Http/Controllers/V1/          # Versioned controllers
│   ├── AdminAuthController.php   # Authentication (login, logout, profile)
│   ├── AdminDashboardController.php # Dashboard data and analytics
│   └── AdminUserController.php   # User management for admins
├── Http/Requests/V1/             # Form request validation
│   ├── AdminLoginRequest.php     # Login validation
│   └── AdminUpdateProfileRequest.php # Profile update validation
├── Models/Admin.php               # Admin model with Spatie permissions
├── Repositories/                  # Repository pattern implementation
├── Providers/AdminServiceProvider.php # Service provider
├── routes/api.php                 # Admin API routes
└── database/                      # Database components
    ├── factories/AdminFactory.php # Admin factory for testing
    └── seeders/AdminSeeder.php   # Admin seeder
```

## 🔐 **Authentication System**

### **Guards**
- **Admin Guard**: `auth:admin` - JWT-based authentication for admin API
- **User Guard**: `auth:api` - JWT-based authentication for regular users

### **Admin Types**
- **Super Admin**: Full system access
- **Admin**: Standard admin privileges
- **Moderator**: Limited admin access

## 🚀 **Setup Instructions**

### **1. Run Migrations**
```bash
php artisan migrate
```

### **2. Seed Admin Data**
```bash
php artisan db:seed --class=Modules\\Admin\\Database\\Seeders\\AdminSeeder
```

This creates 10 admin users:
- 1 Super Admin
- 1 Admin  
- 1 Moderator
- 7 Additional admins (4 admin, 3 moderator roles)

### **3. Demo Credentials**
```
Super Admin: superadmin@arabicacademy.com / password
Admin: admin@arabicacademy.com / password
Moderator: moderator@arabicacademy.com / password
```

## 📡 **API Endpoints**

### **Public Endpoints**
```
POST /api/v1/admin/auth/login     # Admin login
POST /api/v1/admin/auth/refresh   # Refresh token
```

### **Protected Endpoints** (require `auth:admin`)
```
POST   /api/v1/admin/auth/logout          # Admin logout
GET    /api/v1/admin/auth/profile         # Get admin profile
PUT    /api/v1/admin/auth/profile         # Update admin profile
POST   /api/v1/admin/auth/avatar          # Upload avatar
DELETE /api/v1/admin/auth/avatar          # Delete avatar

GET    /api/v1/admin/dashboard/           # Dashboard overview
GET    /api/v1/admin/dashboard/statistics # Dashboard statistics
GET    /api/v1/admin/dashboard/analytics  # Analytics data
GET    /api/v1/admin/dashboard/system-logs # System logs
GET    /api/v1/admin/dashboard/notifications # Notifications
POST   /api/v1/admin/dashboard/export-data # Export data

GET    /api/v1/admin/users/               # List users
GET    /api/v1/admin/users/{user}         # Get user details
POST   /api/v1/admin/users/               # Create user
PUT    /api/v1/admin/users/{user}         # Update user
DELETE /api/v1/admin/users/{user}         # Delete user
GET    /api/v1/admin/users/statistics     # User statistics
GET    /api/v1/admin/users/activity-log   # User activity log
GET    /api/v1/admin/users/export         # Export users
```

## 🛡️ **Authorization & Policies**

### **AdminPolicy**
The `AdminPolicy` class handles authorization for admin-related actions using Spatie permissions:

- **viewAny**: View all admins
- **view**: View specific admin
- **create**: Create new admin
- **update**: Update admin (self or others)
- **delete**: Delete admin (cannot delete self)
- **manageRoles**: Manage admin roles
- **managePermissions**: Manage admin permissions
- **accessDashboard**: Access admin dashboard
- **manageSystemSettings**: Manage system settings

### **Spatie Permissions Integration**
- Uses `HasRoles` trait in Admin model
- Role-based access control
- Permission-based authorization
- Flexible role assignment

## 🎨 **Frontend Integration**

### **Next.js Admin Pages**
- `/admin/login` - Admin login page
- `/admin/dashboard` - Admin dashboard

### **Features**
- Responsive design with Tailwind CSS
- Form validation
- Error handling
- Loading states
- JWT token management
- Local storage for admin data

## 🔧 **Configuration**

### **Environment Variables**
```env
NEXT_PUBLIC_API_URL=http://localhost:8000
```

### **JWT Configuration**
```php
// config/jwt.php
'ttl' => env('JWT_TTL', 60), // Token lifetime in minutes
```

## 🧪 **Testing**

### **Admin Factory**
```php
// Create admin with specific role
Admin::factory()->active()->verified()->create()->assignRole('admin');

// Create admin with avatar
Admin::factory()->withAvatar()->create();
```

### **Admin Seeder**
```bash
php artisan db:seed --class=Modules\\Admin\\Database\\Seeders\\AdminSeeder
```

## 📚 **Best Practices Implemented**

1. **Repository Pattern**: Clean data access layer
2. **Form Request Validation**: Dedicated validation classes
3. **Policy-based Authorization**: Spatie permissions integration
4. **API Versioning**: V1 namespace for future compatibility
5. **Proper Error Handling**: Consistent error responses
6. **Logging**: Comprehensive activity logging
7. **Soft Deletes**: Data preservation
8. **Factory & Seeding**: Test data generation
9. **JWT Authentication**: Secure token-based auth
10. **Module Structure**: Organized, maintainable code

## 🔄 **Future Enhancements**

- [ ] Admin activity logging
- [ ] Two-factor authentication
- [ ] Admin session management
- [ ] Bulk operations
- [ ] Advanced analytics
- [ ] Admin audit trail
- [ ] Role-based dashboard customization

## 🐛 **Troubleshooting**

### **Common Issues**

1. **JWT Token Issues**
   - Check JWT configuration
   - Verify token expiration
   - Ensure proper guard configuration

2. **Permission Issues**
   - Verify Spatie permissions are installed
   - Check role assignments
   - Validate policy registration

3. **Database Issues**
   - Run migrations
   - Check seeder execution
   - Verify model relationships

### **Debug Commands**
```bash
# Check admin users
php artisan tinker
>>> Modules\Admin\Models\Admin::with('roles')->get();

# Check permissions
>>> Spatie\Permission\Models\Permission::all();
>>> Spatie\Permission\Models\Role::all();
```

## 📞 **Support**

For issues or questions about the Admin module:
1. Check this README
2. Review Laravel documentation
3. Check Spatie permissions documentation
4. Review module structure and policies
