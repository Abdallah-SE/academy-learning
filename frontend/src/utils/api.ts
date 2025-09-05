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
    // Log error for debugging
    console.error('API Error:', {
      status: error.response?.status,
      message: error.response?.data?.message,
      url: error.config?.url,
    });

    // Don't redirect automatically - let components handle 401 errors
    // The components will check the response status and handle accordingly
    return Promise.reject(error);
  }
);

export default api;
