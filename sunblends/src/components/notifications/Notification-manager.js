import React, { useState, useEffect, useCallback } from 'react';
import { useNavbar } from '../../context/NavbarContext';


const NotificationManager = () => {
  const [notifications, setNotifications] = useState([]);
  const { 
    setHasNewNotification, 
    setNotificationBadgeCount,
    addNotification 
  } = useNavbar();
  
  // Listen for custom notification events
  useEffect(() => {
    const handleNewNotification = (event) => {
      if (event?.detail) {
        const notification = {
          id: Date.now(),
          message: event.detail.message || 'New notification',
          type: event.detail.type || 'info',
          duration: event.detail.duration || 5000
        };
        
        addToNotifications(notification);
        
        // Update the navbar notification badge
        setHasNewNotification(true);
        setNotificationBadgeCount(prev => prev + 1);
        
        // Send to NavbarContext if it's an order notification
        if (event.detail.orderId) {
          addNotification({
            id: notification.id,
            title: 'Order Update',
            message: event.detail.message,
            status: event.detail.status,
            orderId: event.detail.orderId,
            timestamp: new Date(),
            read: false
          });
        }
      }
    };
    
    // Add event listener for custom notification event
    window.addEventListener('show-notification', handleNewNotification);
    
    return () => {
      window.removeEventListener('show-notification', handleNewNotification);
    };
  }, [setHasNewNotification, setNotificationBadgeCount, addNotification]);
  
  const addToNotifications = useCallback((notification) => {
    setNotifications(prev => [...prev, notification]);
    
    // Auto-remove after duration
    if (notification.duration !== Infinity) {
      setTimeout(() => {
        removeNotification(notification.id);
      }, notification.duration);
    }
  }, []);
  
  const removeNotification = useCallback((id) => {
    setNotifications(prev => prev.filter(notification => notification.id !== id));
  }, []);
  
  if (notifications.length === 0) {
    return null;
  }
  
  return (
    <div className="notification-container">
      {notifications.map(notification => (
        <div 
          key={notification.id} 
          className={`notification notification-${notification.type}`}
        >
          <div className="notification-content">
            <p>{notification.message}</p>
          </div>
          <button 
            className="notification-close"
            onClick={() => removeNotification(notification.id)}
          >
            ×
          </button>
        </div>
      ))}
    </div>
  );
};

// Helper function to show notifications from anywhere
export const showNotification = (message, type = 'info', duration = 5000, options = {}) => {
  const event = new CustomEvent('show-notification', {
    detail: {
      message,
      type,
      duration,
      ...options
    }
  });
  
  window.dispatchEvent(event);
};

export default NotificationManager;