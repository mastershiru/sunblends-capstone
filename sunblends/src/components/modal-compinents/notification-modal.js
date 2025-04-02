import React, { useEffect, useState } from "react";
import axios from "axios";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import {
  faXmark,
  faCheck,
  faSpinner,
  faBan,
  faCircleInfo,
  faCircleCheck,
  faEye,
  faTruck,
  faStore,
  faClock,
  faMapMarkerAlt,
  faUtensils,
  faBell,
  faCalendar,
  faMoneyBill,
  faUser,
  faPhone
} from "@fortawesome/free-solid-svg-icons";
import { useNavbar } from "../../context/NavbarContext";
import TokenManager from "../../utils/tokenManager";

const NotificationModal = () => {
  const {
    statusModalOpen,
    statusModalData,
    toggleStatusModal,
    viewOrderDetails,
    toggleModalOrders,
    isOpenOrders,
  } = useNavbar();
  
  const [orderDetails, setOrderDetails] = useState(null);
  const [cartItems, setCartItems] = useState([]);
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState(null);

  // Fetch order details when modal opens with orderId
  useEffect(() => {
    if (statusModalOpen && statusModalData?.order_id) {
      fetchOrderDetails(statusModalData.order_id);
    }
  }, [statusModalOpen, statusModalData]);

  // Add sound effect when modal appears
  useEffect(() => {
    if (statusModalOpen && statusModalData?.is_critical) {
      try {
        const audio = new Audio('/notification-sound.mp3');
        audio.volume = 0.7;
        audio.play().catch(e => console.log('Could not play notification sound', e));
      } catch (e) {
        console.log('Error playing sound', e);
      }
    }
  }, [statusModalOpen, statusModalData]);

  // Fetch detailed order information
  const fetchOrderDetails = async (orderId) => {
    setIsLoading(true);
    setError(null);
  
    try {
      // Get token from TokenManager
      const token = TokenManager.getToken();
      const headers = {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      };
  
      // Fetch order details
      const orderResponse = await axios.get(
        `http://127.0.0.1:8000/api/orders/${orderId}`,
        { headers }
      );
      
      // Get order items
      const cartResponse = await axios.get(
        `http://127.0.0.1:8000/api/orders/${orderId}/items`,
        { headers }
      );
  
      // Fix for missing order details
      const orderData = orderResponse.data;
      
      // Get the customer order details to fetch the missing information
      if (orderData.customer_id) {
        try {
          const customerOrdersResponse = await axios.get(
            `http://127.0.0.1:8000/api/orders/customer/${orderData.customer_id}`,
            { headers }
          );
          
          if (customerOrdersResponse.data && customerOrdersResponse.data.success) {
            // Find the complete order data with all fields
            const completeOrderData = customerOrdersResponse.data.orders.find(
              order => order.order_id == orderId
            );
            
            if (completeOrderData) {
              // Merge the data to get the missing fields
              setOrderDetails({
                ...orderData,
                delivery_option: completeOrderData.delivery_option,
                address: completeOrderData.address,
                pickup_in: completeOrderData.pickup_in,
                type_order: completeOrderData.type_order || completeOrderData.delivery_option,
                payment_method: completeOrderData.payment_method
              });
            } else {
              setOrderDetails(orderData);
            }
          } else {
            setOrderDetails(orderData);
          }
        } catch (error) {
          console.error("Error fetching additional order details:", error);
          setOrderDetails(orderData);
        }
      } else {
        setOrderDetails(orderData);
      }
      
      if (cartResponse.data && cartResponse.data.success) {
        setCartItems(cartResponse.data.items || []);
      } else {
        setCartItems([]);
      }
    } catch (error) {
      console.error("Error fetching order details:", error);
      setError("Failed to load order details");
    } finally {
      setIsLoading(false);
    }
  };

  // Don't render anything if modal is not open
  if (!statusModalOpen || !statusModalData) {
    return null;
  }

  // Handle view details click
  const handleViewDetails = () => {
    // Close this modal first
    toggleStatusModal();
    
    // Use the viewNotificationDetails flow instead of directly opening order details
    if (statusModalData.order_id) {
      // Check if orders modal is already open
      if (!isOpenOrders) {
        // First open the orders modal
        toggleModalOrders();
        
        // Wait for the modal to render, then highlight and open the specific order
        setTimeout(() => {
          // Dispatch an event to highlight the order
          const event = new CustomEvent("viewOrder", {
            detail: { orderId: statusModalData.order_id }
          });
          document.dispatchEvent(event);
        }, 300);
      } else {
        // If orders modal is already open, just trigger the order highlight
        const event = new CustomEvent("viewOrder", {
          detail: { orderId: statusModalData.order_id }
        });
        document.dispatchEvent(event);
      }
    }
  };

  // Get icon based on status
  const getStatusIcon = () => {
    const status = statusModalData.status?.toLowerCase();
    
    if (status === 'ready' && (statusModalData.delivery_method === 'delivery' || orderDetails?.delivery_option === 'delivery')) {
      return faTruck;
    } else if (status === 'ready') {
      return faCheck;
    } else if (status === 'completed') {
      return faCircleCheck;
    } else if (status === 'processing') {
      return faSpinner;
    } else if (status === 'cancelled') {
      return faBan;
    }
    
    return faCircleInfo;
  };

  // Get color based on status
  const getStatusColor = () => {
    const status = statusModalData.status?.toLowerCase();
    
    if (status === 'ready' || status === 'completed') {
      return '#10b981'; // Green
    } else if (status === 'processing') {
      return '#3b82f6'; // Blue
    } else if (status === 'cancelled') {
      return '#ef4444'; // Red
    }
    
    return '#6b7280'; // Gray
  };

  // Format time (helper function)
  const formatTime = (timeString) => {
    if (!timeString) return null;
    
    try {
      const date = new Date(timeString);
      return date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    } catch (e) {
      return timeString;
    }
  };
  
  // Format date (helper function)
  const formatDate = (dateString) => {
    if (!dateString) return null;
    
    try {
      const date = new Date(dateString);
      return date.toLocaleDateString([], {year: 'numeric', month: 'short', day: 'numeric'});
    } catch (e) {
      return dateString;
    }
  };
  
  // Format date and time
  const formatDateTime = (dateString) => {
    if (!dateString) return "N/A";
    try {
      const date = new Date(dateString);
      return date.toLocaleDateString() + " " + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    } catch {
      return "Invalid Date";
    }
  };

  // Determine if order is delivery based on available data
  const isDeliveryOrder = () => {
    // Check all possible sources of delivery information
    if (statusModalData?.delivery_method?.toLowerCase() === 'delivery') return true;
    if (orderDetails?.delivery_option?.toLowerCase() === 'delivery') return true;
    if (orderDetails?.type_order?.toLowerCase() === 'delivery') return true;
    
    // Check addresses - if an address is provided, it's likely a delivery
    if (statusModalData?.address && statusModalData.address.trim() !== '') return true;
    if (orderDetails?.address && orderDetails.address.trim() !== '') return true;
    
    // If we have pickup time set and no delivery indicators, it's likely pickup
    if (orderDetails?.pickup_in && !orderDetails?.delivered_in) return false;
    
    return false;
  };

  // Get delivery address
  const getDeliveryAddress = () => {
    if (statusModalData?.address && statusModalData.address.trim() !== '') {
      return statusModalData.address;
    }
    
    if (orderDetails?.address && orderDetails.address.trim() !== '') {
      return orderDetails.address;
    }
    
    // If we have customer info but no specific address
    if (orderDetails?.customer?.Customer_Address) {
      return orderDetails.customer.Customer_Address;
    }
    
    return "Please contact the customer for delivery address";
  };

  // Get pickup time
  const getPickupTime = () => {
    // If we have scheduled pickup time
    if (orderDetails?.pickup_in) {
      const pickupTime = formatDateTime(orderDetails.pickup_in);
      const now = new Date();
      const pickup = new Date(orderDetails.pickup_in);
      
      // If pickup time is in the past
      if (pickup < now) {
        return `${pickupTime} (Ready now)`;
      }
      
      return pickupTime;
    }
    
    // If order is already ready but no specific time
    if (statusModalData.status?.toLowerCase() === 'ready') {
      return "Ready now";
    }
    
    return "As soon as possible";
  };

  return (
    <div
      style={{
        position: "fixed",
        top: 0,
        left: 0,
        right: 0,
        bottom: 0,
        backgroundColor: "rgba(0, 0, 0, 0.7)",
        display: "flex",
        justifyContent: "center",
        alignItems: "center",
        zIndex: 9999,
      }}
      onClick={() => toggleStatusModal()} // Close when clicking outside
    >
      <div
        style={{
          backgroundColor: "white",
          borderRadius: "12px",
          width: "90%",
          maxWidth: "550px",
          maxHeight: "90vh",
          overflow: "auto",
          boxShadow: "0 10px 25px rgba(0, 0, 0, 0.3)",
          animation: statusModalData.is_critical ? "pulse 2s infinite" : "none",
          border: statusModalData.is_critical ? "3px solid #ff8243" : "none",
        }}
        onClick={(e) => e.stopPropagation()} // Prevent closing when clicking the modal itself
      >
        {/* Modal Header */}
        <div style={{
          display: "flex",
          justifyContent: "space-between",
          alignItems: "center",
          padding: "1rem 1.5rem",
          borderBottom: "1px solid #e5e7eb",
          backgroundColor: statusModalData.is_critical ? '#fffaf0' : 'white',
          position: "sticky",
          top: 0,
          zIndex: 1
        }}>
          <div style={{ display: "flex", alignItems: "center", gap: "0.5rem" }}>
            {statusModalData.is_critical && 
              <FontAwesomeIcon icon={faBell} style={{ color: "#ff8243" }} />
            }
            <h2 style={{ margin: 0, fontSize: "1.25rem", fontWeight: "600", color: "#1f2937" }}>
              {statusModalData.title || "Order Update"}
            </h2>
          </div>
          <button
            onClick={toggleStatusModal}
            style={{
              background: "none",
              border: "none",
              cursor: "pointer",
              fontSize: "1.25rem",
              color: "#6b7280"
            }}
          >
            <FontAwesomeIcon icon={faXmark} />
          </button>
        </div>

        {/* Modal Body */}
        <div style={{
          padding: "1.5rem",
          display: "flex",
          flexDirection: "column",
          alignItems: "center",
          textAlign: "center"
        }}>
          {/* Status Icon */}
          <div style={{
            width: "80px",
            height: "80px",
            borderRadius: "50%",
            backgroundColor: getStatusColor(),
            display: "flex",
            alignItems: "center",
            justifyContent: "center",
            color: "white",
            marginBottom: "1rem",
            boxShadow: "0 4px 10px rgba(0,0,0,0.1)"
          }}>
            <FontAwesomeIcon icon={getStatusIcon()} size="2x" />
          </div>

          {/* Status */}
          <h3 style={{ 
            color: getStatusColor(),
            margin: "0.5rem 0",
            fontSize: "1.5rem",
            fontWeight: "600"
          }}>
            {statusModalData.status?.toUpperCase() || "UPDATE"}
          </h3>

          {/* Message */}
          <p style={{
            margin: "1rem 0",
            fontSize: "1.1rem",
            lineHeight: "1.5",
            color: "#4b5563"
          }}>
            {statusModalData.message || `Your order #${statusModalData.order_id} is now ${statusModalData.status}.`}
          </p>
          
          {/* Loading state for order details */}
          {isLoading && (
            <div style={{ 
              display: "flex", 
              flexDirection: "column", 
              alignItems: "center", 
              padding: "1rem", 
              color: "#6b7280" 
            }}>
              <FontAwesomeIcon icon={faSpinner} spin size="2x" style={{ marginBottom: "0.5rem" }} />
              <p>Loading order details...</p>
            </div>
          )}
          
          {/* Error state */}
          {error && (
            <div style={{ 
              padding: "1rem", 
              backgroundColor: "#fee2e2", 
              color: "#ef4444", 
              borderRadius: "0.5rem", 
              width: "100%", 
              textAlign: "center" 
            }}>
              <p>{error}</p>
            </div>
          )}

          {/* Order Info Card */}
          {!isLoading && !error && (
            <div style={{
              width: "100%",
              margin: "1rem 0",
              padding: "1rem",
              backgroundColor: "#f9fafb",
              borderRadius: "8px",
              border: "1px solid #e5e7eb",
              textAlign: "left"
            }}>
              <div style={{ display: "flex", alignItems: "center", gap: "0.5rem", marginBottom: "1rem" }}>
                <FontAwesomeIcon icon={faUtensils} style={{ color: "#6b7280" }} />
                <strong style={{ fontSize: "1.1rem" }}>Order #{statusModalData.order_id}</strong>
              </div>
              
              {/* Order details grid */}
              <div style={{ 
                display: "grid", 
                gridTemplateColumns: "1fr 1fr",
                gap: "0.75rem 1rem",
                fontSize: "0.9rem",
                color: "#4b5563",
                marginBottom: "1rem"
              }}>
                {/* Date & Time */}
                <div style={{ display: "flex", alignItems: "center", gap: "0.5rem" }}>
                  <FontAwesomeIcon icon={faCalendar} style={{ color: "#6b7280", width: "16px" }} />
                  <span>
                    {formatDateTime(orderDetails?.created_at || statusModalData.timestamp)}
                  </span>
                </div>
                
                {/* Payment Method */}
                <div style={{ display: "flex", alignItems: "center", gap: "0.5rem" }}>
                  <FontAwesomeIcon icon={faMoneyBill} style={{ color: "#6b7280", width: "16px" }} />
                  <span style={{ textTransform: "capitalize" }}>
                    {orderDetails?.payment_method || "Cash"}
                  </span>
                </div>
                
                {/* Customer Name */}
                {(orderDetails?.customer?.Customer_Name || statusModalData.customer_name) && (
                  <div style={{ display: "flex", alignItems: "center", gap: "0.5rem" }}>
                    <FontAwesomeIcon icon={faUser} style={{ color: "#6b7280", width: "16px" }} />
                    <span>
                      {orderDetails?.customer?.Customer_Name || statusModalData.customer_name}
                    </span>
                  </div>
                )}
                
                {/* Customer Phone */}
                {orderDetails?.customer?.Customer_Number && (
                  <div style={{ display: "flex", alignItems: "center", gap: "0.5rem" }}>
                    <FontAwesomeIcon icon={faPhone} style={{ color: "#6b7280", width: "16px" }} />
                    <span>
                      {orderDetails?.customer?.Customer_Number}
                    </span>
                  </div>
                )}
              </div>
              
              {/* Divider */}
              <div style={{ 
                borderTop: "1px dashed #e5e7eb", 
                margin: "0.75rem 0", 
              }}></div>
              
              {/* Order Type */}
              <div style={{ 
                display: "flex", 
                justifyContent: "space-between", 
                margin: "0.5rem 0", 
                fontSize: "0.9rem", 
                color: "#6b7280" 
              }}>
                <span>Order Type:</span>
                <span style={{ fontWeight: "500", textTransform: "capitalize" }}>
                  {isDeliveryOrder() ? "Delivery" : "Pickup"}
                </span>
              </div>
              
              {/* Total Price */}
              {(statusModalData.total_price || orderDetails?.total_price) && (
                <div style={{ 
                  display: "flex", 
                  justifyContent: "space-between", 
                  margin: "0.5rem 0", 
                  fontSize: "0.9rem", 
                  color: "#6b7280" 
                }}>
                  <span>Total:</span>
                  <span style={{ fontWeight: "600", color: "#1f2937" }}>
                    ₱{parseFloat(statusModalData.total_price || orderDetails?.total_price || 0).toFixed(2)}
                  </span>
                </div>
              )}
              
              {/* Item Count */}
              {cartItems.length > 0 && (
                <div style={{ 
                  display: "flex", 
                  justifyContent: "space-between", 
                  margin: "0.5rem 0", 
                  fontSize: "0.9rem", 
                  color: "#6b7280" 
                }}>
                  <span>Items:</span>
                  <span style={{ fontWeight: "500" }}>
                    {cartItems.length} {cartItems.length === 1 ? 'item' : 'items'}
                  </span>
                </div>
              )}
            </div>
          )}
          
          {/* Order Items (if available) */}
          {!isLoading && !error && cartItems.length > 0 && (
            <div style={{
              width: "100%",
              margin: "1rem 0",
              border: "1px solid #e5e7eb",
              borderRadius: "8px",
              overflow: "hidden"
            }}>
              <div style={{
                backgroundColor: "#f8f9fa",
                padding: "0.75rem 1rem",
                fontWeight: "600",
                borderBottom: "1px solid #e5e7eb"
              }}>
                Order Items
              </div>
              
              <div style={{ maxHeight: "200px", overflowY: "auto" }}>
                {cartItems.map((item, index) => (
                  <div 
                    key={index}
                    style={{
                      padding: "0.75rem 1rem",
                      borderBottom: index < cartItems.length - 1 ? "1px solid #e5e7eb" : "none",
                      display: "flex",
                      alignItems: "center",
                      gap: "0.75rem"
                    }}
                  >
                    <img 
                      src={item.Item_Img || '/img/default-food.png'} 
                      alt={item.Item_Title}
                      style={{
                        width: "40px",
                        height: "40px",
                        objectFit: "cover",
                        borderRadius: "4px"
                      }}
                      onError={(e) => {
                        e.target.onerror = null;
                        e.target.src = '/img/default-food.png';
                      }}
                    />
                    
                    <div style={{ flex: 1 }}>
                      <div style={{ fontWeight: "500" }}>{item.Item_Title}</div>
                      {item.Item_Category && (
                        <div style={{ fontSize: "0.8rem", color: "#6b7280" }}>
                          {item.Item_Category}
                        </div>
                      )}
                    </div>
                    
                    <div style={{ textAlign: "right" }}>
                      <div>{item.Item_Quantity} × ₱{parseFloat(item.Item_Price).toFixed(2)}</div>
                      <div style={{ fontWeight: "600" }}>
                        ₱{(parseFloat(item.Item_Price) * parseInt(item.Item_Quantity)).toFixed(2)}
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* Pickup instructions */}
        {statusModalData.status?.toLowerCase() === "ready" && !isDeliveryOrder() && (
          <div style={{
            width: "100%",
            margin: "1.5rem 0",
            padding: "1.25rem",
            backgroundColor: "#fff3e0",
            borderRadius: "8px",
            borderLeft: "4px solid #ff8243",
            textAlign: "left"
          }}>
            <div style={{ display: "flex", alignItems: "center", gap: "0.5rem", marginBottom: "0.75rem" }}>
              <FontAwesomeIcon icon={faStore} style={{ color: "#ff8243" }} />
              <strong>Pickup Information</strong>
            </div>
            <p>Your order is ready and waiting for you at our store!</p>
            
            <div style={{ margin: "1rem 0", lineHeight: "1.5" }}>
              <div style={{ display: "flex", alignItems: "flex-start", gap: "0.5rem", marginBottom: "0.5rem" }}>
                <FontAwesomeIcon icon={faMapMarkerAlt} style={{ color: "#6b7280", marginTop: "0.2rem" }} />
                <div>
                  <strong>{orderDetails?.store_name || "Sunblends Cafe"}</strong><br />
                  {orderDetails?.store_address || "123 Sunblends St., Taguig City"}<br />
                  {orderDetails?.store_location || "2nd Floor, BGC, Metro Manila"}
                </div>
              </div>
              
              <div style={{ display: "flex", alignItems: "flex-start", gap: "0.5rem", marginTop: "0.75rem" }}>
                <FontAwesomeIcon icon={faClock} style={{ color: "#6b7280", marginTop: "0.2rem" }} />
                <div>
                  <strong>Pickup Time:</strong><br />
                  {getPickupTime()}
                </div>
              </div>
            </div>
            
            <div style={{ 
              marginTop: "1rem",
              padding: "0.75rem",
              backgroundColor: "rgba(255, 255, 255, 0.6)",
              borderRadius: "6px",
              fontSize: "0.9rem",
              color: "#4b5563",
              display: "flex",
              alignItems: "center",
              gap: "0.5rem"
            }}>
              <FontAwesomeIcon icon={faCircleInfo} style={{ color: "#ff8243" }} />
              <span>{orderDetails?.pickup_instructions || "Please show your order number when you arrive. Your food will stay fresh for the next 30 minutes."}</span>
            </div>
          </div>
        )}


        {statusModalData.status?.toLowerCase() === "ready" && isDeliveryOrder() && (
          <div style={{
            width: "100%",
            margin: "1.5rem 0",
            padding: "1.25rem",
            backgroundColor: "#e3f2fd",
            borderRadius: "8px",
            borderLeft: "4px solid #3b82f6",
            textAlign: "left"
          }}>
            <div style={{ display: "flex", alignItems: "center", gap: "0.5rem", marginBottom: "0.75rem" }}>
              <FontAwesomeIcon icon={faTruck} style={{ color: "#3b82f6" }} />
              <strong>Delivery Information</strong>
            </div>
            <p>{orderDetails?.delivery_message || "Your order has been picked up by our delivery partner!"}</p>
            
            <div style={{ 
              display: "flex", 
              alignItems: "flex-start", 
              gap: "0.5rem", 
              margin: "0.75rem 0",
              padding: "0.75rem",
              backgroundColor: "rgba(255, 255, 255, 0.5)",
              borderRadius: "6px"
            }}>
              <FontAwesomeIcon icon={faMapMarkerAlt} style={{ color: "#3b82f6", marginTop: "0.2rem" }} />
              <div>
                <strong>Delivery Address:</strong><br />
                {getDeliveryAddress()}
              </div>
            </div>
            
            <div style={{ 
              display: "flex", 
              justifyContent: "space-between", 
              margin: "0.75rem 0", 
              padding: "0.75rem",
              backgroundColor: "rgba(255, 255, 255, 0.5)", 
              borderRadius: "6px",
              alignItems: "center"
            }}>
              <div style={{ display: "flex", alignItems: "center", gap: "0.5rem" }}>
                <FontAwesomeIcon icon={faClock} style={{ color: "#3b82f6" }} />
                <span>Estimated arrival:</span>
              </div>
              <span style={{ fontWeight: "600", color: "#3b82f6" }}>
                {orderDetails?.delivery_estimate || "15-30 minutes"}
              </span>
            </div>
            
            {orderDetails?.delivered_in && (
              <div style={{ 
                display: "flex", 
                justifyContent: "space-between", 
                margin: "0.75rem 0", 
                padding: "0.75rem",
                backgroundColor: "rgba(255, 255, 255, 0.5)", 
                borderRadius: "6px",
                alignItems: "center"
              }}>
                <div style={{ display: "flex", alignItems: "center", gap: "0.5rem" }}>
                  <FontAwesomeIcon icon={faClock} style={{ color: "#3b82f6" }} />
                  <span>Expected delivery by:</span>
                </div>
                <span style={{ fontWeight: "600", color: "#3b82f6" }}>
                  {formatDateTime(orderDetails.delivered_in)}
                </span>
              </div>
            )}
            
            <div style={{ 
              marginTop: "1rem",
              padding: "0.75rem",
              backgroundColor: "rgba(255, 255, 255, 0.6)",
              borderRadius: "6px",
              fontSize: "0.9rem",
              color: "#4b5563",
              display: "flex",
              alignItems: "center",
              gap: "0.5rem"
            }}>
              <FontAwesomeIcon icon={faCircleInfo} style={{ color: "#3b82f6" }} />
              <span>{orderDetails?.delivery_instructions || "The delivery driver will contact you when they arrive. Please have your phone ready."}</span>
            </div>
          </div>
        )}


        {statusModalData.status?.toLowerCase() === "completed" && (
          <div style={{
            width: "100%",
            margin: "1.5rem 0",
            padding: "1.25rem",
            backgroundColor: isDeliveryOrder() ? "#e3f2fd" : "#fff3e0",
            borderRadius: "8px",
            borderLeft: `4px solid ${isDeliveryOrder() ? "#3b82f6" : "#ff8243"}`,
            textAlign: "left"
          }}>
            <div style={{ display: "flex", alignItems: "center", gap: "0.5rem", marginBottom: "0.75rem" }}>
              <FontAwesomeIcon 
                icon={isDeliveryOrder() ? faTruck : faStore} 
                style={{ color: isDeliveryOrder() ? "#3b82f6" : "#ff8243" }} 
              />
              <strong>Order Completed</strong>
            </div>
            
            <p>
              {isDeliveryOrder() 
                ? (orderDetails?.completion_message_delivery || "Your order has been delivered successfully! We hope you enjoy your meal.")
                : (orderDetails?.completion_message_pickup || "Your order has been picked up. Thank you for choosing Sunblends Cafe!")}
            </p>
            
            {/* Show completed time if available */}
            {orderDetails?.completed_at && (
              <div style={{ 
                display: "flex", 
                alignItems: "flex-start", 
                gap: "0.5rem", 
                margin: "0.75rem 0",
                padding: "0.75rem",
                backgroundColor: "rgba(255, 255, 255, 0.5)",
                borderRadius: "6px"
              }}>
                <FontAwesomeIcon 
                  icon={faClock} 
                  style={{ color: isDeliveryOrder() ? "#3b82f6" : "#ff8243", marginTop: "0.2rem" }} 
                />
                <div>
                  <strong>Completed on:</strong><br />
                  {formatDateTime(orderDetails.completed_at)}
                </div>
              </div>
            )}
            
            <div style={{ 
              marginTop: "1rem",
              padding: "0.75rem",
              backgroundColor: "rgba(255, 255, 255, 0.6)",
              borderRadius: "6px",
              fontSize: "0.9rem",
              color: "#4b5563"
            }}>
              {orderDetails?.feedback_message || "We hope you enjoyed your experience with us! Please consider leaving a review or feedback on your experience."}
            </div>
          </div>
        )}

          {/* Order ID and timestamp */}
          <div style={{
            width: "100%",
            display: "flex",
            justifyContent: "space-between",
            marginTop: "1.5rem",
            paddingTop: "1rem",
            borderTop: "1px dashed #e5e7eb",
            color: "#6b7280",
            fontSize: "0.875rem"
          }}>
            <div>Order #{statusModalData.order_id}</div>
            {statusModalData.timestamp && (
              <div style={{ color: "#9ca3af" }}>
                {new Date(statusModalData.timestamp).toLocaleString()}
              </div>
            )}
          </div>
        </div>

        {/* Modal Footer */}
        <div style={{
          padding: "1rem 1.5rem",
          display: "flex",
          justifyContent: "center",
          gap: "1rem",
          borderTop: "1px solid #e5e7eb",
          backgroundColor: "#f9fafb"
        }}>
          <button
            onClick={toggleStatusModal}
            style={{
              padding: "0.5rem 1.25rem",
              backgroundColor: "#f3f4f6",
              color: "#4b5563",
              border: "none",
              borderRadius: "0.25rem",
              cursor: "pointer",
              fontWeight: "500",
              transition: "background-color 0.2s"
            }}
            onMouseOver={e => e.currentTarget.style.backgroundColor = "#e5e7eb"}
            onMouseOut={e => e.currentTarget.style.backgroundColor = "#f3f4f6"}
          >
            Close
          </button>
          
          {statusModalData.order_id && (
            <button
              onClick={handleViewDetails}
              style={{
                padding: "0.5rem 1.25rem",
                backgroundColor: "#ff8243",
                color: "white",
                border: "none",
                borderRadius: "0.25rem",
                cursor: "pointer",
                display: "flex",
                alignItems: "center",
                gap: "0.5rem",
                fontWeight: "500",
                transition: "background-color 0.2s"
              }}
              onMouseOver={e => e.currentTarget.style.backgroundColor = "#f97316"}
              onMouseOut={e => e.currentTarget.style.backgroundColor = "#ff8243"}
            >
              <FontAwesomeIcon icon={faEye} />
              View Details
            </button>
          )}
        </div>
      </div>

      <style jsx>{`
        @keyframes pulse {
          0% { box-shadow: 0 0 0 0 rgba(255, 130, 67, 0.7); }
          70% { box-shadow: 0 0 0 15px rgba(255, 130, 67, 0); }
          100% { box-shadow: 0 0 0 0 rgba(255, 130, 67, 0); }
        }
      `}</style>
    </div>
  );
};

export default NotificationModal;