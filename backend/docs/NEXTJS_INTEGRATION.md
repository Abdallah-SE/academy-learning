# Next.js Integration Guide

## Overview

This guide explains how to integrate your Laravel backend with a Next.js frontend application.

## Prerequisites

- Laravel backend running on `http://localhost:8000`
- Next.js frontend running on `http://localhost:3000`
- JWT authentication configured

## Environment Setup

### Laravel (.env)
```env
# API Configuration
API_VERSION=v1
API_PREFIX=api
API_RATE_LIMITING=true
API_RATE_LIMIT_MAX_ATTEMPTS=60
API_RATE_LIMIT_DECAY_MINUTES=1

# CORS Configuration
CORS_ALLOWED_ORIGINS=http://localhost:3000,http://127.0.0.1:3000

# JWT Configuration
JWT_SECRET=your_jwt_secret_here
JWT_TTL=60
JWT_REFRESH_TTL=20160

# File Upload
FILE_UPLOAD_MAX_SIZE=5120
FILE_STORAGE_DISK=public
```

### Next.js (.env.local)
```env
NEXT_PUBLIC_API_URL=http://localhost:8000/api
NEXT_PUBLIC_APP_URL=http://localhost:3000
```

## API Client Setup

### Create API Client (Next.js)

```typescript
// lib/api-client.ts
class ApiClient {
  private baseURL: string;
  private token: string | null;

  constructor() {
    this.baseURL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api';
    this.token = typeof window !== 'undefined' ? localStorage.getItem('token') : null;
  }

  private async request(endpoint: string, options: RequestInit = {}) {
    const url = `${this.baseURL}${endpoint}`;
    
    const config: RequestInit = {
      headers: {
        'Content-Type': 'application/json',
        ...options.headers,
      },
      ...options,
    };

    if (this.token) {
      config.headers = {
        ...config.headers,
        Authorization: `Bearer ${this.token}`,
      };
    }

    try {
      const response = await fetch(url, config);
      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || 'API request failed');
      }

      return data;
    } catch (error) {
      console.error('API Error:', error);
      throw error;
    }
  }

  // Auth methods
  async login(credentials: { email: string; password: string }) {
    const response = await this.request('/auth/login', {
      method: 'POST',
      body: JSON.stringify(credentials),
    });
    
    if (response.success && response.data.token) {
      this.token = response.data.token;
      localStorage.setItem('token', response.data.token);
    }
    
    return response;
  }

  async register(userData: { name: string; email: string; password: string; password_confirmation: string }) {
    const response = await this.request('/auth/register', {
      method: 'POST',
      body: JSON.stringify(userData),
    });
    
    if (response.success && response.data.token) {
      this.token = response.data.token;
      localStorage.setItem('token', response.data.token);
    }
    
    return response;
  }

  async logout() {
    try {
      await this.request('/auth/logout', { method: 'POST' });
    } finally {
      this.token = null;
      localStorage.removeItem('token');
    }
  }

  async getCurrentUser() {
    return this.request('/auth/me');
  }

  // User methods
  async getUsers() {
    return this.request('/users');
  }

  async getUser(id: number) {
    return this.request(`/users/${id}`);
  }

  async createUser(userData: any) {
    return this.request('/users', {
      method: 'POST',
      body: JSON.stringify(userData),
    });
  }

  async updateUser(id: number, userData: any) {
    return this.request(`/users/${id}`, {
      method: 'PUT',
      body: JSON.stringify(userData),
    });
  }

  async deleteUser(id: number) {
    return this.request(`/users/${id}`, {
      method: 'DELETE',
    });
  }

  async uploadAvatar(file: File) {
    const formData = new FormData();
    formData.append('avatar', file);

    return this.request('/users/avatar', {
      method: 'POST',
      headers: {}, // Let browser set Content-Type for FormData
      body: formData,
    });
  }

  // Admin methods
  async getDashboard() {
    return this.request('/admin/dashboard');
  }

  // Membership methods
  async getMemberships() {
    return this.request('/admin/memberships');
  }

  async getMembership(id: number) {
    return this.request(`/admin/memberships/${id}`);
  }

  async createMembership(membershipData: any) {
    return this.request('/admin/memberships', {
      method: 'POST',
      body: JSON.stringify(membershipData),
    });
  }

  async updateMembership(id: number, membershipData: any) {
    return this.request(`/admin/memberships/${id}`, {
      method: 'PUT',
      body: JSON.stringify(membershipData),
    });
  }

  async deleteMembership(id: number) {
    return this.request(`/admin/memberships/${id}`, {
      method: 'DELETE',
    });
  }

  async uploadMembershipImage(id: number, file: File) {
    const formData = new FormData();
    formData.append('image', file);

    return this.request(`/admin/memberships/${id}/image`, {
      method: 'POST',
      headers: {},
      body: formData,
    });
  }
}

export const apiClient = new ApiClient();
```

## Authentication Context (Next.js)

