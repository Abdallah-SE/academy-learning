import React from 'react';
import { 
  HomeIcon, 
  UsersIcon, 
  UserCogIcon, 
  BookOpenIcon, 
  DollarSignIcon, 
  SettingsIcon, 
  BarChart3Icon, 
  BellIcon, 
  HelpCircleIcon,
  LogInIcon,
  TestTubeIcon
} from 'lucide-react';
import { BreadcrumbItem } from '@/components/atoms/Breadcrumb';

export interface BreadcrumbConfig {
  [key: string]: BreadcrumbItem;
}

export const breadcrumbConfig: BreadcrumbConfig = {
  '/admin': {
    label: 'Admin',
    href: '/admin',
    icon: React.createElement(UserCogIcon, { className: "w-4 h-4" }),
    isClickable: true,
  },
  '/admin/dashboard': {
    label: 'Dashboard',
    href: '/admin/dashboard',
    icon: React.createElement(HomeIcon, { className: "w-4 h-4" }),
    isClickable: true,
  },
  '/admin/login': {
    label: 'Login',
    href: '/admin/login',
    icon: React.createElement(LogInIcon, { className: "w-4 h-4" }),
    isClickable: true,
  },
  '/admin/users': {
    label: 'Users',
    href: '/admin/users',
    icon: React.createElement(UsersIcon, { className: "w-4 h-4" }),
    isClickable: true,
  },
  '/admin/admins': {
    label: 'Admins',
    href: '/admin/admins',
    icon: React.createElement(UserCogIcon, { className: "w-4 h-4" }),
    isClickable: true,
  },
  '/admin/courses': {
    label: 'Courses',
    href: '/admin/courses',
    icon: React.createElement(BookOpenIcon, { className: "w-4 h-4" }),
    isClickable: true,
  },
  '/admin/revenue': {
    label: 'Revenue',
    href: '/admin/revenue',
    icon: React.createElement(DollarSignIcon, { className: "w-4 h-4" }),
    isClickable: true,
  },
  '/admin/settings': {
    label: 'Settings',
    href: '/admin/settings',
    icon: React.createElement(SettingsIcon, { className: "w-4 h-4" }),
    isClickable: true,
  },
  '/admin/analytics': {
    label: 'Analytics',
    href: '/admin/analytics',
    icon: React.createElement(BarChart3Icon, { className: "w-4 h-4" }),
    isClickable: true,
  },
  '/admin/notifications': {
    label: 'Notifications',
    href: '/admin/notifications',
    icon: React.createElement(BellIcon, { className: "w-4 h-4" }),
    isClickable: true,
  },
  '/admin/help': {
    label: 'Help',
    href: '/admin/help',
    icon: React.createElement(HelpCircleIcon, { className: "w-4 h-4" }),
    isClickable: true,
  },
  '/admin/test-auth': {
    label: 'Test Auth',
    href: '/admin/test-auth',
    icon: React.createElement(TestTubeIcon, { className: "w-4 h-4" }),
    isClickable: true,
  },
};

// Dynamic breadcrumb configurations for specific pages
export const dynamicBreadcrumbConfig: {
  [key: string]: (params?: any) => BreadcrumbItem[];
} = {
  '/admin/admins/create': () => [
    breadcrumbConfig['/admin/admins'],
    {
      label: 'Create Admin',
      isActive: true,
      isClickable: false,
    }
  ],
  '/admin/admins/edit': (params: { id: string }) => [
    breadcrumbConfig['/admin/admins'],
    {
      label: `Edit Admin #${params.id}`,
      isActive: true,
      isClickable: false,
    }
  ],
  '/admin/admins/view': (params: { id: string }) => [
    breadcrumbConfig['/admin/admins'],
    {
      label: `View Admin #${params.id}`,
      isActive: true,
      isClickable: false,
    }
  ],
  '/admin/users/create': () => [
    breadcrumbConfig['/admin/users'],
    {
      label: 'Create User',
      isActive: true,
      isClickable: false,
    }
  ],
  '/admin/users/edit': (params: { id: string }) => [
    breadcrumbConfig['/admin/users'],
    {
      label: `Edit User #${params.id}`,
      isActive: true,
      isClickable: false,
    }
  ],
  '/admin/courses/create': () => [
    breadcrumbConfig['/admin/courses'],
    {
      label: 'Create Course',
      isActive: true,
      isClickable: false,
    }
  ],
  '/admin/courses/edit': (params: { id: string }) => [
    breadcrumbConfig['/admin/courses'],
    {
      label: `Edit Course #${params.id}`,
      isActive: true,
      isClickable: false,
    }
  ],
};

// Helper function to get breadcrumb configuration for a path
export const getBreadcrumbConfig = (pathname: string, params?: any): BreadcrumbItem[] => {
  // Check for dynamic configurations first
  const dynamicKey = Object.keys(dynamicBreadcrumbConfig).find(key => 
    pathname.startsWith(key)
  );
  
  if (dynamicKey && dynamicBreadcrumbConfig[dynamicKey]) {
    return dynamicBreadcrumbConfig[dynamicKey](params);
  }
  
  // Check for exact match
  if (breadcrumbConfig[pathname]) {
    return [breadcrumbConfig[pathname]];
  }
  
  // Generate breadcrumbs from path segments
  const segments = pathname.split('/').filter(Boolean);
  const breadcrumbs: BreadcrumbItem[] = [];
  let currentPath = '';
  
  segments.forEach((segment, index) => {
    currentPath += `/${segment}`;
    const isLast = index === segments.length - 1;
    
    // Check if we have a configuration for this path
    const config = breadcrumbConfig[currentPath];
    if (config) {
      breadcrumbs.push({
        ...config,
        isActive: isLast,
        isClickable: !isLast,
      });
    } else {
      // Generate default breadcrumb
      const label = segment
        .split('-')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
      
      breadcrumbs.push({
        label,
        href: isLast ? undefined : currentPath,
        isActive: isLast,
        isClickable: !isLast,
      });
    }
  });
  
  return breadcrumbs;
};
