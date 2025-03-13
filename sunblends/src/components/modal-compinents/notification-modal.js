import React from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faCheck, faSpinner, faTimes, faClipboardCheck, faShoppingBag, faXmark } from '@fortawesome/free-solid-svg-icons';

const NotificationModal = ({ isOpen, onClose, data, onViewOrder }) => {
  if (!isOpen || !data) return null;
  
  const getStatusIcon = () => {
    switch(data.status) {
      case 'completed': return faClipboardCheck;
      case 'ready': return faCheck;
      case 'processing': return faSpinner;
      case 'cancelled': return faTimes;
      default: return faShoppingBag;
    }
  };
  
  const getStatusClass = () => {
    switch(data.status) {
      case 'completed':
      case 'ready': 
        return 'text-green-600 bg-green-100';
      case 'processing': 
        return 'text-blue-600 bg-blue-100';
      case 'cancelled': 
        return 'text-red-600 bg-red-100';
      default: 
        return 'text-gray-600 bg-gray-100';
    }
  };
  
  const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleString([], {
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  };
  
  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 animate-fadeIn" style={{
      position: 'fixed',
      top: 0,
      left: 0,
      right: 0,
      bottom: 0,
      backgroundColor: 'rgba(0, 0, 0, 0.5)',
      zIndex: 1050,
      display: 'flex',
      justifyContent: 'center',
      alignItems: 'center',
    }}>
      <div style={{
        backgroundColor: 'white',
        borderRadius: '0.5rem',
        maxWidth: '500px',
        width: '90%',
        boxShadow: '0 10px 25px rgba(0, 0, 0, 0.2)',
        animation: 'bounceIn 0.5s'
      }}>
        {/* Header with status */}
        <div style={{
          borderBottom: '1px solid #e5e7eb',
          padding: '1rem',
          display: 'flex',
          justifyContent: 'space-between',
          alignItems: 'center'
        }}>
          <div style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
            <div className={getStatusClass()} style={{
              width: '40px',
              height: '40px',
              borderRadius: '50%',
              display: 'flex',
              alignItems: 'center',
              justifyContent: 'center',
            }}>
              <FontAwesomeIcon icon={getStatusIcon()} />
            </div>
            <h2 style={{ margin: 0, fontSize: '1.25rem', fontWeight: 'bold' }}>
              {data.title || `Order #${data.order_id} Update`}
            </h2>
          </div>
          <button
            onClick={onClose}
            style={{
              background: 'none',
              border: 'none',
              cursor: 'pointer',
              fontSize: '1.25rem'
            }}
          >
            <FontAwesomeIcon icon={faXmark} />
          </button>
        </div>
        
        {/* Body with message */}
        <div style={{ padding: '1.5rem' }}>
          <p style={{ fontSize: '1rem', marginBottom: '1rem' }}>
            {data.message}
          </p>
          
          {/* Order details */}
          <div style={{
            backgroundColor: '#f9fafb',
            borderRadius: '0.5rem',
            padding: '1rem',
            marginBottom: '1rem'
          }}>
            <div style={{
              display: 'flex',
              justifyContent: 'space-between',
              marginBottom: '0.5rem'
            }}>
              <span style={{ fontWeight: '500' }}>Order #:</span>
              <span>{data.order_id}</span>
            </div>
            
            {data.items_count && (
              <div style={{
                display: 'flex',
                justifyContent: 'space-between',
                marginBottom: '0.5rem'
              }}>
                <span style={{ fontWeight: '500' }}>Items:</span>
                <span>{data.items_count}</span>
              </div>
            )}
            
            {data.total_price && (
              <div style={{
                display: 'flex',
                justifyContent: 'space-between',
                marginBottom: '0.5rem'
              }}>
                <span style={{ fontWeight: '500' }}>Total:</span>
                <span>₱{parseFloat(data.total_price).toFixed(2)}</span>
              </div>
            )}
            
            {data.timestamp && (
              <div style={{
                display: 'flex',
                justifyContent: 'space-between'
              }}>
                <span style={{ fontWeight: '500' }}>Updated:</span>
                <span>{formatDate(data.timestamp)}</span>
              </div>
            )}
          </div>
        </div>
        
        {/* Footer with buttons */}
        <div style={{
          borderTop: '1px solid #e5e7eb',
          padding: '1rem',
          display: 'flex',
          justifyContent: 'flex-end',
          gap: '0.5rem'
        }}>
          <button
            onClick={onClose}
            style={{
              padding: '0.5rem 1rem',
              border: '1px solid #e5e7eb',
              backgroundColor: 'white',
              borderRadius: '0.25rem',
              cursor: 'pointer'
            }}
          >
            Close
          </button>
          
          <button
            onClick={() => {
              onClose();
              onViewOrder(data.order_id);
            }}
            style={{
              padding: '0.5rem 1rem',
              backgroundColor: '#ff8243',
              color: 'white',
              border: 'none',
              borderRadius: '0.25rem',
              cursor: 'pointer',
              fontWeight: '500'
            }}
            onMouseOver={(e) => e.currentTarget.style.backgroundColor = '#f97316'}
            onMouseOut={(e) => e.currentTarget.style.backgroundColor = '#ff8243'}
          >
            View Order Details
          </button>
        </div>
      </div>
    </div>
  );
};

export default NotificationModal;