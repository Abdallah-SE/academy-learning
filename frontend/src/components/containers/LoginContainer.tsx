import React, { useCallback } from 'react';
import { useAuthContext } from '@/context/AuthContext'; // Changed from useAuth
import { LoginForm } from '@/components/presenters/LoginForm';
import { LoginFormData } from '@/schemas/auth.schema';

export const LoginContainer: React.FC = () => {
  const { login, isLoading, error } = useAuthContext(); // Changed from useAuth

 
  const handleLogin = useCallback(async (data: LoginFormData) => {
   
    
    const success = await login({ email: data.email, password: data.password, remember: data.remember });
     
    // REMOVE the router.push here - let the login page handle the redirect
    if (success) {
     } else {
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
