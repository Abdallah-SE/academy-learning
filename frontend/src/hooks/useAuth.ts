import { useState, useEffect, useCallback, useMemo, useRef } from 'react';
import { useRouter } from 'next/navigation';
import { AdminRepository, AdminLoginData, AdminUser } from '@/repositories/adminRepository';

interface UseAuthReturn {
  user: AdminUser | null;
  isLoading: boolean;
  isInitializing: boolean;
  error: string | null;
  login: (data: AdminLoginData) => Promise<boolean>;
  logout: () => Promise<void>;
  isAuthenticated: boolean;
  clearError: () => void; // ✅ Add error clearing function
}

export const useAuth = (): UseAuthReturn => {
  const [user, setUser] = useState<AdminUser | null>(null);
  const [isLoading, setIsLoading] = useState(false);
  const [isInitializing, setIsInitializing] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const router = useRouter();
  
  const isAuthenticated = useMemo(() => !!user, [user]);
  const hasInitialized = useRef(false);

  // ✅ Clear error function
  const clearError = useCallback(() => {
    setError(null);
  }, []);

  // ✅ Enhanced auth status check with better error handling
  const checkAuthStatus = useCallback(async () => {
    if (hasInitialized.current) {
       return;
    }
    
    hasInitialized.current = true;
     
    try {
      const response = await AdminRepository.getProfile();
 
      if (response.success && response.data) {
         setUser(response.data.admin);
        setError(null); // ✅ Clear any previous errors
      } else {
         setUser(null);
      }
    } catch (err: any) {
       
      // ✅ Better error handling - don't set user to null immediately
      // This might be a temporary network issue or cookie problem
      if (err.response?.status === 401) {
         setUser(null);
      } else {
         // Don't change user state for network errors
      }
    } finally {
       setIsInitializing(false);
    }
  }, []);

  useEffect(() => {
    checkAuthStatus();
  }, [checkAuthStatus]);

  // ✅ Enhanced login with better error handling
  const login = useCallback(async (data: AdminLoginData): Promise<boolean> => {
     setIsLoading(true);
    setError(null);
    
    try {
      const response = await AdminRepository.login(data);
       
      if (response.success && response.data) {
         
        // ✅ Use the admin data from login response
        setUser(response.data.admin);
        setError(null);
        return true;
      } else {
         setError(response.message || 'Login failed');
        return false;
      }
    } catch (err: any) {
       
      // ✅ Better error message handling
      const errorMessage = err.response?.data?.message || 
                          err.message || 
                          'Network error occurred';
      setError(errorMessage);
      return false;
    } finally {
      setIsLoading(false);
    }
  }, []);

  // ✅ Enhanced logout with better error handling
  const logout = useCallback(async (): Promise<void> => {
     setIsLoading(true);
    
    try {
      await AdminRepository.logout(); // ✅ Backend clears cookie
     } catch (err) {
       // ✅ Don't prevent logout even if API call fails
    } finally {
      setUser(null);
      setError(null);
      setIsLoading(false);
      router.push('/admin/login');
    }
  }, [router]);

  return {
    user,
    isLoading,
    isInitializing,
    error,
    login,
    logout,
    isAuthenticated,
    clearError, // ✅ Export clearError function
  };
};
