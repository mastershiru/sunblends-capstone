import React, { useState, useEffect, useRef } from "react";
import axios from "axios";
import Login from "./modal-compinents/login";
import Register from "./modal-compinents/register";
import Cart from "./modal-compinents/cart";
import Orders from "./modal-compinents/history-orders";
import EditProfile from "./modal-compinents/edit-profile";
import Navbar from "./Navbar";
import { useLocation } from "react-router-dom";
import imagebg from "../assets/images/menu-bg.png";
import Pusher from "pusher-js";
import Echo from "laravel-echo";
import NotificationModal from "./modal-compinents/notification-modal";
import NotificationsCenter from "./modal-compinents/notification-center";
import NotificationManager, {
  showNotification,
} from "./notifications/Notification-manager";

const BookingTable = () => {
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const [isDropdownOpen, setIsDropdownOpen] = useState(false);
  const dropdownRef = useRef(null);
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [userData, setUserData] = useState(null);

  const [statusModalOpen, setStatusModalOpen] = useState(false);
  const [statusModalData, setStatusModalData] = useState(null);
  const [notifications, setNotifications] = useState([]);
  const [isLoading, setIsLoading] = useState(false);
  const [hasNewNotification, setHasNewNotification] = useState(false);
  const [notificationBadgeCount, setNotificationBadgeCount] = useState(0);
  const [isNotificationCenterOpen, setIsNotificationCenterOpen] =
    useState(false);

  // API URL
  const API_BASE_URL = "http://172.16.8.48:8000/api";

  // navigate to other screen like home to All menu, all menu to home----------------------------------------------------------------------------
  const location = useLocation();
  useEffect(() => {
    if (location.hash) {
      const sectionId = location.hash.substring(1);
      const section = document.getElementById(sectionId);
      if (section) {
        section.scrollIntoView({ behavior: "smooth" });
      }
    }
  }, [location]);

  //navbar menu
  const [isOpen, setIsOpen] = useState(false);

  const toggleNavbar = () => {
    setIsOpen((prevState) => !prevState);
  };

  const toggleNotificationCenter = () => {
    setIsNotificationCenterOpen(!isNotificationCenterOpen);
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

  // Fetch notifications when user logs in
  useEffect(() => {
    if (isLoggedIn && userData?.customer_id) {
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
  }, [isLoggedIn, userData]);

  // Function to fetch notifications from the server
  const fetchNotifications = async (silent = false) => {
    if (!userData?.customer_id) return;

    if (!silent) setIsLoading(true);

    try {
      const response = await axios.get(
        `${API_BASE_URL}/notifications?customer_id=${userData.customer_id}`,
        {
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
          },
        }
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
      console.error("Error fetching notifications:", error);
    } finally {
      if (!silent) setIsLoading(false);
    }
  };

  // Function to mark a notification as read
  const markNotificationAsRead = async (id) => {
    if (!userData?.customer_id || !id) return;

    try {
      const response = await axios.post(
        `${API_BASE_URL}/notifications/${id}/read`,
        { customer_id: userData.customer_id },
        {
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
          },
        }
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
    const notification = await markNotificationAsRead(id);
    if (notification && notification.order_id) {
      viewOrderDetails(notification.order_id);
    }
  };

  // Function to mark all notifications as read
  const clearNotifications = async () => {
    if (!userData?.customer_id) return;

    try {
      const response = await axios.post(
        `${API_BASE_URL}/notifications/mark-all-read`,
        { customer_id: userData.customer_id },
        {
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
          },
        }
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

  // Initialize Echo once component mounts
  useEffect(() => {
    // Clean up existing connections
    if (window.Echo) {
      try {
        window.Echo.disconnect();
      } catch (e) {
        console.error("Error disconnecting Echo:", e);
      }
    }

    try {
      // First initialize Pusher
      window.Pusher = Pusher;

      // Check Pusher is available
      if (!window.Pusher) {
        throw new Error("Pusher not available");
      }

      // Get token for authenticated channels (if available)
      const token = localStorage.getItem("token");

      // Initialize Echo
      window.Echo = new Echo({
        broadcaster: "pusher",
        key: "sunblends-key",
        wsHost: "localhost",
        wsPort: 8080,
        disableStats: true,
        forceTLS: false,
        encrypted: false,
        enabledTransports: ["ws", "wss"],
        auth: token
          ? {
              headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
              },
            }
          : undefined,
      });

      console.log("Echo initialized successfully");

      // Set up channel listeners
      const publicChannel = window.Echo.channel("orders");

      publicChannel.listen(".OrderStatusChanged", (data) => {
        console.log("Received OrderStatusChanged event:", data);
        handleOrderStatusUpdate(data);
      });

      // Also listen without the dot prefix as a fallback
      publicChannel.listen("OrderStatusChanged", (data) => {
        console.log("Received OrderStatusChanged event (no dot):", data);
        handleOrderStatusUpdate(data);
      });

      // If logged in, listen on private channel
      if (isLoggedIn && userData && userData.customer_id) {
        try {
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
        } catch (error) {
          console.error("Error setting up private channel:", error);
        }
      }
    } catch (error) {
      console.error("Error setting up Echo:", error);
    }

    // Cleanup function
    return () => {
      if (window.Echo) {
        console.log("Cleaning up Echo connections");
        try {
          window.Echo.disconnect();
        } catch (e) {
          console.error("Error during cleanup:", e);
        }
      }
    };
  }, [isLoggedIn, userData]);

  // Handle order status updates from public channel
  const handleOrderStatusUpdate = (data) => {
    console.log("Processing order status update:", data);

    // Extract notification data
    const notificationData = data.orderData || data;

    // Only process if this notification is for the current user
    if (
      isLoggedIn &&
      userData &&
      notificationData.customer_id === userData.customer_id
    ) {
      console.log("Order update is for current user");

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

      // Show modal for important updates
      if (isImportantStatus(notificationData.status)) {
        showNotificationModal(notificationData);
      }
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

    // Show modal for important notifications
    showNotificationModal(notificationData);
  };

  // Show notification modal
  const showNotificationModal = (notificationData) => {
    setStatusModalData({
      order_id: notificationData.order_id,
      status: notificationData.status,
      message: notificationData.description || notificationData.message,
      total_price: notificationData.total_price,
    });

    setStatusModalOpen(true);
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
    return ["completed", "cancelled", "ready", "processing"].includes(status);
  };

  // Get appropriate status messages
  const getStatusMessage = (status) => {
    switch (status) {
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

  // View order details from notification
  const viewOrderDetails = (orderId) => {
    // Close any open modals
    setStatusModalOpen(false);
    setIsNotificationCenterOpen(false);

    // Open orders modal
    toggleModalOrders();

    // Notify Orders component to highlight the specific order
    setTimeout(() => {
      const event = new CustomEvent("viewOrder", { detail: { orderId } });
      document.dispatchEvent(event);
    }, 300); // Small delay to ensure Orders component is mounted
  };

  // Check if user is logged in on page load
  useEffect(() => {
    const storedUserData = localStorage.getItem("userData");
    const storedIsLoggedIn = localStorage.getItem("isLoggedIn");

    if (storedIsLoggedIn === "true" && storedUserData) {
      try {
        const parsed = JSON.parse(storedUserData);
        setIsLoggedIn(true);
        setUserData(parsed);
      } catch (e) {
        console.error("Error parsing stored user data:", e);
        localStorage.removeItem("userData");
        localStorage.removeItem("isLoggedIn");
      }
    }
  }, []);

  // Save user data to localStorage when logged in
  useEffect(() => {
    if (isLoggedIn && userData) {
      localStorage.setItem("isLoggedIn", "true");
      localStorage.setItem("userData", JSON.stringify(userData));
    } else if (!isLoggedIn) {
      localStorage.removeItem("isLoggedIn");
      localStorage.removeItem("userData");
    }
  }, [isLoggedIn, userData]);

  // Toggle dropdown menu
  const toggleDropdown = () => {
    setIsDropdownOpen((prev) => !prev);
  };

  // Close dropdown when clicking outside
  const handleClickOutside = (event) => {
    if (dropdownRef.current && !dropdownRef.current.contains(event.target)) {
      setIsDropdownOpen(false);
    }
  };

  useEffect(() => {
    document.addEventListener("mousedown", handleClickOutside);
    return () => {
      document.removeEventListener("mousedown", handleClickOutside);
    };
  }, []);

  // Login modal
  const [isOpenLogin, setIsOpenLogin] = useState(false);
  const toggleModalLogin = () => {
    setIsOpenLogin(!isOpenLogin);
  };

  // Register modal
  const [isOpenRegister, setIsOpenRegister] = useState(false);
  const toggleModalRegister = () => {
    setIsOpenRegister(!isOpenRegister);
  };

  // Edit profile modal
  const [isOpenEditProfile, setIsOpenEditProfile] = useState(false);

  // Cart functionality
  const [cartNumber, setCartNumber] = useState(0);
  const [isOpenCart, setIsOpenCart] = useState(false);
  const toggleModalCart = () => {
    if (cartNumber === -1) {
      alert("Your cart is empty.");
    } else {
      setIsOpenCart(!isOpenCart);
    }
  };

  const [cartItems, setCartItems] = useState([]);

  // Checkout modal
  const [isOpenCheckout, setIsOpenCheckout] = useState(false);
  const toggleModalCheckout = (state) => setIsOpenCheckout(state);

  // Orders history modal
  const [isOpenOrders, setIsOpenOrders] = useState(false);
  const toggleModalOrders = () => {
    setIsOpenOrders(!isOpenOrders);
  };

  //logout
  const handleLogout = () => {
    const confirmLogout = window.confirm("Are you sure you want to log out?");
    if (confirmLogout) {
      setIsLoggedIn(false);
      setEmail("");
      setPassword("");
      setUserData(null);
      setCartItems([]); // Clear cart items
      localStorage.removeItem("isLoggedIn");
      localStorage.removeItem("userData");
      alert("You have been logged out.");
      toggleModalLogin(true);
      setCartNumber(0);
    }
  };

  // Add this effect to fetch cart data when user logs in
  useEffect(() => {
    const fetchCartData = async () => {
      if (isLoggedIn && userData) {
        try {
          const response = await fetch(
            `http://172.16.8.48:8000/api/cart/${userData.customer_id}/count`
          );

          const data = await response.json();
          // console.log("Cart API Response:", data); // Debugging step

          if (data.success) {
            setCartNumber(Number(data.cart_count)); // Ensure correct update
          } else {
            console.error("Cart API error:", data);
            setCartNumber(0);
          }
        } catch (error) {
          console.error("Error fetching cart:", error);
          setCartNumber(0);
        }
      } else {
        setCartNumber(0);
      }
    };

    fetchCartData();
  }, [isLoggedIn, userData]);
  // RESERVATION START    --------------------------------------------------------------------------------------------------------------------
  const [person, setPerson] = useState("1 Person");
  const [date, setDate] = useState("");
  const [time, setTime] = useState("");

  useEffect(() => {
    if (window.location.hash) {
      const sectionId = window.location.hash.substring(1); // Remove the "#" symbol
      const section = document.getElementById(sectionId);
      if (section) {
        section.scrollIntoView({ behavior: "smooth" });
      }
    }
  }, []);

  return (
    <>
      <NotificationManager />

      <NotificationModal
        isOpen={statusModalOpen}
        onClose={() => setStatusModalOpen(false)}
        data={statusModalData}
        onViewOrder={viewOrderDetails}
      />

      {/* Notifications center modal */}
      <NotificationsCenter
        isOpen={isNotificationCenterOpen}
        onClose={() => setIsNotificationCenterOpen(false)}
        notifications={notifications}
        onMarkAllAsRead={clearNotifications}
        onViewDetails={(orderId) => {
          setIsNotificationCenterOpen(false);
          viewOrderDetails(orderId);
        }}
      />
      <Navbar
        isLoggedIn={isLoggedIn}
        userData={userData}
        cartNumber={cartNumber}
        isDropdownOpen={isDropdownOpen}
        dropdownRef={dropdownRef}
        isOpen={isOpen}
        toggleDropdown={toggleDropdown}
        toggleModalCart={toggleModalCart}
        toggleModalLogin={toggleModalLogin}
        toggleModalRegister={toggleModalRegister}
        toggleModalOrders={toggleModalOrders}
        setIsOpenEditProfile={setIsOpenEditProfile}
        handleLogout={handleLogout}
        toggleNavbar={toggleNavbar}
        toggleNotificationCenter={toggleNotificationCenter}
        notificationBadgeCount={notificationBadgeCount}
        hasNewNotification={hasNewNotification}
        setIsNotificationCenterOpen={setIsNotificationCenterOpen}
      />
      <Login
        isOpenLogin={isOpenLogin}
        toggleModalLogin={toggleModalLogin}
        setIsLoggedIn={setIsLoggedIn}
        email={email}
        setEmail={setEmail}
        password={password}
        setPassword={setPassword}
        setUserData={setUserData}
        toggleModalRegister={toggleModalRegister}
      />
      <EditProfile
        isOpenEditProfile={isOpenEditProfile}
        setIsOpenEditProfile={setIsOpenEditProfile}
        userData={userData}
        setUserData={setUserData}
      />
      <Register
        isOpenRegister={isOpenRegister}
        toggleModalRegister={toggleModalRegister}
        toggleModalLogin={toggleModalLogin}
      />

      <Cart
        isOpenCart={isOpenCart}
        toggleModalCart={toggleModalCart}
        cartItems={cartItems}
        setCartItems={setCartItems}
        setCartNumber={setCartNumber}
        isLoggedIn={isLoggedIn}
        toggleModalLogin={toggleModalLogin}
        toggleModalCheckout={toggleModalCheckout}
        isOpenCheckout={isOpenCheckout}
      />
      <Orders
        isOpenOrders={isOpenOrders}
        toggleModalOrders={toggleModalOrders}
      />
      <section id="reservation" style={{ backgroundImage: `url(${imagebg})` }}>
        <div className="container container-reservation">
          <div className="booking-box">
            <h2 className="booking-title">BOOKING TABLE</h2>
            <p className="booking-subtitle">
              Please fill out the form below to make a reservation.
            </p>
            <div className="form-group">
              <label>Person</label>
              <select
                value={person}
                onChange={(e) => setPerson(e.target.value)}
              >
                <option>1 Person</option>
                <option>2 People</option>
                <option>3 People</option>
                <option>4 People</option>
              </select>
            </div>
            <div className="form-group">
              <label>Date</label>
              <input
                type="date"
                value={date}
                onChange={(e) => setDate(e.target.value)}
              />
            </div>
            <div className="form-group">
              <label>Time</label>
              <input
                type="time"
                value={time}
                onChange={(e) => setTime(e.target.value)}
              />
            </div>
            <button className="booking-button">BOOKING TABLE</button>
          </div>
        </div>
      </section>
    </>
  );
};

export default BookingTable;
