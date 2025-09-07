import { useState, useEffect, useCallback, useMemo, useRef } from 'react';
import { useRouter } from 'next/navigation';
import { AdminRepository, AdminLoginData, AdminUser } from '@/repositories/adminRepository';
import { cookieUtils } from '@/utils/cookies';

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

  // ✅ Enhanced auth status check with better error handling and timeout
  const checkAuthStatus = useCallback(async () => {
    if (hasInitialized.current) {
       return;
    }
    
    hasInitialized.current = true;
     
    try {
      // ✅ Add timeout to prevent hanging requests
      const timeoutPromise = new Promise((_, reject) => {
        setTimeout(() => reject(new Error('Request timeout')), 10000);
      });
      
      const response = await Promise.race([
        AdminRepository.getProfile(),
        timeoutPromise
      ]) as any;
 
      if (response.success && response.data) {
         setUser(response.data.admin);
        setError(null); // ✅ Clear any previous errors
      } else {
         setUser(null);
      }
    } catch (err: any) {
       
      // ✅ Better error handling with specific error types
      if (err.response?.status === 401) {
        // Unauthorized - user is not authenticated
        setUser(null);
        setError(null); // Don't show error for normal unauthenticated state
      } else if (err.message === 'Request timeout' || err.code === 'ECONNABORTED') {
        // Timeout - backend might be unavailable
        console.warn('Backend timeout - server might be unavailable');
        setUser(null);
        setError('Backend server is not responding. Please check if the server is running.');
      } else if (!err.response) {
        // Network error - backend is down
        console.warn('Network error - backend server is down');
        setUser(null);
        setError('Cannot connect to backend server. Please check if the server is running.');
      } else {
        // Other errors
        console.warn('Auth check failed:', err.message);
        setUser(null);
        setError('Authentication check failed. Please try again.');
      }
    } finally {
       setIsInitializing(false);
    }
  }, []);

  useEffect(() => {
    checkAuthStatus();
  }, [checkAuthStatus]);

  // ✅ Enhanced login with better error handling and state management
  const login = useCallback(async (data: AdminLoginData): Promise<boolean> => {
     setIsLoading(true);
    setError(null);
    
    try {
      const response = await AdminRepository.login(data);
       
      if (response.success && response.data) {
         
        // ✅ Use the admin data from login response
        setUser(response.data.admin);
        setError(null);
        
        // ✅ Ensure initialization state is properly set
        setIsInitializing(false);
        
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
       console.warn('Logout API call failed, but continuing with logout:', err);
    } finally {
      // ✅ Clear authentication state first
      setUser(null);
      setError(null);
      setIsLoading(false);
      
      // ✅ Clear any client-side cookies as backup
      cookieUtils.remove('token');
      cookieUtils.remove('admin_token');
      cookieUtils.remove('auth_token');
      
      // ✅ Reset initialization flag to force re-check on next login
      hasInitialized.current = false;
      
      // ✅ Force redirect to login page with fallback
      try {
        router.replace('/admin/login');
        // Fallback: force page reload if router doesn't work
        setTimeout(() => {
          if (window.location.pathname !== '/admin/login') {
            window.location.href = '/admin/login';
          }
        }, 100);
      } catch (redirectError) {
        console.warn('Router redirect failed, using window.location:', redirectError);
        window.location.href = '/admin/login';
      }
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
