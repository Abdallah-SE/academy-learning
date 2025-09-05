'use client';

import { useEffect } from 'react';
import { useAuthContext } from '@/context/AuthContext';
import { useRouter } from 'next/navigation';
import { LoginContainer } from '@/components/containers/LoginContainer';
import { Loading } from '@/components/atoms/Loading';

export default function AdminLoginPage() {
  const { isAuthenticated, isInitializing } = useAuthContext();
  const router = useRouter();

 
  useEffect(() => {
     
    // Only redirect if we're not initializing and already authenticated
    if (!isInitializing && isAuthenticated) {
       // Use replace instead of push to prevent back button issues
      router.replace('/admin/dashboard');
    }
  }, [isAuthenticated, isInitializing, router]);

  // Show loading while checking authentication
  if (isInitializing) {
     return <Loading />;
  }

  // If authenticated, show loading (will redirect)
  if (isAuthenticated) {
     return <Loading />;
  }

   return <LoginContainer />;
}
