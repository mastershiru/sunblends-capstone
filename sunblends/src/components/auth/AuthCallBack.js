import React, { useEffect } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import { toast } from 'react-toastify';
import TokenManager from '../../utils/tokenManager';

function AuthCallback() {
  const navigate = useNavigate();
  const location = useLocation();

  useEffect(() => {
    const handleAuthCallback = async () => {
      try {
        // Parse query parameters
        const queryParams = new URLSearchParams(location.search);
        const token = queryParams.get('token');
        const userDataString = queryParams.get('user');
        
        if (!token || !userDataString) {
          toast.error('Authentication data is missing');
          navigate('/');
          return;
        }

        // Parse the user data
        const userData = JSON.parse(decodeURIComponent(userDataString));
        
        // Store token and user data
        TokenManager.setToken(token, userData);
        
        // Set session storage for minimal state
        sessionStorage.setItem("user_id", userData.customer_id);
        sessionStorage.setItem("is_authenticated", "true");
        
        // Show success message
        toast.success('Successfully logged in!', {
          position: 'top-right',
          autoClose: 2000
        });
        
        // Navigate to home page or dashboard
        navigate('/');
        
        // Optional: Refresh the page to ensure all components update
        // window.location.reload();
      } catch (error) {
        console.error('Error processing authentication callback:', error);
        toast.error('Failed to complete authentication');
        navigate('/');
      }
    };

    handleAuthCallback();
  }, [location, navigate]);

  return (
    <div className="auth-callback-container">
      <div className="loading-spinner"></div>
      <p>Completing login, please wait...</p>
    </div>
  );
}

export default AuthCallback;