import React, { useState, useEffect } from "react";
import Login from "./login";
import Register from "./register";
import Cart from "./cart";
import EditProfile from "./edit-profile";
import Orders from "./history-orders";
import NotificationCenter from "./notification-center";
import Forgotpassword from "./forgot.password";
import Checkout from "./checkout";
import { useNavbar } from "../../context/NavbarContext";

function Modals() {
  // Get all necessary state and functions from NavbarContext
  const {
    isLoggedIn,
    setIsLoggedIn,
    userData,
    setUserData,
    cartItems,
    setCartItems,
    cartNumber,
    setCartNumber,
    
    // Modal state
    isOpenLogin,
    isOpenRegister,
    isOpenCart,
    isOpenEditProfile,
    isOpenOrders,
    isNotificationCenterOpen,
    
    // Toggle functions
    toggleModalLogin,
    toggleModalRegister,
    toggleModalCart,
    toggleModalOrders,
    toggleNotificationCenter,
    setIsOpenEditProfile,
    
    // Notifications
    notifications,
    clearNotifications,
    viewOrderDetails
  } = useNavbar();
  
  // Local state for modals that aren't in the NavbarContext
  const [isOpenForgotpassword, setIsOpenForgotpassword] = useState(false);
  const [isOpenCheckout, setIsOpenCheckout] = useState(false);

  // Form state
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");

  // Toggle checkout modal
  const toggleModalCheckout = () => {
    setIsOpenCheckout(prev => !prev);
    
    // If opening checkout, close cart
    if (!isOpenCheckout) {
      toggleModalCart();
    }
  };

  return (
    <>
      {/* Login Modal */}
      <Login
        isOpenLogin={isOpenLogin}
        toggleModalLogin={toggleModalLogin}
        setIsLoggedIn={setIsLoggedIn}
        setUserData={setUserData}
        email={email}
        setEmail={setEmail}
        password={password}
        setPassword={setPassword}
        toggleModalRegister={toggleModalRegister}
      />
      
      {/* Register Modal */}
      <Register
        isOpenRegister={isOpenRegister}
        toggleModalRegister={toggleModalRegister}
        toggleModalLogin={toggleModalLogin}
      />
      
      {/* Cart Modal */}
      <Cart
        isOpenCart={isOpenCart}
        toggleModalCart={toggleModalCart}
        isLoggedIn={isLoggedIn}
        userData={userData}
        cartItems={cartItems}
        setCartItems={setCartItems}
        setCartNumber={setCartNumber}
        toggleModalLogin={toggleModalLogin}
        toggleModalCheckout={toggleModalCheckout}
      />
      
      {/* Checkout Modal */}
      <Checkout
        isOpenCheckout={isOpenCheckout}
        toggleModalCheckout={toggleModalCheckout}
        cartItems={cartItems}
        setCartItems={setCartItems}
        setCartNumber={setCartNumber}
        userData={userData}
        isLoggedIn={isLoggedIn}
      />
      
      {/* Edit Profile Modal */}
      <EditProfile
        isOpenEditProfile={isOpenEditProfile}
        setIsOpenEditProfile={setIsOpenEditProfile}
        userData={userData}
        setUserData={setUserData}
      />
      
      {/* Order History Modal */}
      <Orders
        isOpenOrders={isOpenOrders}
        toggleModalOrders={toggleModalOrders}
        userData={userData}
      />

      {/* Notification Center */}
      <NotificationCenter
        isOpen={isNotificationCenterOpen}
        onClose={toggleNotificationCenter}
        notifications={notifications}
        onMarkAllAsRead={clearNotifications}
        onViewDetails={viewOrderDetails}
      />

      {/* Forgot Password Modal */}
      <Forgotpassword
        isOpenForgotpassword={isOpenForgotpassword}
        setIsOpenForgotpassword={setIsOpenForgotpassword}
        toggleModalLogin={toggleModalLogin}
      />
    </>
  );
}

export default Modals;