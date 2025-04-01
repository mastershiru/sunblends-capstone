import React, { useEffect } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import TokenManager from '../utils/tokenManager';
import { useNavbar } from '../context/NavbarContext';

const RouteGuard = ({ children }) => {
  const location = useLocation();
  const navigate = useNavigate();
  const { setIsOpenLogin } = useNavbar();
  
  useEffect(() => {
    // Check token validity on route change
    const validateToken = async () => {
      if (TokenManager.hasToken()) {
        try {
          const isValid = await TokenManager.validateToken();
          if (!isValid) {
            // Force logout and redirect
            TokenManager.clearToken();
            
            // Redirect home if not already there
            if (location.pathname !== '/') {
              navigate('/');
            }
            
            // Show login modal
            setTimeout(() => {
              setIsOpenLogin(true);
              alert("Your session has expired. Please login again.");
            }, 300);
          }
        } catch (error) {
          console.error("Token validation error:", error);
        }
      }
    };
    
    validateToken();
  }, [location.pathname, navigate, setIsOpenLogin]);
  
  return <>{children}</>;
};

export default RouteGuard;