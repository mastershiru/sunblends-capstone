import React from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faCheck, faSpinner, faTimes, faClipboardCheck, faShoppingBag, faXmark, faEye } from '@fortawesome/free-solid-svg-icons';
import { useNavbar } from '../../context/NavbarContext';
import "../../assets/css/modal.css";

const NotificationModal = () => {
  const {
    statusModalOpen,
    toggleStatusModal,
    statusModalData,
    viewOrderDetails
  } = useNavbar();
  
  if (!statusModalOpen || !statusModalData) return null;
  
  const getStatusIcon = () => {
    switch(statusModalData.status?.toLowerCase()) {
      case 'completed': return faClipboardCheck;
      case 'ready': return faCheck;
      case 'processing': return faSpinner;
      case 'cancelled': return faTimes;
      default: return faShoppingBag;
    }
  };
  
  const getStatusClass = () => {
    switch(statusModalData.status?.toLowerCase()) {
      case 'completed':
      case 'ready': 
        return 'status-success';
      case 'processing':
        return 'status-processing';
      case 'cancelled':
        return 'status-cancelled';
      default:
        return '';
    }
  };

  const handleViewDetails = () => {
    // Close this modal
    toggleStatusModal();
    
    // Open order details with the specific order ID
    if (statusModalData.order_id) {
      viewOrderDetails(statusModalData.order_id);
    }
  };

  return (
    <div className="modal-overlay">
      <div className="status-modal">
        <div className="modal-header">
          <h2>Order Update</h2>
          <button className="close-btn" onClick={toggleStatusModal}>
            <FontAwesomeIcon icon={faXmark} />
          </button>
        </div>
        
        <div className="modal-body">
          <div className={`status-icon ${getStatusClass()}`}>
            <FontAwesomeIcon icon={getStatusIcon()} size="3x" />
          </div>
          
          <h3 className={getStatusClass()}>
            {statusModalData.title || `Order ${statusModalData.status}`}
          </h3>
          
          <p className="status-message">
            {statusModalData.message || `Your order #${statusModalData.order_id} is now ${statusModalData.status}.`}
          </p>
          
          {statusModalData.timestamp && (
            <p className="status-time">
              {new Date(statusModalData.timestamp).toLocaleString()}
            </p>
          )}
        </div>
        
        <div className="modal-footer">
          <button className="btn-secondary" onClick={toggleStatusModal}>
            Close
          </button>
          
          {statusModalData.order_id && (
            <button className="btn-primary" onClick={handleViewDetails}>
              <FontAwesomeIcon icon={faEye} className="mr-2" />
              View Details
            </button>
          )}
        </div>
      </div>
    </div>
  );
};

export default NotificationModal;