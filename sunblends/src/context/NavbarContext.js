import React, {
  createContext,
  useState,
  useContext,
  useEffect,
  useRef,
} from "react";
import axios from "axios";
import TokenManager from "../utils/tokenManager";
import Pusher from "pusher-js";
import Echo from "laravel-echo";
import { showNotification } from "../components/notifications/Notification-manager";
import { toast } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";

const NavbarContext = createContext();

export const NavbarProvider = ({ children }) => {
  // User authentication state
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const [userData, setUserData] = useState(null);
  const [isInitializing, setIsInitializing] = useState(true);

  // UI state for navbar
  const [isDropdownOpen, setIsDropdownOpen] = useState(false);
  const [isOpen, setIsOpen] = useState(false); // Mobile nav
  const dropdownRef = useRef(null);

  // Cart state
  const [cartNumber, setCartNumber] = useState(0);
  const [cartItems, setCartItems] = useState([]);

  // Modal states
  const [isOpenLogin, setIsOpenLogin] = useState(false);
  const [isOpenRegister, setIsOpenRegister] = useState(false);
  const [isOpenCart, setIsOpenCart] = useState(false);
  const [isOpenEditProfile, setIsOpenEditProfile] = useState(false);
  const [isOpenOrders, setIsOpenOrders] = useState(false);
  const [isOpenCheckout, setIsOpenCheckout] = useState(false);

  // Notification state
  const [hasNewNotification, setHasNewNotification] = useState(false);
  const [notificationBadgeCount, setNotificationBadgeCount] = useState(0);
  const [isNotificationCenterOpen, setIsNotificationCenterOpen] =
    useState(false);
  const [notifications, setNotifications] = useState([]);

  // Order details state
  const [isOpenOrderDetails, setIsOpenOrderDetails] = useState(false);
  const [selectedOrderId, setSelectedOrderId] = useState(null);
  const [selectedOrder, setSelectedOrder] = useState(null);
  const [statusModalOpen, setStatusModalOpen] = useState(false);
  const [statusModalData, setStatusModalData] = useState(null);
  const [isLoading, setIsLoading] = useState(false);

  // API URL
  process.env.REACT_APP_API_URL || "http://127.0.0.1:8000/api"

  // Check if user is logged in when the component mounts
  useEffect(() => {
    const initializeAuth = async () => {
      setIsInitializing(true);

      try {
        // First check for TokenManager's in-memory token
        if (TokenManager.hasToken()) {
          // Validate that the token is still valid
          const isValid = await TokenManager.validateToken();

          if (isValid) {
            const userFromToken = TokenManager.getUser();
            setIsLoggedIn(true);
            setUserData(userFromToken);
          } else {
            // If token is invalid, try to refresh
            tryRefreshSession();
          }
        }
        // If no in-memory token but we had a session, try to refresh
        else if (TokenManager.hadSession()) {
          tryRefreshSession();
        }
        // Fall back to localStorage for compatibility with existing code
        else {
          fallbackToLocalStorage();
        }
      } catch (error) {
        console.error("Authentication initialization error:", error);
        // Clear any invalid auth data
        TokenManager.clearToken();
      } finally {
        setIsInitializing(false);
      }
    };

    // Function to try refreshing the session
    const tryRefreshSession = async () => {
      const refreshResult = await TokenManager.refreshSession();

      if (refreshResult.success) {
        setIsLoggedIn(true);
        setUserData(refreshResult.user);
      } else if (refreshResult.requireLogin) {
        // Token was manually revoked or user needs to login again
        setIsLoggedIn(false);
        setUserData(null);
        // Show login modal
        setIsOpenLogin(true);
        // Show message to user
        alert(
          refreshResult.message ||
            "Your session has expired. Please login again."
        );
      } else {
        // If refresh failed, check fallback localStorage method
        fallbackToLocalStorage();
      }
    };

    const fallbackToLocalStorage = () => {
      const storedUserData = localStorage.getItem("userData");
      const storedIsLoggedIn = localStorage.getItem("isLoggedIn");
      const token = localStorage.getItem("token");

      if (storedIsLoggedIn === "true" && storedUserData && token) {
        try {
          const parsedUserData = JSON.parse(storedUserData);

          // Store in TokenManager for future use
          TokenManager.setToken(token, parsedUserData);

          // Update state
          setIsLoggedIn(true);
          setUserData(parsedUserData);
        } catch (error) {
          console.error("Error parsing stored user data:", error);
          // Clear invalid data
          TokenManager.clearToken();
        }
      }
    };

    initializeAuth();
  }, []);

  useEffect(() => {
    if (TokenManager.hasToken()) {
      TokenManager.validateToken().catch(() => {
        // Token validation failed, force logout
        TokenManager.clearToken();
        window.location.reload();
      });
    }
  }, []);

  useEffect(() => {
    const handleForceReauth = (event) => {
      console.log("Force reauth event received:", event.detail);

      // Clear auth state
      setIsLoggedIn(false);
      setUserData(null);
      setCartItems([]);
      setCartNumber(0);

      // Show login modal with a small delay
      setTimeout(() => {
        setIsOpenLogin(true);

        // Show message
        const message =
          event.detail?.message ||
          "Your session has expired. Please login again.";
        alert(message);
      }, 300);
    };

    // Listen for force reauth events
    document.addEventListener("forceReauthentication", handleForceReauth);

    return () => {
      document.removeEventListener("forceReauthentication", handleForceReauth);
    };
  }, []);

  // Save user data to localStorage when logged in (for backward compatibility)
  useEffect(() => {
    if (isLoggedIn && userData) {
      localStorage.setItem("isLoggedIn", "true");
      localStorage.setItem("userData", JSON.stringify(userData));
    } else if (!isLoggedIn) {
      localStorage.removeItem("isLoggedIn");
      localStorage.removeItem("userData");
    }
  }, [isLoggedIn, userData]);

  useEffect(() => {
    if (isLoggedIn && userData?.customer_id && !isInitializing) {
      // Fetch cart data when auth state is initialized
      fetchCartFromServer();
    }
  }, [isLoggedIn, userData, isInitializing]);

  // Fetch notifications when user logs in
  useEffect(() => {
    if (isLoggedIn && userData?.customer_id && !isInitializing) {
      fetchNotifications();

      // Set up polling to refresh notifications every 30 seconds
      const intervalId = setInterval(() => {
        fetchNotifications(true); // silent refresh
      }, 30000);

      return () => clearInterval(intervalId);
    } else {
      // Clear notifications when logged out
      setNotifications([]);
      setNotificationBadgeCount(0);
      setHasNewNotification(false);
    }
  }, [isLoggedIn, userData, isInitializing]);

  // Initialize Echo once component mounts
  useEffect(() => {
    // Initialize WebSocket connection
    try {
      console.log("Setting up WebSocket connection...");

      window.Echo = new Echo({
        broadcaster: "reverb",
        key: "sunblends-key",
        cluster: "mt1", // Add this line - using default cluster
        wsHost: window.location.hostname, // Use dynamic hostname instead of hardcoded "localhost"
        wsPort: 8080,
        disableStats: true,
        forceTLS: false,
        encrypted: false,
        enabledTransports: ["ws", "wss"],
        authEndpoint: `${API_BASE_URL}/broadcasting/auth`,
        auth: TokenManager.hasToken()
          ? {
              headers: {
                Authorization: `Bearer ${TokenManager.getToken()}`,
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
              },
            }
          : undefined,
      });

      console.log("WebSocket connection initialized successfully");

      // Listen on public channel for all order updates
      const publicChannel = window.Echo.channel("orders");
      publicChannel.listen(".OrderStatusChanged", (data) => {
        console.log("Received OrderStatusChanged event:", data);
        handleOrderStatusUpdate(data);
      });

      // Listen on private channel for this user's orders
      if (isLoggedIn && userData?.customer_id) {
        const privateChannel = window.Echo.private(
          `customer.${userData.customer_id}`
        );
        privateChannel.listen(".OrderStatusChanged", (data) => {
          console.log("Received private OrderStatusChanged event:", data);
          handlePrivateOrderUpdate(data);
        });
        console.log(
          `Subscribed to private channel: customer.${userData.customer_id}`
        );
      }
    } catch (error) {
      console.error("Error setting up WebSocket connection:", error);
    }

    return () => {
      if (window.Echo) {
        console.log("Disconnecting WebSocket...");
        window.Echo.disconnect();
      }
    };
  }, [isLoggedIn, userData]);

  // Handle order status updates from public channel
  const handleOrderStatusUpdate = (data) => {
    console.log("Processing order status update:", data);

    // Extract order data
    const orderData = data.orderData || data;
    console.log("Extracted order data:", orderData);

    // Check if this is for the current user
    if (
      !isLoggedIn ||
      !userData ||
      orderData.customer_id !== userData.customer_id
    ) {
      console.log("Order update not for current user, ignoring");
      return; // Not for this user
    }

    console.log("Order update is for current user, processing...");

    // Always show a toast notification
    showNotification(
      `Order #${orderData.order_id} is now ${orderData.status}`,
      getNotificationType(orderData.status),
      5000
    );

    // Refresh notifications list silently
    fetchNotifications(true);

    // Check if this is a critical status that needs immediate attention
    const isCriticalStatus = ["ready", "completed"].includes(
      orderData.status?.toLowerCase()
    );

    if (isCriticalStatus) {
      console.log("Critical status detected, showing immediate modal");

      // Play notification sound first

      // Determine delivery type for custom message
      const isDelivery = orderData.delivery_option;

      console.log("Delivery type:", isDelivery ? "delivery" : "pickup");

      // Create status-specific message
      let title, message;

      if (orderData.status?.toLowerCase() === "ready") {
        title = isDelivery
          ? "Your Order is On the Way!"
          : "Your Order is Ready for Pickup!";

        message = isDelivery
          ? "Your food is ready and out for delivery! It will arrive at your location soon."
          : "Great news! Your order is now ready for pickup. Please come to our store.";
      } else {
        // completed
        title = "Your Order is Complete!";
        message = isDelivery
          ? "Your order has been delivered successfully. Enjoy your meal!"
          : "Your order has been completed. Thank you for visiting Sunblends!";
      }

      // Show the immediate modal
      console.log("Showing modal with title:", title);
      showImmediateStatusModal({
        order_id: orderData.order_id,
        status: orderData.status,
        title: title,
        message: message,
        delivery_method: isDelivery ? "delivery" : "pickup",
        timestamp: new Date().toISOString(),
        is_critical: true,
      });
    } else {
      console.log("Non-critical status update, not showing modal");
    }
  };

  // Handle private notifications (already filtered for this user)
  const handlePrivateOrderUpdate = (data) => {
    console.log("Processing private order update:", data);

    // Extract notification data
    const notificationData = data.orderData || data;

    // Show toast notification
    showNotification(
      notificationData.message ||
        `Order #${notificationData.order_id} status updated to ${notificationData.status}`,
      notificationData.notify_type ||
        getNotificationType(notificationData.status),
      5000
    );

    // Refresh notifications from server
    fetchNotifications(true); // silent refresh

    handleOrderStatusUpdate(data);

    // CRITICAL STATUSES - Ready or Completed
    const criticalStatuses = ["ready", "completed"];
    if (criticalStatuses.includes(notificationData.status?.toLowerCase())) {
      // Determine message based on delivery method
      let customMessage = "";
      const isDelivery = notificationData.delivery_option;

      if (notificationData.status?.toLowerCase() === "ready") {
        customMessage = isDelivery
          ? "Your order is ready and out for delivery! It will arrive soon."
          : "Your order is ready for pickup! Please come to our store to get your food.";
      } else if (notificationData.status?.toLowerCase() === "completed") {
        customMessage = isDelivery
          ? "Your order has been delivered successfully! Enjoy your meal."
          : "Your order has been completed. Thank you for picking up your food!";
      }

      // Use a timeout to ensure the notification appears even if user is in another tab
      setTimeout(() => {
        showNotificationModal({
          ...notificationData,
          // Add special flag for critical notifications
          is_critical: true,
          // Add custom message based on delivery type
          custom_message: customMessage,
        });
      }, 500);
    }
    // Still show modal for other important updates
    else {
      showNotificationModal(notificationData);
    }
  };

  const showImmediateStatusModal = (data) => {
    console.log("Showing immediate status modal:", data);

    // Set the modal data
    setStatusModalData(data);

    // Open the modal
    setStatusModalOpen(true);

    // Play sound again just to be sure
  };

  // Get notification type based on status
  const getNotificationType = (status) => {
    switch (status) {
      case "completed":
      case "ready":
        return "success";
      case "processing":
        return "info";
      case "cancelled":
        return "error";
      default:
        return "info";
    }
  };

  // Check if status is important
  const isImportantStatus = (status) => {
    return ["completed", "cancelled", "ready", "processing"].includes(
      status?.toLowerCase()
    );
  };

  // Show notification modal
  const showNotificationModal = (notificationData) => {
    // Create more descriptive title based on status
    let title = "Order Update";
    let message = notificationData.custom_message || null;

    // If no custom message was provided, create default title and message
    if (!message) {
      if (notificationData.status?.toLowerCase() === "ready") {
        title = "Your Order is Ready for Pickup!";
        message =
          notificationData.description ||
          notificationData.message ||
          "Your order is hot and ready! Come and get it while it's fresh.";
      } else if (notificationData.status?.toLowerCase() === "completed") {
        title = "Your Order is Complete!";
        message =
          notificationData.description ||
          notificationData.message ||
          "Great news! Your order has been completed.";
      } else if (notificationData.status?.toLowerCase() === "processing") {
        title = "Your Order is Being Prepared";
        message =
          notificationData.description ||
          notificationData.message ||
          "Our kitchen is now preparing your delicious order.";
      } else if (notificationData.status?.toLowerCase() === "cancelled") {
        title = "Your Order has been Cancelled";
        message =
          notificationData.description ||
          notificationData.message ||
          "We're sorry, but your order has been cancelled. Please contact us for assistance.";
      } else {
        // Default fallback message
        message =
          notificationData.description ||
          notificationData.message ||
          getStatusMessage(notificationData.status).modalMessage;
      }
    }

    // Set modal data with enhanced information
    setStatusModalData({
      order_id: notificationData.order_id,
      status: notificationData.status,
      message: message,
      total_price: notificationData.total_price,
      title: title,
      is_critical: notificationData.is_critical || false,
      delivery_method:
        notificationData.delivery_method || notificationData.order_type,
    });

    // Open the status modal
    setStatusModalOpen(true);

    // Play sound for critical notifications (ready/completed)
    if (
      notificationData.is_critical ||
      ["ready", "completed"].includes(notificationData.status?.toLowerCase())
    ) {
      try {
        // Try to play notification sound
        const audio = new Audio("/notification-sound.mp3");
        audio
          .play()
          .catch((e) => console.log("Could not play notification sound", e));
      } catch (e) {
        console.log("Error playing sound", e);
      }
    }
  };

  // Get appropriate status messages
  const getStatusMessage = (status) => {
    switch (status?.toLowerCase()) {
      case "completed":
        return {
          toastMessage: "Your order is completed!",
          modalMessage:
            "Great news! Your order has been completed and is ready for pickup/delivery.",
        };
      case "processing":
        return {
          toastMessage: "Your order is being prepared!",
          modalMessage: "Our kitchen is now preparing your delicious order.",
        };
      case "ready":
        return {
          toastMessage: "Your order is ready for pickup!",
          modalMessage:
            "Your order is hot and ready! Come and get it while it's fresh.",
        };
      case "cancelled":
        return {
          toastMessage: "Your order has been cancelled.",
          modalMessage:
            "We're sorry, but your order has been cancelled. Please contact us for assistance.",
        };
      default:
        return {
          toastMessage: `Order status changed to ${status}`,
          modalMessage: `Your order status has been updated to ${status}.`,
        };
    }
  };

  // Function to fetch notifications from the server
  const fetchNotifications = async (silent = false) => {
    if (!userData?.customer_id) return;

    if (!silent) setIsLoading(true);

    try {
      const token = TokenManager.getToken();
      const headers = {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
        "Content-Type": "application/json",
      };

      const response = await axios.get(
        `${API_BASE_URL}/notifications?customer_id=${userData.customer_id}`,
        { headers }
      );

      if (response.data.success) {
        // Process notifications
        const notificationsData = response.data.notifications || [];

        // Map to consistent format
        const formattedNotifications = notificationsData.map(
          (notification) => ({
            id: notification.id,
            message:
              notification.message ||
              `Order #${notification.order_id} status update`,
            order_id: notification.order_id,
            status: notification.status || "info",
            timestamp: notification.timestamp || notification.created_at,
            read: notification.read || false,
            data: notification.data || {},
          })
        );

        setNotifications(formattedNotifications);

        // Count unread notifications
        const unreadCount = formattedNotifications.filter(
          (n) => !n.read
        ).length;
        setNotificationBadgeCount(unreadCount);
        setHasNewNotification(unreadCount > 0);
      } else {
        console.error("Error in notification response:", response.data);
      }
    } catch (error) {
      console.error("Failed to fetch notifications:", error);

      // If error is authentication related, try refreshing token
      if (error.response?.status === 401 && !silent) {
        const refreshResult = await TokenManager.refreshSession();
        if (refreshResult.success) {
          fetchNotifications();
        }
      }
    } finally {
      if (!silent) setIsLoading(false);
    }
  };

  // Function to mark a notification as read
  const markNotificationAsRead = async (id) => {
    if (!userData?.customer_id || !id) return;

    try {
      const token = TokenManager.getToken();
      const headers = {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
        "Content-Type": "application/json",
      };

      const response = await axios.post(
        `${API_BASE_URL}/notifications/${id}/read`,
        { customer_id: userData.customer_id },
        { headers }
      );

      if (response.data.success) {
        // Update local state to mark as read
        setNotifications((prev) =>
          prev.map((n) => (n.id === id ? { ...n, read: true } : n))
        );

        // Recalculate badge count
        const updatedNotifications = notifications.map((n) =>
          n.id === id ? { ...n, read: true } : n
        );
        const updatedUnreadCount = updatedNotifications.filter(
          (n) => !n.read
        ).length;

        setNotificationBadgeCount(updatedUnreadCount);
        setHasNewNotification(updatedUnreadCount > 0);

        // Return the notification that was marked as read
        return notifications.find((n) => n.id === id);
      }
    } catch (error) {
      console.error("Error marking notification as read:", error);
    }
    return null;
  };

  // Function to view a notification's details and mark it as read
  const viewNotificationDetails = async (id) => {
    try {
      // First mark the notification as read
      const notification = await markNotificationAsRead(id);

      if (notification && notification.order_id) {
        // Close notification center if it's open
        if (isNotificationCenterOpen) {
          setIsNotificationCenterOpen(false);
        }

        // Step 1: Make sure order history modal is open
        if (!isOpenOrders) {
          setIsOpenOrders(true);

          // Need to wait for the modal to be rendered before accessing elements
          setTimeout(() => {
            // Step 2: Once order history modal is open, view the specific order
            const event = new CustomEvent("viewOrder", {
              detail: { orderId: notification.order_id },
            });
            document.dispatchEvent(event);

            // Step 3: Open the order details
            // The viewOrder event handler in Orders will handle this
          }, 300);
        } else {
          // If orders modal is already open, just trigger the view order event
          const event = new CustomEvent("viewOrder", {
            detail: { orderId: notification.order_id },
          });
          document.dispatchEvent(event);
        }
      }

      return notification;
    } catch (error) {
      console.error("Error viewing notification:", error);
      return null;
    }
  };

  // Function to mark all notifications as read
  const clearNotifications = async () => {
    if (!userData?.customer_id) return;

    try {
      const token = TokenManager.getToken();
      const headers = {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
        "Content-Type": "application/json",
      };

      const response = await axios.post(
        `${API_BASE_URL}/notifications/mark-all-read`,
        { customer_id: userData.customer_id },
        { headers }
      );

      if (response.data.success) {
        // Update local state to mark all as read
        setNotifications((prev) => prev.map((n) => ({ ...n, read: true })));
        setNotificationBadgeCount(0);
        setHasNewNotification(false);
      }
    } catch (error) {
      console.error("Error marking all notifications as read:", error);
    }
  };

  // Close dropdown when clicking outside
  useEffect(() => {
    const handleClickOutside = (event) => {
      if (dropdownRef.current && !dropdownRef.current.contains(event.target)) {
        setIsDropdownOpen(false);
      }
    };

    document.addEventListener("mousedown", handleClickOutside);
    return () => {
      document.removeEventListener("mousedown", handleClickOutside);
    };
  }, [dropdownRef]);

  // View order details
  const viewOrderDetails = (orderId) => {
    // Close any other modals
    setStatusModalOpen(false);
    setIsNotificationCenterOpen(false);

    // First, highlight the order in the orders list if that modal is open
    if (isOpenOrders) {
      // Create and dispatch a custom event that the Orders component will listen for
      const event = new CustomEvent("viewOrder", {
        detail: { orderId },
      });
      document.dispatchEvent(event);

      // Let the highlight animation complete before opening details
      setTimeout(() => {
        // Then set the order details to show
        setSelectedOrderId(orderId);
        setIsOpenOrderDetails(true);

        // Fetch order details
        fetchOrderDetails(orderId);
      }, 300);
    } else {
      // If orders modal not open, just open the order details directly
      setSelectedOrderId(orderId);
      setIsOpenOrderDetails(true);

      // Fetch order details
      fetchOrderDetails(orderId);
    }
  };

  // Fetch order details
  const fetchOrderDetails = async (orderId) => {
    if (!orderId) return;

    try {
      const token = TokenManager.getToken();
      const headers = {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      };

      const response = await axios.get(`${API_BASE_URL}/orders/${orderId}`, {
        headers,
      });

      if (response.data) {
        setSelectedOrder(response.data);
      }
    } catch (error) {
      console.error("Failed to fetch order details:", error);
    }
  };

  // Fetch cart items from server
  const fetchCartFromServer = async (silent = false) => {
    if (!userData || !userData.customer_id) return;

    if (!silent)
      console.log("Fetching cart data for user:", userData.customer_id);

    try {
      const token = TokenManager.getToken();
      const headers = {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      };

      const response = await axios.get(
        `${API_BASE_URL}/cart/${userData.customer_id}/count`,
        { headers }
      );

      if (response.data && response.data.success) {
        // Check for cart_count field (the API is returning cart_count not count)
        if (response.data.cart_count !== undefined) {
          // Convert to number since it might be a string
          const countValue = parseInt(response.data.cart_count, 10);
          setCartNumber(isNaN(countValue) ? 0 : countValue);
        }

        // If we got cart items, use them
        if (response.data.cart_items && response.data.cart_items.length > 0) {
          setCartItems(response.data.cart_items);
        }
      } else {
        console.error("Error in cart response:", response.data);
      }
    } catch (error) {
      console.error("Failed to fetch cart:", error);

      // If error is authentication related, try refreshing token
      if (error.response?.status === 401) {
        const refreshResult = await TokenManager.refreshSession();
        if (refreshResult.success) {
          // Retry fetching cart
          fetchCartFromServer();
        }
      }
    }
  };

  // Add to cart
  const addToCart = (dish) => {
    setCartItems((prev) => {
      // Check if dish already exists in cart
      const existingItem = prev.find((item) => item.title === dish.title);

      if (existingItem) {
        // If it exists, increase quantity
        return prev.map((item) =>
          item.title === dish.title
            ? { ...item, quantity: item.quantity + 1 }
            : item
        );
      } else {
        // If it doesn't exist, add with quantity 1
        return [...prev, { ...dish, quantity: 1 }];
      }
    });

    // Increase cart number
    setCartNumber((prev) => prev + 1);
  };

  // Toggle functions
  const toggleDropdown = () => {
    setIsDropdownOpen((prev) => !prev);
  };

  const toggleNavbar = () => {
    setIsOpen((prev) => !prev);
  };

  const toggleModalLogin = () => {
    setIsOpenLogin((prev) => !prev);
  };

  const toggleModalRegister = () => {
    setIsOpenRegister((prev) => !prev);
  };

  const toggleModalCart = () => {
    // If opening cart, fetch latest data
    if (!isOpenCart && isLoggedIn && userData) {
      fetchCartFromServer();
    }
    setIsOpenCart((prev) => !prev);
  };

  const toggleModalCheckout = (state) => {
    if (typeof state === "boolean") {
      setIsOpenCheckout(state);
    } else {
      setIsOpenCheckout((prev) => !prev);
    }

    // If opening checkout, close cart
    if (state === true) {
      setIsOpenCart(false);
    }
  };

  const toggleModalOrders = () => {
    setIsOpenOrders((prev) => !prev);
  };

  const toggleOrderDetails = (value) => {
    if (typeof value === "boolean") {
      setIsOpenOrderDetails(value);

      // If closing, clear the selected order
      if (value === false) {
        setSelectedOrder(null);
        setSelectedOrderId(null);
      }
    } else {
      setIsOpenOrderDetails((prev) => !prev);

      // If closing, clear the selected order
      if (isOpenOrderDetails) {
        setSelectedOrder(null);
        setSelectedOrderId(null);
      }
    }
  };

  const toggleNotificationCenter = () => {
    setIsNotificationCenterOpen((prev) => !prev);

    // Close dropdown when opening notification center
    if (!isNotificationCenterOpen) {
      setIsDropdownOpen(false);
    }

    // If opening notification center and there are unread notifications,
    // mark them as seen (not read yet)
    if (!isNotificationCenterOpen && hasNewNotification) {
      // This would be a good place to visually acknowledge notifications
      // without marking them as read in the database yet
      console.log("Notification center opened with unread notifications");
    }
  };

  const toggleStatusModal = () => {
    setStatusModalOpen((prev) => !prev);
  };

  // Show status update modal
  const showStatusUpdateModal = (data) => {
    setStatusModalData(data);
    setStatusModalOpen(true);
  };

  // Add to cart number
  const addToCartNumber = () => {
    setCartNumber((prev) => prev + 1);
  };

  // Logout function using TokenManager
  const handleLogout = async () => {
    const confirmLogout = window.confirm("Are you sure you want to log out?");
    if (confirmLogout) {
      try {
        // Try to call logout API if available
        if (isLoggedIn && TokenManager.hasToken()) {
          const token = TokenManager.getToken();

          // Make an explicit API call to the logout endpoint
          try {
            await axios.post(
              `${API_BASE_URL}/logout`,
              {}, // Empty body
              {
                headers: {
                  Authorization: `Bearer ${token}`,
                  Accept: "application/json",
                  "Content-Type": "application/json",
                },
              }
            );
            toast.success("You have been logged out.", {
              position: "top-right",
              autoClose: 2000,
              hideProgressBar: false,
              closeOnClick: true,
              pauseOnHover: true,
              draggable: true,
              progress: undefined,
            });
          } catch (error) {
            // Just log errors, but continue with client-side logout
            console.error("Error during server logout:", error);
          }
        }
      } finally {
        // Reset state and clear tokens regardless of API success
        setIsLoggedIn(false);
        setUserData(null);
        setCartItems([]);
        setCartNumber(0);
        setNotifications([]);
        setHasNewNotification(false);
        setNotificationBadgeCount(0);

        // Clear all tokens and storage
        TokenManager.clearToken();
        localStorage.removeItem("isLoggedIn");
        localStorage.removeItem("userData");
        localStorage.removeItem("token");
        localStorage.removeItem("email");

        // Clean up WebSocket connection
        if (window.Echo) {
          window.Echo.disconnect();
        }
      }
    }
  };

  // Login handler for components to call
  const handleLogin = (token, user) => {
    // Store in TokenManager
    TokenManager.setToken(token, user);

    // Update state
    setIsLoggedIn(true);
    setUserData(user);

    // For backwards compatibility
    localStorage.setItem("token", token);
    localStorage.setItem("userData", JSON.stringify(user));
    localStorage.setItem("isLoggedIn", "true");
    localStorage.setItem("email", user.customer_email || "");
  };

  return (
    <NavbarContext.Provider
      value={{
        // State
        isLoggedIn,
        setIsLoggedIn,
        userData,
        setUserData,
        cartNumber,
        setCartNumber,
        cartItems,
        setCartItems,
        isDropdownOpen,
        dropdownRef,
        isOpen,
        hasNewNotification,
        setHasNewNotification,
        notificationBadgeCount,
        setNotificationBadgeCount,
        isNotificationCenterOpen,
        notifications,
        setNotifications,
        isOpenLogin,
        isOpenRegister,
        isOpenCart,
        isOpenEditProfile,
        setIsOpenEditProfile,
        isOpenOrders,
        isOpenCheckout,
        isOpenOrderDetails,
        selectedOrderId,
        selectedOrder,
        statusModalOpen,
        statusModalData,
        isInitializing,
        isLoading,

        // Functions
        toggleDropdown,
        toggleNavbar,
        toggleModalLogin,
        toggleModalRegister,
        toggleModalCart,
        toggleModalCheckout,
        toggleModalOrders,
        toggleOrderDetails,
        toggleNotificationCenter,
        toggleStatusModal,
        fetchCartFromServer,
        fetchNotifications,
        fetchOrderDetails,
        viewOrderDetails,
        clearNotifications,
        showStatusUpdateModal,
        handleLogout,
        handleLogin,
        addToCart,
        addToCartNumber,
        viewNotificationDetails,
        markNotificationAsRead,
        getNotificationType,
        getStatusMessage,
      }}
    >
      {children}
    </NavbarContext.Provider>
  );
};

export const useNavbar = () => useContext(NavbarContext);
