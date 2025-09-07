import axios from 'axios';

const BACKEND_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000';

const api = axios.create({
  baseURL: `${BACKEND_URL}/api/v1`,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  withCredentials: true, // ✅ Enable cookies - this is the key!
  timeout: 10000,
});

// ✅ Enhanced response interceptor for better error handling
api.interceptors.response.use(
  (response) => response,
  (error) => {
    // Enhanced error logging for debugging with fallback handling
    const errorDetails = {
      status: error.response?.status || 'No status',
      message: error.response?.data?.message || error.message || 'Unknown error',
      url: error.config?.url || 'Unknown URL',
      method: error.config?.method?.toUpperCase() || 'Unknown method',
      baseURL: error.config?.baseURL || 'Unknown base URL',
      timeout: error.code === 'ECONNABORTED',
      networkError: !error.response,
      code: error.code || 'No error code',
      fullError: error, // Include the full error object for debugging
    };

    // Only log errors in development or if they're not 401 (unauthorized)
    if (process.env.NODE_ENV === 'development' || error.response?.status !== 401) {
      console.error('API Error Details:', errorDetails);
      
      // Additional logging for empty error cases
      if (!errorDetails.message || errorDetails.message === 'Unknown error') {
        console.error('Full error object:', error);
        console.error('Error response:', error.response);
        console.error('Error config:', error.config);
      }
    }

    // Handle specific error cases
    if (error.code === 'ECONNABORTED') {
      console.warn('Request timeout - backend might be unavailable');
    } else if (!error.response) {
      console.warn('Network error - backend server might be down');
    }

    return Promise.reject(error);
  }
);

export default api;
