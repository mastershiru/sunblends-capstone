import axios from 'axios';

// Base URL for API
const API_URL = 'http://127.0.0.1:8000';

// Create an axios instance with default configuration
const instance = axios.create({
  baseURL: API_URL,
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  }
});

// Add interceptor to automatically fetch CSRF token if needed
instance.interceptors.response.use(
  response => response,
  async error => {
    // If we get a 419 (CSRF token mismatch), try to get a new token and retry
    if (error.response && error.response.status === 419) {
      try {
        // Get a new CSRF token
        await axios.get(`${API_URL}/sanctum/csrf-cookie`, { withCredentials: true });
        
        // Clone original request and retry
        const config = error.config;
        return await instance(config);
      } catch (refreshError) {
        return Promise.reject(refreshError);
      }
    }
    return Promise.reject(error);
  }
);

export default instance;