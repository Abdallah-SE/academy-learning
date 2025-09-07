'use client';

import { useEffect, useCallback, useMemo, useState } from 'react';
import { usePathname, useSearchParams } from 'next/navigation';
import { useBreadcrumb as useBreadcrumbContext } from '@/context/BreadcrumbContext';
import { getBreadcrumbConfig } from '@/config/breadcrumbs';
import { BreadcrumbItem } from '@/components/atoms/Breadcrumb';

export const useBreadcrumb = () => {
  // Always call hooks in the same order - this is the key to fixing Rules of Hooks
  const [isClient, setIsClient] = useState(false);
  
  // Always call these hooks, but handle errors gracefully
  let pathname = '/';
  let searchParams = new URLSearchParams();
  
  try {
    pathname = usePathname();
    searchParams = useSearchParams();
  } catch (error) {
    // Silently handle the error - this is expected during SSR
    pathname = '/';
    searchParams = new URLSearchParams();
  }
  
  useEffect(() => {
    setIsClient(true);
  }, []);
  const { setBreadcrumbs, breadcrumbs } = useBreadcrumbContext();

  // Memoize search params to prevent unnecessary re-renders
  const memoizedSearchParams = useMemo(() => {
    return Object.fromEntries(searchParams.entries());
  }, [searchParams]);

  // Auto-generate breadcrumbs based on current path
  const generateBreadcrumbs = useCallback(() => {
    const config = getBreadcrumbConfig(pathname, memoizedSearchParams);
    setBreadcrumbs(config);
  }, [pathname, memoizedSearchParams, setBreadcrumbs]);

  // Auto-generate breadcrumbs when pathname changes
  useEffect(() => {
    generateBreadcrumbs();
  }, [generateBreadcrumbs]);

  // Manual breadcrumb management functions
  const setCustomBreadcrumbs = useCallback((customBreadcrumbs: BreadcrumbItem[]) => {
    setBreadcrumbs(customBreadcrumbs);
  }, [setBreadcrumbs]);

  const addBreadcrumb = useCallback((breadcrumb: BreadcrumbItem) => {
    setBreadcrumbs([...breadcrumbs, breadcrumb]);
  }, [breadcrumbs, setBreadcrumbs]);

  const removeLastBreadcrumb = useCallback(() => {
    if (breadcrumbs.length > 0) {
      setBreadcrumbs(breadcrumbs.slice(0, -1));
    }
  }, [breadcrumbs, setBreadcrumbs]);

  const updateLastBreadcrumb = useCallback((breadcrumb: BreadcrumbItem) => {
    if (breadcrumbs.length > 0) {
      const updated = [...breadcrumbs];
      updated[updated.length - 1] = breadcrumb;
      setBreadcrumbs(updated);
    }
  }, [breadcrumbs, setBreadcrumbs]);

  return {
    breadcrumbs,
    generateBreadcrumbs,
    setCustomBreadcrumbs,
    addBreadcrumb,
    removeLastBreadcrumb,
    updateLastBreadcrumb,
  };
};

// Hook for specific page breadcrumb management
export const usePageBreadcrumb = (pageBreadcrumbs: BreadcrumbItem[]) => {
  const { setBreadcrumbs } = useBreadcrumbContext();

  // Memoize the breadcrumbs to prevent infinite re-renders
  // Use a custom comparison function since breadcrumbs may contain React elements
  const memoizedBreadcrumbs = useMemo(() => pageBreadcrumbs, [
    pageBreadcrumbs.length,
    pageBreadcrumbs.map(b => `${b.label}-${b.href}-${b.isActive}-${b.isClickable}`).join('|')
  ]);

  useEffect(() => {
    setBreadcrumbs(memoizedBreadcrumbs);
  }, [memoizedBreadcrumbs, setBreadcrumbs]);
};
