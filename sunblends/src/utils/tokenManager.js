import axios from 'axios';

// In-memory storage for token and user data (not accessible from other scripts/XSS)
let authToken = null;
let currentUser = null;

// SessionStorage keys - safe to keep minimal data for persistence
const SESSION_AUTH_FLAG = 'is_authenticated';
const SESSION_USER_ID = 'user_id';
const API_BASE_URL = 'http://127.0.0.1:8000/api';



const TokenManager = {
  // Set token in memory and a flag in sessionStorage
  setToken(token, userData) {
    // Store token and user data in memory only (not accessible via browser localStorage)
    authToken = token;
    currentUser = userData;
    
    // Set default Authorization header for all future axios requests
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    
    // Store minimal indicator in sessionStorage for persistence across page refreshes
    sessionStorage.setItem(SESSION_AUTH_FLAG, 'true');
    if (userData && userData.customer_id) {
      sessionStorage.setItem(SESSION_USER_ID, userData.customer_id);
    }
    
    // For backwards compatibility with existing code
    localStorage.setItem('email', userData?.customer_email || userData?.email || '');
    localStorage.setItem('isLoggedIn', 'true');
    
    return true;
  },
  
  // Get the current user data
  getUser() {
    return currentUser;
  },
  
  // Get the token from memory
  getToken() {
    return authToken;
  },
  
  // Check if we have a token
  hasToken() {
    return authToken !== null;
  },
  
  // Check if we had a session that was lost due to page refresh
  hadSession() {
    return sessionStorage.getItem(SESSION_AUTH_FLAG) === 'true';
  },
  
  // Get user ID from session
  getUserId() {
    return sessionStorage.getItem(SESSION_USER_ID);
  },
  
  // Clear token and session data
  clearToken() {
    authToken = null;
    currentUser = null;
    
    // Clear axios default header
    delete axios.defaults.headers.common['Authorization'];
    
    // Clear session storage
    sessionStorage.removeItem(SESSION_AUTH_FLAG);
    sessionStorage.removeItem(SESSION_USER_ID);
    
    // For backwards compatibility with existing code
    localStorage.removeItem('email');
    localStorage.removeItem('userData');
    localStorage.removeItem('isLoggedIn');
    localStorage.removeItem('token');
    
    return true;
  },
  
  // Refresh token if possible
  async refreshSession() {
    const userId = this.getUserId();
    if (!userId) return { success: false, message: 'No user ID in session' };
    
    try {
      // Request specifically focuses on customer-token
      const response = await axios.post(`${API_BASE_URL}/refresh-session`, {
        customer_id: userId,
        is_refresh: true,  
        clean_tokens: true,  // This will clean up any non-customer-token tokens
        token_type: 'customer-token' // Explicitly tell server we want customer-token
      });
      
      if (response.data.success && response.data.token) {
        console.log("Session refreshed successfully:", {
          userId,
          tokenPrefix: response.data.token.split('|')[0],
          tokenType: 'customer-token'
        });
        
        this.setToken(response.data.token, response.data.user);
        return {
          success: true,
          user: response.data.user
        };
      }
      
      // Handle special case: server explicitly requires full re-auth
      if (response.data.require_reauth) {
        console.warn("Server requires re-authentication:", response.data.message);
        this.clearToken();
        
        return {
          success: false,
          requireLogin: true,
          message: response.data.message || 'Please login again'
        };
      }
      
      // If refresh failed for other reasons, clear token data
      console.warn("Session refresh failed:", response.data.message);
      this.clearToken();
      return {
        success: false,
        message: response.data.message || 'Session refresh failed'
      };
    } catch (error) {
      console.error('Token refresh error:', error.response?.data || error.message);
      
      // If there's a 401 error, that means the token was likely revoked
      if (error.response?.status === 401) {
        this.clearToken();
        return {
          success: false,
          requireLogin: true,
          message: error.response?.data?.message || 'Your session has expired. Please login again.'
        };
      }
      
      // Only clear token on specific errors, not network errors
      if (error.response) {
        this.clearToken();
      }
      
      return {
        success: false,
        message: 'Error refreshing session'
      };
    }
  },

  async validateToken() {
    if (!this.hasToken()) return false;
    
    try {
      // Use the new public endpoint that doesn't require auth middleware
      const response = await axios.post(
        `${API_BASE_URL}/check-token`,
        {},  // empty body
        {
          headers: {
            'Authorization': `Bearer ${this.getToken()}`,
            'Accept': 'application/json'
          }
        }
      );
      
      return response.data.valid === true;
    } catch (error) {
      console.error('Token validation error:', error.response?.data || error.message);
      
      // If we receive a 401 with require_reauth flag, the token is invalid
      if (error.response?.status === 401 && error.response?.data?.require_reauth) {
        this.clearToken();
        
        // Dispatch event for app-wide handling
        const event = new CustomEvent("forceReauthentication", {
          detail: { 
            message: error.response.data.message || "Your session has expired. Please login again."
          }
        });
        document.dispatchEvent(event);
        
        return false;
      }
      
      // For connection errors, we'll assume the token is still valid
      // to prevent users from being logged out due to temporary API issues
      return true;
    }
  },
  
  // Make authenticated API request with automatic token handling
  async request(config) {
    // Add auth header if we have a token
    const headers = {
      ...(config.headers || {}),
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    };
    
    if (this.hasToken()) {
      headers['Authorization'] = `Bearer ${this.getToken()}`;
    }
    
    try {
      const url = config.url.startsWith('http') 
        ? config.url 
        : `${API_BASE_URL}${config.url}`;
        
      return await axios({
        ...config,
        headers,
        url
      });
    } catch (error) {
      // Handle 401 unauthorized errors with token refresh
      if (error.response?.status === 401 && this.hadSession()) {
        // Try to refresh the token
        const refreshResult = await this.refreshSession();
        
        if (refreshResult.success) {
          // Retry the original request with the new token
          headers['Authorization'] = `Bearer ${this.getToken()}`;
          
          const url = config.url.startsWith('http') 
            ? config.url 
            : `${API_BASE_URL}${config.url}`;
            
          return axios({
            ...config,
            headers,
            url
          });
        }
      }
      
      throw error;
    }
  }
};

// Add convenience methods for common HTTP methods
['get', 'post', 'put', 'delete', 'patch'].forEach(method => {
  TokenManager[method] = async (url, data = null, config = {}) => {
    return TokenManager.request({
      method,
      url,
      data,
      ...config
    });
  };
});

export default TokenManager;