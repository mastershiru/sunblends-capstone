import React, { useState, useEffect, useCallback } from 'react';
import SimpleNotification from './simple-notification';

const NotificationManager = () => {
  const [notifications, setNotifications] = useState([]);
  
  // Listen for custom notification events
  useEffect(() => {
    const handleShowNotification = (event) => {
      console.log("Notification event received:", event.detail);
      if (event.detail) {
        addNotification(event.detail);
      }
    };
    
    window.addEventListener('showNotification', handleShowNotification);
    console.log("NotificationManager: Event listener attached");
    
    return () => {
      window.removeEventListener('showNotification', handleShowNotification);
    };
  }, []);
  
  const addNotification = useCallback((notification) => {
    console.log("Adding notification:", notification);
    const id = Date.now();
    setNotifications(prev => [...prev, { 
      id, 
      message: notification.message || 'Notification',
      type: notification.type || 'info',
      duration: notification.duration || 5000
    }]);
    
    return id;
  }, []);
  
  const removeNotification = useCallback((id) => {
    console.log("Removing notification:", id);
    setNotifications(prev => prev.filter(notification => notification.id !== id));
  }, []);
  
 
  
  return (
    <div style={{ position: 'fixed', top: '20px', right: '20px', zIndex: 1000 }}>
      {notifications.map((notification, index) => (
        <div 
          key={notification.id}
          style={{ 
            marginBottom: '10px',
            transform: `translateY(${index * 5}px)`
          }}
        >
          <SimpleNotification
            message={notification.message}
            type={notification.type}
            duration={notification.duration}
            onClose={() => removeNotification(notification.id)}
          />
        </div>
      ))}
    </div>
  );
};

// Helper function to show notifications from anywhere
export const showNotification = (message, type = 'info', duration = 5000) => {
  console.log("showNotification called with:", message, type, duration);
  window.dispatchEvent(new CustomEvent('showNotification', {
    detail: { message, type, duration }
  }));
};

export default NotificationManager;