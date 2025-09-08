import axios, { AxiosError, AxiosResponse, InternalAxiosRequestConfig } from 'axios';

const BACKEND_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000';

// Define comprehensive error types
interface ApiErrorDetails {
  status: number | string;
  message: string;
  url: string;
  method: string;
  baseURL: string;
  timeout: boolean;
  networkError: boolean;
  code: string;
  validationErrors?: Record<string, string[]>;
  timestamp: string;
}

interface ApiResponse<T = any> {
  data: T;
  message?: string;
  success?: boolean;
}

// Backend error response structure
interface BackendErrorResponse {
  success: false;
  message: string;
  timestamp: string;
  status_code: number;
  errors?: Record<string, string[]>;
}

// Helper function to check if request is an auth check
const isAuthCheck = (url?: string): boolean => {
  if (!url) return false;
  return url.includes('/auth/profile') || url.includes('/auth/check');
};

// Helper function to determine if error should be logged
const shouldLogError = (errorDetails: ApiErrorDetails, error: AxiosError): boolean => {
  // Don't log in production
  if (process.env.NODE_ENV !== 'development') {
    return false;
  }

  // Don't log expected 401 errors for auth checks
  if (error.response?.status === 401 && isAuthCheck(errorDetails.url)) {
    return false;
  }

  // Don't log 401 errors in general (they're expected when not authenticated)
  if (error.response?.status === 401) {
    return false;
  }

  // Don't log empty/invalid errors
  const hasValidContent = Object.values(errorDetails).some(value => 
    value !== 'No status' && value !== 'Unknown error' && value !== 'Unknown URL' && 
    value !== 'Unknown method' && value !== 'Unknown base URL' && value !== 'No error code'
  );

  return hasValidContent;
};

// Error logging utility
const logError = (errorDetails: ApiErrorDetails, originalError: AxiosError): void => {
  console.group(`🚨 API Error - ${errorDetails.method} ${errorDetails.url}`);
  console.error('Status:', errorDetails.status);
  console.error('Message:', errorDetails.message);
  console.error('Code:', errorDetails.code);
  console.error('Timestamp:', errorDetails.timestamp);
  
  if (errorDetails.validationErrors) {
    console.group(' Validation Errors:');
    Object.entries(errorDetails.validationErrors).forEach(([field, messages]) => {
      console.error(`${field}:`, messages);
    });
    console.groupEnd();
  }
  
  if (errorDetails.networkError) {
    console.error('Network Error Details:', {
      timeout: errorDetails.timeout,
      code: errorDetails.code,
    });
  }
  
  console.groupEnd();
};

// Specific error handling
const handleSpecificErrors = (error: AxiosError, errorDetails: ApiErrorDetails): void => {
  // Only log warnings for non-401 errors
  if (error.code === 'ECONNABORTED') {
    console.warn('⏱️ Request timeout - backend might be unavailable');
  } else if (errorDetails.networkError) {
    console.warn('🌐 Network error - backend server might be down');
  } else if (error.response?.status && error.response.status >= 500) {
    console.error('🔥 Server error - backend is experiencing issues');
  } else if (error.response?.status === 403) {
    console.warn('🚫 Forbidden - insufficient permissions');
  } else if (error.response?.status === 404) {
    console.warn(' Not found - resource may not exist');
  } else if (error.response?.status === 422) {
    console.warn(' Validation error - check form data');
  }
  // Note: Removed 401 logging as it's expected behavior
};

// Create axios instance with proper configuration
const api = axios.create({
  baseURL: `${BACKEND_URL}/api/v1`,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  withCredentials: true,
  timeout: 10000,
});

// Request interceptor for logging and auth
api.interceptors.request.use(
  (config: InternalAxiosRequestConfig) => {
    // Add timestamp to requests
    config.metadata = { startTime: new Date() };
    
    // Log requests in development (but not auth checks)
    if (process.env.NODE_ENV === 'development' && !isAuthCheck(config.url)) {
      console.log(`🚀 ${config.method?.toUpperCase()} ${config.url}`, {
        data: config.data,
        params: config.params,
      });
    }
    
    return config;
  },
  (error: AxiosError) => {
    console.error('❌ Request interceptor error:', error);
    return Promise.reject(error);
  }
);

// Response interceptor with smart error handling
api.interceptors.response.use(
  (response: AxiosResponse) => {
    // Log successful responses in development (but not auth checks)
    if (process.env.NODE_ENV === 'development' && !isAuthCheck(response.config.url)) {
      const duration = new Date().getTime() - (response.config.metadata?.startTime?.getTime() || 0);
      console.log(`✅ ${response.config.method?.toUpperCase()} ${response.config.url} (${duration}ms)`, {
        status: response.status,
        data: response.data,
      });
    }
    
    return response;
  },
  (error: AxiosError) => {
    // Validate error object
    if (!error || typeof error !== 'object') {
      console.error('❌ Invalid error object received:', error);
      return Promise.reject(error);
    }

    // Build comprehensive error details
    const errorDetails: ApiErrorDetails = {
      status: error.response?.status || 'No status',
      message: error.response?.data?.message || error.message || 'Unknown error',
      url: error.config?.url || 'Unknown URL',
      method: error.config?.method?.toUpperCase() || 'Unknown method',
      baseURL: error.config?.baseURL || 'Unknown base URL',
      timeout: error.code === 'ECONNABORTED',
      networkError: !error.response,
      code: error.code || 'No error code',
      timestamp: new Date().toISOString(),
    };

    // Add validation errors if present - properly typed
    if (error.response?.status === 422) {
      const backendError = error.response.data as BackendErrorResponse;
      if (backendError?.errors) {
        errorDetails.validationErrors = backendError.errors;
      }
    }

    // Smart error logging - only log meaningful errors
    if (shouldLogError(errorDetails, error)) {
      logError(errorDetails, error);
    }

    // Handle specific error cases
    handleSpecificErrors(error, errorDetails);

    return Promise.reject(error);
  }
);

// Utility functions for common API operations
export const apiUtils = {
  // Get error message from error object
  getErrorMessage: (error: AxiosError): string => {
    if (error.response?.data?.message) {
      return error.response.data.message;
    }
    if (error.message) {
      return error.message;
    }
    return 'An unexpected error occurred';
  },

  // Get validation errors from error object - properly typed
  getValidationErrors: (error: AxiosError): Record<string, string[]> => {
    if (error.response?.status === 422) {
      const backendError = error.response.data as BackendErrorResponse;
      return backendError?.errors || {};
    }
    return {};
  },

  // Check if error is network related
  isNetworkError: (error: AxiosError): boolean => {
    return !error.response || error.code === 'ECONNABORTED';
  },

  // Check if error is validation related
  isValidationError: (error: AxiosError): boolean => {
    return error.response?.status === 422;
  },

  // Check if error is authentication related
  isAuthError: (error: AxiosError): boolean => {
    return error.response?.status === 401 || error.response?.status === 403;
  },

  // Check if error is expected (like 401 for unauthenticated users)
  isExpectedError: (error: AxiosError): boolean => {
    return error.response?.status === 401 && isAuthCheck(error.config?.url);
  },
};

// Export the configured axios instance
export default api;

// Export types for use in other files
export type { ApiErrorDetails, ApiResponse, BackendErrorResponse };