```typescript
// contexts/AuthContext.tsx
import React, { createContext, useContext, useState, useEffect } from 'react';
import { apiClient } from '../lib/api-client';

interface User {
  id: number;
  name: string;
  email: string;
  role: string;
}

interface AuthContextType {
  user: User | null;
  loading: boolean;
  login: (email: string, password: string) => Promise<void>;
  register: (userData: any) => Promise<void>;
  logout: () => Promise<void>;
  checkAuth: () => Promise<void>;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const [user, setUser] = useState<User | null>(null);
  const [loading, setLoading] = useState(true);

  const checkAuth = async () => {
    try {
      const token = localStorage.getItem('token');
      if (token) {
        const response = await apiClient.getCurrentUser();
        if (response.success) {
          setUser(response.data);
        }
      }
    } catch (error) {
      console.error('Auth check failed:', error);
      localStorage.removeItem('token');
    } finally {
      setLoading(false);
    }
  };

  const login = async (email: string, password: string) => {
    const response = await apiClient.login({ email, password });
    if (response.success) {
      setUser(response.data.user);
    } else {
      throw new Error(response.message);
    }
  };

  const register = async (userData: any) => {
    const response = await apiClient.register(userData);
    if (response.success) {
      setUser(response.data.user);
    } else {
      throw new Error(response.message);
    }
  };

  const logout = async () => {
    await apiClient.logout();
    setUser(null);
  };

  useEffect(() => {
    checkAuth();
  }, []);

  return (
    <AuthContext.Provider value={{ user, loading, login, register, logout, checkAuth }}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
}
```

## Usage Examples

### Login Component
```typescript
// components/LoginForm.tsx
import { useState } from 'react';
import { useAuth } from '../contexts/AuthContext';

export function LoginForm() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const { login } = useAuth();

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    
    try {
      await login(email, password);
      // Redirect or show success message
    } catch (error) {
      console.error('Login failed:', error);
      // Show error message
    } finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      <input
        type="email"
        value={email}
        onChange={(e) => setEmail(e.target.value)}
        placeholder="Email"
        required
      />
      <input
        type="password"
        value={password}
        onChange={(e) => setPassword(e.target.value)}
        placeholder="Password"
        required
      />
      <button type="submit" disabled={loading}>
        {loading ? 'Logging in...' : 'Login'}
      </button>
    </form>
  );
}
```

### Protected Route Component
```typescript
// components/ProtectedRoute.tsx
import { useAuth } from '../contexts/AuthContext';
import { useRouter } from 'next/router';
import { useEffect } from 'react';

export function ProtectedRoute({ children }: { children: React.ReactNode }) {
  const { user, loading } = useAuth();
  const router = useRouter();

  useEffect(() => {
    if (!loading && !user) {
      router.push('/login');
    }
  }, [user, loading, router]);

  if (loading) {
    return <div>Loading...</div>;
  }

  if (!user) {
    return null;
  }

  return <>{children}</>;
}
```

### User Management Component
```typescript
// components/UserList.tsx
import { useState, useEffect } from 'react';
import { apiClient } from '../lib/api-client';

export function UserList() {
  const [users, setUsers] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadUsers();
  }, []);

  const loadUsers = async () => {
    try {
      const response = await apiClient.getUsers();
      if (response.success) {
        setUsers(response.data);
      }
    } catch (error) {
      console.error('Failed to load users:', error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return <div>Loading users...</div>;
  }

  return (
    <div>
      <h2>Users</h2>
      {users.map((user: any) => (
        <div key={user.id}>
          <h3>{user.name}</h3>
          <p>{user.email}</p>
        </div>
      ))}
    </div>
  );
}
```

## Error Handling

```typescript
// lib/error-handler.ts
export function handleApiError(error: any) {
  if (error.response) {
    // Server responded with error
    const { status, data } = error.response;
    
    switch (status) {
      case 401:
        // Unauthorized - redirect to login
        localStorage.removeItem('token');
        window.location.href = '/login';
        break;
      case 403:
        // Forbidden - show access denied message
        return 'Access denied';
      case 422:
        // Validation error
        return data.errors ? Object.values(data.errors).flat().join(', ') : data.message;
      default:
        return data.message || 'An error occurred';
    }
  } else if (error.request) {
    // Network error
    return 'Network error. Please check your connection.';
  } else {
    // Other error
    return error.message || 'An unexpected error occurred';
  }
}
```

## Testing the Integration

1. Start Laravel backend: `php artisan serve`
2. Start Next.js frontend: `npm run dev`
3. Test authentication flow
4. Test API endpoints
5. Test file uploads
6. Test error handling

## Common Issues and Solutions

### CORS Issues
- Ensure CORS middleware is properly configured
- Check allowed origins in Laravel config
- Verify Next.js is running on the correct port

### Authentication Issues
- Check JWT configuration
- Verify token storage and retrieval
- Ensure proper Authorization headers

### File Upload Issues
- Check file size limits
- Verify storage disk configuration
- Ensure proper FormData handling

## Security Considerations

1. Use HTTPS in production
2. Implement proper CORS policies
3. Validate all inputs
4. Use rate limiting
5. Implement proper error handling
6. Secure file uploads
7. Use environment variables for sensitive data
