'use client';

import React from 'react';
import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { ChevronRightIcon, HomeIcon } from 'lucide-react';
import { cn } from '@/lib/utils';

export interface BreadcrumbItem {
  label: string;
  href?: string;
  icon?: React.ReactNode;
  isActive?: boolean;
  isClickable?: boolean;
}

export interface BreadcrumbProps {
  items?: BreadcrumbItem[];
  showHome?: boolean;
  homeHref?: string;
  separator?: React.ReactNode;
  className?: string;
  maxItems?: number;
  showEllipsis?: boolean;
}

export const Breadcrumb: React.FC<BreadcrumbProps> = ({
  items = [],
  showHome = true,
  homeHref = '/admin/dashboard',
  separator = <ChevronRightIcon className="w-4 h-4 text-gray-400" />,
  className,
  maxItems = 5,
  showEllipsis = true,
}) => {
  const pathname = usePathname();
  
  // Generate breadcrumb items from pathname if not provided
  const generatedItems = items.length > 0 ? items : generateBreadcrumbFromPath(pathname);
  
  // Limit items if maxItems is specified
  const displayItems = maxItems && generatedItems.length > maxItems 
    ? showEllipsis 
      ? [
          ...generatedItems.slice(0, 1),
          { label: '...', isClickable: false },
          ...generatedItems.slice(-(maxItems - 2))
        ]
      : generatedItems.slice(-maxItems)
    : generatedItems;

  return (
    <nav 
      className={cn(
        "flex items-center space-x-1 text-sm text-gray-600",
        className
      )}
      aria-label="Breadcrumb"
    >
      {showHome && (
        <>
          <Link
            href={homeHref}
            className="flex items-center space-x-1 hover:text-gray-900 transition-colors"
          >
            <HomeIcon className="w-4 h-4" />
            <span>Home</span>
          </Link>
          {displayItems.length > 0 && separator}
        </>
      )}
      
      {displayItems.map((item, index) => (
        <React.Fragment key={index}>
          {item.isClickable && item.href ? (
            <Link
              href={item.href}
              className={cn(
                "flex items-center space-x-1 hover:text-gray-900 transition-colors",
                item.isActive && "text-gray-900 font-medium"
              )}
            >
              {item.icon && <span className="flex-shrink-0">{item.icon}</span>}
              <span>{item.label}</span>
            </Link>
          ) : (
            <span
              className={cn(
                "flex items-center space-x-1",
                item.isActive && "text-gray-900 font-medium",
                !item.isClickable && "text-gray-500"
              )}
            >
              {item.icon && <span className="flex-shrink-0">{item.icon}</span>}
              <span>{item.label}</span>
            </span>
          )}
          
          {index < displayItems.length - 1 && (
            <span className="flex-shrink-0">
              {separator}
            </span>
          )}
        </React.Fragment>
      ))}
    </nav>
  );
};

// Helper function to generate breadcrumb items from pathname
function generateBreadcrumbFromPath(pathname: string): BreadcrumbItem[] {
  const segments = pathname.split('/').filter(Boolean);
  const items: BreadcrumbItem[] = [];
  
  let currentPath = '';
  
  segments.forEach((segment, index) => {
    currentPath += `/${segment}`;
    const isLast = index === segments.length - 1;
    
    // Convert segment to readable label
    const label = segment
      .split('-')
      .map(word => word.charAt(0).toUpperCase() + word.slice(1))
      .join(' ');
    
    items.push({
      label,
      href: isLast ? undefined : currentPath,
      isActive: isLast,
      isClickable: !isLast,
    });
  });
  
  return items;
}

// Compact breadcrumb for mobile/small screens
export const CompactBreadcrumb: React.FC<BreadcrumbProps> = ({
  items = [],
  showHome = true,
  homeHref = '/admin/dashboard',
  className,
}) => {
  const pathname = usePathname();
  const generatedItems = items.length > 0 ? items : generateBreadcrumbFromPath(pathname);
  
  // Show only the last 2 items for compact view
  const displayItems = generatedItems.slice(-2);
  
  return (
    <nav 
      className={cn(
        "flex items-center space-x-1 text-sm text-gray-600",
        className
      )}
      aria-label="Breadcrumb"
    >
      {showHome && generatedItems.length > 2 && (
        <>
          <Link
            href={homeHref}
            className="flex items-center space-x-1 hover:text-gray-900 transition-colors"
          >
            <HomeIcon className="w-4 h-4" />
          </Link>
          <ChevronRightIcon className="w-4 h-4 text-gray-400" />
        </>
      )}
      
      {displayItems.map((item, index) => (
        <React.Fragment key={index}>
          {item.isClickable && item.href ? (
            <Link
              href={item.href}
              className={cn(
                "flex items-center space-x-1 hover:text-gray-900 transition-colors",
                item.isActive && "text-gray-900 font-medium"
              )}
            >
              {item.icon && <span className="flex-shrink-0">{item.icon}</span>}
              <span className="truncate max-w-32">{item.label}</span>
            </Link>
          ) : (
            <span
              className={cn(
                "flex items-center space-x-1",
                item.isActive && "text-gray-900 font-medium",
                !item.isClickable && "text-gray-500"
              )}
            >
              {item.icon && <span className="flex-shrink-0">{item.icon}</span>}
              <span className="truncate max-w-32">{item.label}</span>
            </span>
          )}
          
          {index < displayItems.length - 1 && (
            <ChevronRightIcon className="w-4 h-4 text-gray-400 flex-shrink-0" />
          )}
        </React.Fragment>
      ))}
    </nav>
  );
};
