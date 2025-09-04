import React, { useCallback } from 'react';
import { useAuthContext } from '@/context/AuthContext'; // Changed from useAuth
import { LoginForm } from '@/components/presenters/LoginForm';
import { LoginFormData } from '@/schemas/auth.schema';

export const LoginContainer: React.FC = () => {
  const { login, isLoading, error } = useAuthContext(); // Changed from useAuth

  console.log('📦 LoginContainer render', { isLoading, error: error ? 'Has error' : 'No error' });

  const handleLogin = useCallback(async (data: LoginFormData) => {
    console.log('🔐 LoginContainer: handleLogin called', { 
      email: data.email, 
      password: '***',
      remember: data.remember 
    });
    
    const success = await login({ email: data.email, password: data.password, remember: data.remember });
    console.log('📡 LoginContainer: login result', success);
    
    // REMOVE the router.push here - let the login page handle the redirect
    if (success) {
      console.log('✅ LoginContainer: Login successful, login page will redirect');
    } else {
      console.log('❌ LoginContainer: Login failed, staying on login page');
    }
  }, [login]);

  return (
    <LoginForm
      onSubmit={handleLogin}
      isLoading={isLoading}
      error={error}
    />
  );
};
