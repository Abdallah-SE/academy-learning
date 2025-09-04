'use client';

import { useEffect } from 'react';
import { useAuthContext } from '@/context/AuthContext';
import { useRouter } from 'next/navigation';
import { LoginContainer } from '@/components/containers/LoginContainer';
import { Loading } from '@/components/atoms/Loading';

export default function AdminLoginPage() {
  const { isAuthenticated, isInitializing } = useAuthContext();
  const router = useRouter();

  console.log(' Login page: render', { isAuthenticated, isInitializing });

  useEffect(() => {
    console.log('🏠 Login page: useEffect running', { isAuthenticated, isInitializing });
    
    // Only redirect if we're not initializing and already authenticated
    if (!isInitializing && isAuthenticated) {
      console.log('🔄 Login page: User already authenticated, redirecting to dashboard');
      // Use replace instead of push to prevent back button issues
      router.replace('/admin/dashboard');
    }
  }, [isAuthenticated, isInitializing, router]);

  // Show loading while checking authentication
  if (isInitializing) {
    console.log('⏳ Login page: Still initializing...');
    return <Loading />;
  }

  // If authenticated, show loading (will redirect)
  if (isAuthenticated) {
    console.log('✅ Login page: User authenticated, showing loading for redirect');
    return <Loading />;
  }

  console.log('📝 Login page: Rendering login form');
  return <LoginContainer />;
}
