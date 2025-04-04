import React, { useState, useEffect } from "react";
import axios from "axios";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { useNavbar } from "../../context/NavbarContext";
import TokenManager from "../../utils/tokenManager";
import {
  faXmark,
  faSpinner,
  faEye,
  faCheck,
  faClock,
  faExclamationTriangle,
  faShoppingBag,
  faCalendarAlt,
  faUser,
  faUsers,
} from "@fortawesome/free-solid-svg-icons";
import "../../assets/css/modal.css";

// Order Details Component
const OrderDetails = ({
  isOpenOrderDetails,
  toggleOrderDetails,
  selectedOrderId,
  order,
}) => {
  const [orderDetails, setOrderDetails] = useState(null);
  const [cartItems, setCartItems] = useState([]);
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState(null);
  const [ratings, setRatings] = useState({});

  // Fallback image as base64
  const fallbackImageSrc =
    "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADwAAAA8CAYAAAA6/NlyAAAACXBIWXMAAAsTAAALEwEAmpwYAAABsUlEQVR4nO3av2oUURTH8c8aFexs7K0ELQQfwMbKN7ASfAQrwdoHsLGxsLERBBvBwkIQJJWVEIidhQmIpZUQMCDsJhPIwjK7O3Nn5pzZ+4XTDPf+frn3zOyZgSRJkiRJkvTfOwbcAZaAdeA7MA9cGCHfBHAfeAV8Bb4AK8DdOGZlp4DrwAfKLQPXauSdAlaHyPsRuFkjZ6uuAJ965D2Ix3rpDHAD+Bb/3gNOD9PRJPC8R+GbPdqPxzXYLfszcKlH+0ngeaTZBK6WNH4a6a1twIvAAZuF128DB0PamwPmCu83gKfA+ZKc+4V2a8DFko4vFdpsFZ6rGisd140yOxEDFG2WDNZaoV3ZgO/G666V2o1sLmkcOQJjw+pt1Vr6QkfnexjPw8bdXHzrTO31vgTsxuOP6ArMtJT3TvQNcKyl3KN1EngT/ay0lTzVYuEHwOm2O0mNH1O7wL0uOkmFHzK7wJ0uOkmFB0u7wO0uOkmFXzS7wK0uOkmFJ+NmN37sl/jZZTep8Gj9G/B/oCJJkiRJkiRJkiRJkiRJkiRJ0pD+AvsYaqkq8MmZAAAAAElFTkSuQmCC";

  // Fetch order details when modal opens
  useEffect(() => {
    if (isOpenOrderDetails && selectedOrderId) {
      fetchOrderDetails(selectedOrderId);
    }
  }, [isOpenOrderDetails, selectedOrderId]);

  useEffect(() => {
    if (isOpenOrderDetails && selectedOrderId && cartItems.length > 0) {
      // Fetch existing ratings for items in this order
      const fetchExistingRatings = async () => {
        try {
          const token = TokenManager.getToken();
          const headers = {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          };
          
          // For each item in the cart, check if it has been rated
          const ratingPromises = cartItems.map(item => 
            axios.get(`http://127.0.0.1:8000/api/ratings/check`, {
              params: {
                dish_id: item.Item_Dish_ID,
                order_id: selectedOrderId
              },
              headers
            })
          );
          
          const results = await Promise.all(ratingPromises);
          
          // Update the ratings state
          const newRatings = {};
          results.forEach((result, index) => {
            if (result.data.success && result.data.has_rating) {
              newRatings[cartItems[index].Item_ID] = result.data.rating;
            }
          });
          
          setRatings(newRatings);
        } catch (error) {
          console.error("Error fetching existing ratings:", error);
        }
      };
      
      fetchExistingRatings();
    }
  }, [isOpenOrderDetails, selectedOrderId, cartItems]);

  const fetchOrderDetails = async (orderId) => {
    setIsLoading(true);
    setError(null);

    try {
      // Get token from TokenManager
      const token = TokenManager.getToken();
      const headers = {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      };

      // Fetch order details
      const orderResponse = await axios.get(
        `http://127.0.0.1:8000/api/orders/${orderId}`,
        { headers }
      );

      setOrderDetails(orderResponse.data);

      // Get order items
      const cartResponse = await axios.get(
        `http://127.0.0.1:8000/api/orders/${orderId}/items`,
        { headers }
      );

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

  const handleSubmitRating = async (dishId, rating, itemId) => {
    try {
      // Show a loading indicator or disable the stars while submitting
      setIsLoading(true);
      
      // Get token from TokenManager
      const token = TokenManager.getToken();
      const headers = {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      };
      
      // Submit the rating to the API
      const response = await axios.post(
        'http://127.0.0.1:8000/api/dishes/rate',
        {
          dish_id: dishId,
          order_id: selectedOrderId,
          rating: rating
        },
        { headers }
      );
      
      if (response.data.success) {
        // Show success message
        alert("Thank you for rating this dish!");
        
        // Update the local state to show the rating is saved - using itemId as key
        const newRatings = { ...ratings };
        newRatings[itemId] = rating;
        setRatings(newRatings);
      } else {
        alert(response.data.message || "Failed to submit rating. Please try again.");
      }
    } catch (error) {
      console.error("Error submitting rating:", error);
      alert("An error occurred while submitting your rating. Please try again.");
    } finally {
      setIsLoading(false);
    }
  };

  // Format date
  const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    try {
      const date = new Date(dateString);
      return date.toLocaleDateString() + " " + date.toLocaleTimeString();
    } catch {
      return "Invalid Date";
    }
  };

  // Calculate total
  const calculateTotal = () => {
    if (!cartItems || !cartItems.length) return 0;

    return cartItems.reduce((total, item) => {
      const price = parseFloat(item.Item_Price) || 0;
      const quantity = parseInt(item.Item_Quantity) || 0;
      return total + price * quantity;
    }, 0);
  };

  // Get status color
  const getStatusColor = (status) => {
    if (!status) return "#6c757d";

    switch (status?.toLowerCase()) {
      case "completed":
        return "#28a745";
      case "processing":
        return "#007bff";
      case "ready":
        return "#17a2b8";
      case "cancelled":
        return "#dc3545";
      default:
        return "#6c757d";
    }
  };

  return (
    <>
      {isOpenOrderDetails && (
        <div
          className="modal-backdrop"
          style={{
            position: "fixed",
            top: 0,
            left: 0,
            right: 0,
            bottom: 0,
            backgroundColor: "rgba(0, 0, 0, 0.5)",
            display: "flex",
            justifyContent: "center",
            alignItems: "center",
            zIndex: 9999,
            padding: "20px",
          }}
        >
          <div
            className="modal-content"
            style={{
              backgroundColor: "white",
              borderRadius: "8px",
              width: "100%",
              maxWidth: "550px",
              maxHeight: "80vh",
              overflowY: "auto",
              boxShadow: "0 5px 15px rgba(0, 0, 0, 0.3)",
            }}
          >
            {/* Modal Header */}
            <div
              style={{
                padding: "15px 20px",
                borderBottom: "1px solid #eee",
                display: "flex",
                justifyContent: "space-between",
                alignItems: "center",
                position: "sticky",
                top: 0,
                backgroundColor: "white",
                zIndex: 5,
              }}
            >
              <h2
                style={{
                  margin: 0,
                  fontSize: "1.25rem",
                  display: "flex",
                  alignItems: "center",
                  gap: "10px",
                  width: "300px",
                }}
              >
                {orderDetails && orderDetails.status && (
                  <span
                    style={{
                      display: "inline-block",
                      width: "14px",
                      height: "10px",
                      borderRadius: "50%",
                      backgroundColor: getStatusColor(orderDetails.status),
                    }}
                  ></span>
                )}
                Order Details{" "}
                {orderDetails && `#${orderDetails.order_id || ""}`}
              </h2>
              <button
                onClick={() => toggleOrderDetails(false)}
                style={{
                  background: "none",
                  border: "none",
                  fontSize: "1.25rem",
                  cursor: "pointer",
                  padding: "0",
                  color: "#999",
                  textAlign: "right",
                }}
              >
                <FontAwesomeIcon icon={faXmark} />
              </button>
            </div>

            {/* Modal Body */}
            <div style={{ padding: "20px" }}>
              {isLoading ? (
                <div style={{ textAlign: "center", padding: "30px" }}>
                  <FontAwesomeIcon icon={faSpinner} spin size="2x" />
                  <p style={{ marginTop: "10px" }}>Loading order details...</p>
                </div>
              ) : error ? (
                <div
                  style={{ textAlign: "center", padding: "20px", color: "red" }}
                >
                  <p>{error}</p>
                  <button
                    onClick={() => fetchOrderDetails(selectedOrderId)}
                    style={{
                      padding: "6px 12px",
                      backgroundColor: "#ff8243",
                      color: "white",
                      border: "none",
                      borderRadius: "4px",
                      cursor: "pointer",
                      marginTop: "10px",
                    }}
                  >
                    Try Again
                  </button>
                </div>
              ) : orderDetails ? (
                <>
                  {/* Order Summary */}
                  <div style={{ marginBottom: "20px" }}>
                    <div
                      style={{
                        display: "flex",
                        justifyContent: "space-between",
                        padding: "10px 15px",
                        backgroundColor: getStatusColor(orderDetails.status),
                        color: "white",
                        borderRadius: "6px",
                        marginBottom: "15px",
                        alignItems: "center",
                      }}
                    >
                      <span style={{ fontWeight: "bold" }}>
                        Status:{" "}
                        {orderDetails.status
                          ? orderDetails.status.toUpperCase()
                          : "PENDING"}
                      </span>
                      <span>{formatDate(orderDetails.created_at)}</span>
                    </div>

                    <div
                      style={{
                        display: "grid",
                        gridTemplateColumns: "1fr 1fr",
                        gap: "10px",
                        fontSize: "0.9rem",
                      }}
                    >
                      <div>
                        <strong>Customer:</strong>{" "}
                        {orderDetails.customer?.Customer_Name || "N/A"}
                      </div>
                      <div>
                        <strong>Total:</strong> ₱
                        {parseFloat(orderDetails.total_price || 0).toFixed(2)}
                      </div>
                      <div>
                        <strong>Phone:</strong>{" "}
                        {orderDetails.customer?.Customer_Number || "N/A"}
                      </div>
                      <div>
                        <strong>Items:</strong> {cartItems.length}
                      </div>
                    </div>
                  </div>

                  {/* Order Items */}
                  <div
                    style={{
                      border: "1px solid #eee",
                      borderRadius: "6px",
                      marginBottom: "15px",
                      overflow: "hidden",
                    }}
                  >
                    <div
                      style={{
                        backgroundColor: "#f8f9fa",
                        padding: "10px 15px",
                        fontWeight: "bold",
                        fontSize: "0.9rem",
                        borderBottom: "1px solid #eee",
                      }}
                    >
                      Order Items
                    </div>

                    {cartItems.length > 0 ? (
                      <div>
                        {cartItems.map((item, index) => (
                          <div
                            key={index}
                            style={{
                              padding: "10px 15px",
                              display: "flex",
                              alignItems: "center",
                              gap: "10px",
                              borderBottom:
                                index < cartItems.length - 1
                                  ? "1px solid #eee"
                                  : "none",
                            }}
                          >
                            <img
                              src={item.Item_Img || fallbackImageSrc}
                              alt={item.Item_Title || "Item"}
                              style={{
                                width: "40px",
                                height: "40px",
                                objectFit: "cover",
                                borderRadius: "4px",
                              }}
                              onError={(e) => {
                                e.target.onerror = null;
                                e.target.src = fallbackImageSrc;
                              }}
                            />
                            <div style={{ flex: 1 }}>
                              <div style={{ fontWeight: "500" }}>
                                {item.Item_Title || "Unknown Item"}
                              </div>
                              <div
                                style={{ fontSize: "0.8rem", color: "#666" }}
                              >
                                {item.Item_Category || ""}
                              </div>
                              {orderDetails?.status?.toLowerCase() === "completed" && (
                              <div
                                className="rating-container"
                                style={{
                                  borderTop: "1px solid #eee",
                                  padding: "8px 0",
                                  marginTop: "6px",
                                  textAlign: "left"
                                }}
                              >
                                <div style={{ 
                                  fontSize: "15px", 
                                  display: "flex", 
                                  alignItems: "center",
                                  justifyContent: "space-between" 
                                }}>
                                  <div>
                                    <small style={{ color: "#666", marginRight: "5px" }}>Rate this dish:</small>
                                    {[1, 2, 3, 4, 5].map((star) => (
                                      <span
                                        key={star}
                                        onClick={() => {
                                          if (!isLoading && !ratings[item.Item_ID]) {
                                            // Pass item ID as third parameter
                                            handleSubmitRating(item.Item_Dish_ID, star, item.Item_ID);
                                          }
                                        }}
                                        style={{
                                          cursor: (isLoading || ratings[item.Item_ID]) ? "default" : "pointer",
                                          color: star <= (ratings[item.Item_ID] || 0) ? "#ffd700" : "#ccc",
                                          fontSize: "18px",
                                          transition: "color 0.2s",
                                          marginRight: "2px",
                                          userSelect: "none" // Prevent text selection
                                        }}
                                        onMouseEnter={(e) => {
                                          if (!isLoading && !ratings[item.Item_ID]) {
                                            // Only show hover effect if not already rated
                                            const stars = e.target.parentNode.childNodes;
                                            for (let i = 1; i < stars.length; i++) { // Start at 1 to skip the "Rate this dish" text
                                              if (i <= star) {
                                                stars[i].style.color = "#ffcc00";
                                              }
                                            }
                                          }
                                        }}
                                        onMouseLeave={(e) => {
                                          if (!isLoading && !ratings[item.Item_ID]) {
                                            // Only reset on hover if not already rated
                                            const stars = e.target.parentNode.childNodes;
                                            for (let i = 1; i < stars.length; i++) { // Start at 1 to skip the "Rate this dish" text
                                              stars[i].style.color = "#ccc"; 
                                              // Apply gold color back to rated stars
                                              if (i <= (ratings[item.Item_ID] || 0)) {
                                                stars[i].style.color = "#ffd700";
                                              }
                                            }
                                          }
                                        }}
                                      >
                                        ★
                                      </span>
                                    ))}
                                  </div>
                                  {ratings[item.Item_ID] && (
                                    <small style={{ color: "#28a745", fontStyle: "italic" }}>
                                      Rating submitted ({ratings[item.Item_ID]}★)
                                    </small>
                                  )}
                                </div>
                              </div>
                            )}
                            </div>
                            <div
                              style={{
                                display: "flex",
                                flexDirection: "column",
                                alignItems: "flex-end",
                              }}
                            >
                              <div style={{ fontWeight: "500" }}>
                                {item.Item_Quantity || 0} × ₱
                                {parseFloat(item.Item_Price || 0).toFixed(2)}
                              </div>
                              <div>
                                ₱
                                {(
                                  parseFloat(item.Item_Price || 0) *
                                  parseInt(item.Item_Quantity || 0)
                                ).toFixed(2)}
                              </div>
                            </div>
                          </div>
                        ))}

                        <div
                          style={{
                            backgroundColor: "#f8f9fa",
                            padding: "10px 15px",
                            fontWeight: "bold",
                            borderTop: "1px solid #eee",
                            display: "flex",
                            justifyContent: "space-between",
                          }}
                        >
                          <span>Total</span>
                          <span>₱{calculateTotal().toFixed(2)}</span>
                        </div>
                      </div>
                    ) : (
                      <div
                        style={{
                          padding: "20px",
                          textAlign: "center",
                          color: "#666",
                        }}
                      >
                        No items found for this order.
                      </div>
                    )}
                  </div>
                </>
              ) : (
                <div
                  style={{
                    textAlign: "center",
                    padding: "20px",
                    color: "#666",
                  }}
                >
                  <p>No order selected</p>
                </div>
              )}
            </div>

            {/* Modal Footer */}
            <div
              style={{
                padding: "15px 20px",
                borderTop: "1px solid #eee",
                display: "flex",
                justifyContent: "flex-end",
                position: "sticky",
                bottom: 0,
                backgroundColor: "white",
              }}
            >
              <button
                onClick={() => toggleOrderDetails(false)}
                style={{
                  padding: "8px 16px",
                  backgroundColor: "#ff8243",
                  color: "white",
                  border: "none",
                  borderRadius: "4px",
                  cursor: "pointer",
                  fontWeight: "500",
                }}
              >
                Close
              </button>
            </div>
          </div>
        </div>
      )}
    </>
  );
};

// Main Orders Component
const Orders = ({ isOpenOrders, toggleModalOrders }) => {
  const { userData, isLoggedIn } = useNavbar();

  const [orders, setOrders] = useState([]);
  const [reservations, setReservations] = useState([]);
  const [loading, setLoading] = useState(false);
  const [loadingReservations, setLoadingReservations] = useState(false);
  const [error, setError] = useState(null);
  const [reservationError, setReservationError] = useState(null);
  const [selectedOrderId, setSelectedOrderId] = useState(null);
  const [isOrderDetailsOpen, setIsOrderDetailsOpen] = useState(false);
  const [selectedOrder, setSelectedOrder] = useState(null);
  const [activeTab, setActiveTab] = useState("orders"); // "orders" or "reservations"

  // Update the orders fetching logic
  useEffect(() => {
    if (isOpenOrders && isLoggedIn && userData) {
      fetchOrders();
      fetchReservations();
    }
  }, [isOpenOrders, isLoggedIn, userData]);

  useEffect(() => {
    const handleViewOrderEvent = (event) => {
      if (event.detail?.orderId) {
        highlightOrder(event.detail.orderId);
      }
    };

    // Add event listener
    document.addEventListener("viewOrder", handleViewOrderEvent);

    // Clean up event listener on unmount
    return () => {
      document.removeEventListener("viewOrder", handleViewOrderEvent);
    };
  }, [orders]);

  const handleCancelOrder = async (orderId, e) => {
    e.stopPropagation();
    
    if (!window.confirm("Are you sure you want to cancel this order?")) {
      return;
    }
    
    try {
      setLoading(true);
      
      const token = TokenManager.getToken();
      const headers = {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      };
      
      const response = await axios.post(
        `http://127.0.0.1:8000/api/orders/${orderId}/cancel`,
        {},
        { headers }
      );
      
      if (response.data.success) {
        // Update the local orders state to reflect the change
        setOrders(orders.map(order => {
          if (order.order_id === orderId) {
            return { ...order, status: 'cancelled' };
          }
          return order;
        }));
        
        alert("Order cancelled successfully");
      } else {
        alert(response.data.message || "Failed to cancel order");
      }
    } catch (error) {
      console.error("Error cancelling order:", error);
      alert("An error occurred while cancelling your order. Please try again.");
    } finally {
      setLoading(false);
    }
  };

  // Fetch reservations
  const fetchReservations = async () => {
    setLoadingReservations(true);
    setReservationError(null);

    try {
      // Get user ID from userData in context
      if (!userData || !userData.customer_id) {
        throw new Error("User data not found");
      }

      const token = TokenManager.getToken();
      const headers = {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      };

      const response = await axios.get(
        `http://127.0.0.1:8000/api/reservations/customer/${userData.customer_id}`,
        { headers }
      );

      if (response.data && response.data.success) {
        setReservations(response.data.reservations);
      } else {
        throw new Error("Failed to fetch reservations");
      }
    } catch (err) {
      console.error("Error fetching reservations:", err);
      setReservationError("Failed to load your reservations. Please try again.");
    } finally {
      setLoadingReservations(false);
    }
  };

  // Update the fetchOrders function:
  const fetchOrders = async () => {
    setLoading(true);
    setError(null);

    try {
      // Get user ID from userData in context
      if (!userData || !userData.customer_id) {
        throw new Error("User data not found");
      }

      const token = TokenManager.getToken();
      const headers = {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      };

      const response = await axios.get(
        `http://127.0.0.1:8000/api/orders/customer/${userData.customer_id}`,
        { headers }
      );

      if (response.data && response.data.success) {
        setOrders(response.data.orders);
      } else {
        throw new Error("Failed to fetch orders");
      }
    } catch (err) {
      console.error("Error fetching orders:", err);
      setError("Failed to load your orders. Please try again.");
    } finally {
      setLoading(false);
    }
  };

  const handleViewDetails = (order) => {
    setSelectedOrderId(order.order_id);
    setSelectedOrder(order);
    setIsOrderDetailsOpen(true);
  };

  // Handle cancelling a reservation
  const handleCancelReservation = async (reservationId, e) => {
    e.stopPropagation();
    
    if (!window.confirm("Are you sure you want to cancel this reservation?")) {
      return;
    }
    
    try {
      setLoadingReservations(true);
      
      const token = TokenManager.getToken();
      const headers = {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      };
      
      const response = await axios.post(
        `http://127.0.0.1:8000/api/reservations/${reservationId}/cancel`,
        {},
        { headers }
      );
      
      if (response.data.success) {
        // Update the local reservations state to reflect the change
        setReservations(reservations.map(reservation => {
          if (reservation.reservation_id === reservationId) {
            return { ...reservation, reservation_status: 'cancelled' };
          }
          return reservation;
        }));
        
        alert("Reservation cancelled successfully");
      } else {
        alert(response.data.message || "Failed to cancel reservation");
      }
    } catch (error) {
      console.error("Error cancelling reservation:", error);
      alert("An error occurred while cancelling your reservation. Please try again.");
    } finally {
      setLoadingReservations(false);
    }
  };

  // Highlight an order when it's selected from a notification
  const highlightOrder = (orderId) => {
    console.log("Highlighting order:", orderId);
    setActiveTab("orders");

    // Find the order element
    setTimeout(() => {
      const orderElement = document.getElementById(`order-${orderId}`);
      if (orderElement) {
        // Scroll to the order
        orderElement.scrollIntoView({ behavior: "smooth", block: "center" });

        // Highlight with animation
        orderElement.classList.add("highlight-order");
        setTimeout(() => {
          orderElement.classList.remove("highlight-order");
        }, 2000);
      }

      // Find the order data
      const order = orders.find((o) => o.order_id == orderId);
      if (order) {
        console.log("Found order to highlight:", order);
        // Open order details
        handleViewDetails(order);
      } else {
        console.warn("Order not found in list:", orderId);
      }
    }, 300);
  };

  // Get color based on order status
  const getStatusColor = (status) => {
    if (!status) return "#6c757d";

    switch (status.toLowerCase()) {
      case "completed":
        return "#28a745";
      case "processing":
        return "#007bff";
      case "ready":
        return "#17a2b8";
      case "cancelled":
        return "#dc3545";
      case "pending":
        return "#ffc107";
      default:
        return "#6c757d";
    }
  };

  // Get icon based on order status
  const getStatusIcon = (status) => {
    if (!status) return faShoppingBag;

    switch (status.toLowerCase()) {
      case "completed":
        return faCheck;
      case "processing":
        return faSpinner;
      case "ready":
        return faClock;
      case "cancelled":
        return faExclamationTriangle;
      default:
        return faShoppingBag;
    }
  };

  // Format date
  const formatDate = (dateString) => {
    if (!dateString) return "";

    const options = {
      year: "numeric",
      month: "short",
      day: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    };

    try {
      return new Date(dateString).toLocaleDateString(undefined, options);
    } catch (e) {
      return dateString;
    }
  };

  // Format date without time
  const formatDateOnly = (dateString) => {
    if (!dateString) return "";

    const options = {
      year: "numeric",
      month: "short",
      day: "numeric",
    };

    try {
      return new Date(dateString).toLocaleDateString(undefined, options);
    } catch (e) {
      return dateString;
    }
  };

  // Format time
  const formatTime = (timeString) => {
    if (!timeString) return "";

    try {
      // Parse time string (assuming format like "14:30:00")
      const [hours, minutes] = timeString.split(':');
      const hour = parseInt(hours);
      const ampm = hour >= 12 ? 'PM' : 'AM';
      const hour12 = hour % 12 || 12; // Convert to 12-hour format
      
      return `${hour12}:${minutes} ${ampm}`;
    } catch (e) {
      return timeString;
    }
  };

  return (
    <>
      {isOpenOrders && (
        <div
          className="modal-backdrop"
          style={{
            position: "fixed",
            top: 0,
            left: 0,
            right: 0,
            bottom: 0,
            backgroundColor: "rgba(0, 0, 0, 0.5)",
            display: "flex",
            justifyContent: "center",
            alignItems: "center",
            zIndex: 9999,
            padding: "20px",
          }}
        >
          <div
            className="modal-content"
            style={{
              backgroundColor: "white",
              borderRadius: "8px",
              width: "100%",
              maxWidth: "700px",
              maxHeight: "80vh",
              overflowY: "auto",
              overflowX: "hidden",
              boxShadow: "0 5px 15px rgba(0, 0, 0, 0.3)",
            }}
          >
            {/* Modal Header */}
            <div
              style={{
                padding: "15px 20px",
                borderBottom: "1px solid #eee",
                display: "flex",
                justifyContent: "space-between",
                alignItems: "center",
                position: "sticky",
                top: 0,
                backgroundColor: "white",
                zIndex: 5,
              }}
            >
              <h2 style={{ margin: 0, width: "500px" }}>My History</h2>
              <button
                onClick={toggleModalOrders}
                style={{
                  background: "none",
                  border: "none",
                  fontSize: "1.25rem",
                  cursor: "pointer",
                  padding: "0",
                  color: "#999",
                  textAlign: "right",
                }}
              >
                <FontAwesomeIcon icon={faXmark} />
              </button>
            </div>

            {/* Tabs Navigation */}
            <div style={{ 
              display: 'flex', 
              borderBottom: '1px solid #eee',
              backgroundColor: '#f9f9f9',
            }}>
              <button 
                onClick={() => setActiveTab("orders")}
                style={{
                  flex: 1,
                  padding: '12px',
                  fontSize: '14px',
                  fontWeight: activeTab === "orders" ? 'bold' : 'normal',
                  backgroundColor: activeTab === "orders" ? '#ff8243' : 'transparent',
                  border: 'none',
                  borderBottom: activeTab === "orders" ? '3px solid #ff8243' : 'none',
                  cursor: 'pointer',
                  
                  color: activeTab === "orders" ? 'white' : '#333'
                }}
              >
                <FontAwesomeIcon icon={faShoppingBag} style={{ marginRight: '8px' }} />
                Orders
              </button>
              <button 
                onClick={() => setActiveTab("reservations")}
                style={{
                  flex: 1,
                  padding: '12px',
                  fontSize: '14px',
                  fontWeight: activeTab === "reservations" ? 'bold' : 'normal',
                  backgroundColor: activeTab === "reservations" ? '#ff8243' : 'transparent',
                  border: 'none',
                  borderBottom: activeTab === "reservations" ? '3px solid #ff8243' : 'none',
                  cursor: 'pointer',
                  color: activeTab === "reservations" ? 'white' : '#333'
                }}
              >
                <FontAwesomeIcon icon={faCalendarAlt} style={{ marginRight: '8px' }} />
                Reservations
              </button>
            </div>

            {/* Orders Tab */}
            {activeTab === "orders" && (
              <div style={{ padding: "20px" }}>
                {loading ? (
                  <div style={{ textAlign: "center", padding: "30px" }}>
                    <FontAwesomeIcon icon={faSpinner} spin size="2x" />
                    <p style={{ marginTop: "10px" }}>Loading your orders...</p>
                  </div>
                ) : error ? (
                  <div style={{ textAlign: "center", padding: "30px", color: "red" }}>
                    <p>{error}</p>
                    <button
                      onClick={fetchOrders}
                      style={{
                        padding: "8px 16px",
                        backgroundColor: "#ff8243",
                        color: "white",
                        border: "none",
                        borderRadius: "4px",
                        cursor: "pointer",
                        marginTop: "10px",
                      }}
                    >
                      Try Again
                    </button>
                  </div>
                ) : orders.length === 0 ? (
                  <div style={{ textAlign: "center", padding: "30px", color: "#666" }}>
                    <p>You don't have any orders yet.</p>
                    <button
                      onClick={() => {
                        toggleModalOrders();
                        // Scroll to menu section
                        document
                          .getElementById("menu")
                          ?.scrollIntoView({ behavior: "smooth" });
                      }}
                      style={{
                        padding: "8px 16px",
                        backgroundColor: "#ff8243",
                        color: "white",
                        border: "none",
                        borderRadius: "4px",
                        cursor: "pointer",
                        marginTop: "10px",
                      }}
                    >
                      Order Now
                    </button>
                  </div>
                ) : (
                  <div className="orders-list">
                    {orders.map((order) => (
                      <div
                        key={order.order_id}
                        id={`order-${order.order_id}`}
                        style={{
                          backgroundColor: "#f9f9f9",
                          borderRadius: "8px",
                          marginBottom: "15px",
                          padding: "15px",
                          boxShadow: "0 2px 4px rgba(0,0,0,0.05)",
                          transition: "all 0.3s ease",
                          cursor: "pointer",
                        }}
                        onClick={() => handleViewDetails(order)}
                      >
                        <div
                          style={{
                            display: "flex",
                            justifyContent: "space-between",
                            alignItems: "center",
                          }}
                        >
                          <div style={{ display: "flex", alignItems: "center", gap: "10px" }}>
                            <div
                              style={{
                                backgroundColor: getStatusColor(order.status),
                                width: "36px",
                                height: "36px",
                                borderRadius: "50%",
                                display: "flex",
                                alignItems: "center",
                                justifyContent: "center",
                                color: "white",
                              }}
                            >
                              <FontAwesomeIcon icon={getStatusIcon(order.status)} />
                            </div>

                            <div>
                              <h3 style={{ margin: 0, fontSize: "1.05rem" }}>
                                Order #{order.order_id}
                              </h3>
                              <div style={{ fontSize: "0.85rem", color: "#666", marginTop: "2px" }}>
                                {formatDate(order.created_at)}
                              </div>
                            </div>
                          </div>

                          <div style={{ textAlign: "right" }}>
                            <div
                              style={{
                                fontSize: "1rem",
                                fontWeight: "600",
                                marginBottom: "5px",
                              }}
                            >
                              ₱{parseFloat(order.total_price || 0).toFixed(2)}
                            </div>
                            <div style={{ display: "flex", gap: "10px", alignItems: "center" }}>
                              <div
                                style={{
                                  textTransform: "uppercase",
                                  fontSize: "0.7rem",
                                  fontWeight: "bold",
                                  padding: "3px 8px",
                                  borderRadius: "4px",
                                  display: "inline-block",
                                  backgroundColor: getStatusColor(order.status),
                                  color: "white",
                                }}
                              >
                                {order.status || "pending"}
                              </div>

                              {(!order.status ||
                                order.status.toLowerCase() === "pending" ||
                                order.status.toLowerCase() === "processing") && (
                                <button
                                  onClick={(e) => handleCancelOrder(order.order_id, e)}
                                  style={{
                                    padding: "3px 8px",
                                    backgroundColor: "#dc3545",
                                    color: "white",
                                    border: "none",
                                    borderRadius: "4px",
                                    cursor: "pointer",
                                    fontSize: "0.7rem",
                                    fontWeight: "bold",
                                  }}
                                >
                                  CANCEL
                                </button>
                              )}
                            </div>
                          </div>
                        </div>
                      </div>
                    ))}
                  </div>
                )}
              </div>
            )}

            {/* Reservations Tab */}
            {activeTab === "reservations" && (
              <div style={{ padding: "20px" }}>
                {loadingReservations ? (
                  <div style={{ textAlign: "center", padding: "30px" }}>
                    <FontAwesomeIcon icon={faSpinner} spin size="2x" />
                    <p style={{ marginTop: "10px" }}>Loading your reservations...</p>
                  </div>
                ) : reservationError ? (
                  <div style={{ textAlign: "center", padding: "30px", color: "red" }}>
                    <p>{reservationError}</p>
                    <button
                      onClick={fetchReservations}
                      style={{
                        padding: "8px 16px",
                        backgroundColor: "#ff8243",
                        color: "white",
                        border: "none",
                        borderRadius: "4px",
                        cursor: "pointer",
                        marginTop: "10px",
                      }}
                    >
                      Try Again
                    </button>
                  </div>
                ) : reservations.length === 0 ? (
                  <div style={{ textAlign: "center", padding: "30px", color: "#666" }}>
                    <p>You don't have any reservations yet.</p>
                    <button
                      onClick={() => {
                        toggleModalOrders();
                        // Scroll to reservation section
                        document
                          .getElementById("reservation")
                          ?.scrollIntoView({ behavior: "smooth" });
                      }}
                      style={{
                        padding: "8px 16px",
                        backgroundColor: "#ff8243",
                        color: "white",
                        border: "none",
                        borderRadius: "4px",
                        cursor: "pointer",
                        marginTop: "10px",
                      }}
                    >
                      Book a Table
                    </button>
                  </div>
                ) : (
                  <div className="reservations-list">
                    {reservations.map((reservation) => (
                      <div
                        key={reservation.reservation_id}
                        id={`reservation-${reservation.reservation_id}`}
                        style={{
                          backgroundColor: "#f9f9f9",
                          borderRadius: "8px",
                          marginBottom: "15px",
                          padding: "15px",
                          boxShadow: "0 2px 4px rgba(0,0,0,0.05)",
                          transition: "all 0.3s ease",
                        }}
                      >
                        <div
                          style={{
                            display: "flex",
                            justifyContent: "space-between",
                            alignItems: "flex-start",
                          }}
                        >
                          <div style={{ display: "flex", alignItems: "center", gap: "10px" }}>
                            <div
                              style={{
                                backgroundColor: getStatusColor(reservation.reservation_status),
                                width: "36px",
                                height: "36px",
                                borderRadius: "50%",
                                display: "flex",
                                alignItems: "center",
                                justifyContent: "center",
                                color: "white",
                              }}
                            >
                              <FontAwesomeIcon icon={faCalendarAlt} />
                            </div>

                            <div>
                              <h3 style={{ margin: 0, fontSize: "1.05rem" }}>
                                Reservation #{reservation.reservation_id}
                              </h3>
                              <div style={{ fontSize: "0.85rem", color: "#666", marginTop: "2px" }}>
                                Booked on: {formatDate(reservation.created_at)}
                              </div>
                            </div>
                          </div>

                          <div style={{ textAlign: "right" }}>
                            <div style={{ display: "flex", gap: "10px", alignItems: "center" }}>
                              <div
                                style={{
                                  textTransform: "uppercase",
                                  fontSize: "0.7rem",
                                  fontWeight: "bold",
                                  padding: "3px 8px",
                                  borderRadius: "4px",
                                  display: "inline-block",
                                  backgroundColor: getStatusColor(reservation.reservation_status),
                                  color: "white",
                                }}
                              >
                                {reservation.reservation_status || "pending"}
                              </div>

                              {(reservation.reservation_status === "pending") && (
                                <button
                                  onClick={(e) => handleCancelReservation(reservation.reservation_id, e)}
                                  style={{
                                    padding: "3px 8px",
                                    backgroundColor: "#dc3545",
                                    color: "white",
                                    border: "none",
                                    borderRadius: "4px",
                                    cursor: "pointer",
                                    fontSize: "0.7rem",
                                    fontWeight: "bold",
                                  }}
                                >
                                  CANCEL
                                </button>
                              )}
                            </div>
                          </div>
                        </div>

                        {/* Reservation Details */}
                        <div
                          style={{
                            marginTop: "12px",
                            backgroundColor: "white",
                            borderRadius: "6px",
                            padding: "10px",
                            display: "grid",
                            gridTemplateColumns: "repeat(3, 1fr)",
                            gap: "10px",
                          }}
                        >
                          <div>
                            <div style={{ fontSize: "0.8rem", color: "#666" }}>Date</div>
                            <div style={{ fontWeight: "500", display: "flex", alignItems: "center", gap: "5px" }}>
                              <FontAwesomeIcon icon={faCalendarAlt} style={{ color: "#ff8243" }} />
                              {formatDateOnly(reservation.reservation_date)}
                            </div>
                          </div>
                          
                          <div>
                            <div style={{ fontSize: "0.8rem", color: "#666" }}>Time</div>
                            <div style={{ fontWeight: "500", display: "flex", alignItems: "center", gap: "5px" }}>
                              <FontAwesomeIcon icon={faClock} style={{ color: "#ff8243" }} />
                              {formatTime(reservation.reservation_time)}
                            </div>
                          </div>
                          
                          <div>
                            <div style={{ fontSize: "0.8rem", color: "#666" }}>Guests</div>
                            <div style={{ fontWeight: "500", display: "flex", alignItems: "center", gap: "5px" }}>
                              <FontAwesomeIcon icon={reservation.reservation_people > 1 ? faUsers : faUser} style={{ color: "#ff8243" }} />
                              {reservation.reservation_people} {reservation.reservation_people > 1 ? 'People' : 'Person'}
                            </div>
                          </div>
                        </div>
                      </div>
                    ))}
                  </div>
                )}
              </div>
            )}

            {/* Modal Footer */}
            <div
              style={{
                padding: "15px 20px",
                borderTop: "1px solid #eee",
                display: "flex",
                justifyContent: "flex-end",
                position: "sticky",
                bottom: 0,
                backgroundColor: "white",
              }}
            >
              <button
                onClick={toggleModalOrders}
                style={{
                  padding: "8px 16px",
                  backgroundColor: "#ff8243",
                  color: "white",
                  border: "none",
                  borderRadius: "4px",
                  cursor: "pointer",
                  fontWeight: "500",
                }}
              >
                Close
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Order Details Modal */}
      <OrderDetails
        isOpenOrderDetails={isOrderDetailsOpen}
        toggleOrderDetails={setIsOrderDetailsOpen}
        selectedOrderId={selectedOrderId}
        order={selectedOrder}
      />

      {/* Add some CSS for highlight animation */}
      <style>
        {`
          @keyframes highlight {
            0% { background-color: #f9f9f9; }
            50% { background-color: #fff3e0; }
            100% { background-color: #f9f9f9; }
          }
          
          .highlight-order {
            animation: highlight 2s;
            border: 2px solid #ff8243 !important;
          }
        `}
      </style>
    </>
  );
};

export default Orders;