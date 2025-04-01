// src/components/Modals.jsx
import React from 'react';
import { useNavbar } from '../context/NavbarContext';

// Import all modal components
import Login from './modal-compinents/login';
import Register from './modal-compinents/register';
import Cart from './modal-compinents/cart';
import Checkout from './modal-compinents/checkout';
import EditProfile from './modal-compinents/edit-profile';
import ForgotPassword from './modal-compinents/forgot.password';
import HistoryOrders from './modal-compinents/history-orders';
import InsertItem from './modal-compinents/insert-item';
import NotificationCenter from './modal-compinents/notification-center';
import NotificationModal from './modal-compinents/notification-modal';
import OrderDetails from './modal-compinents/order-details';

const Modals = () => {
  // Get modal states and toggle functions from NavbarContext
  const {
    isLoggedIn,
    setIsLoggedIn,
    userData,
    setUserData,
    setCartNumber,
    cartItems,
    setCartItems,
    
    // Login/Register modal states
    isOpenLogin,
    toggleModalLogin,
    isOpenRegister,
    toggleModalRegister,
    isOpenForgotPassword,
    toggleForgotPassword,
    
    // Cart modal states
    isOpenCart,
    toggleModalCart,
    isOpenCheckout,
    toggleModalCheckout,
    isOpenInsertItem,
    toggleModalInsertItem,
    
    // Profile modal states
    isOpenEditProfile,
    setIsOpenEditProfile,
    
    // Orders modal states
    isOpenOrders,
    toggleModalOrders,
    isOpenOrderDetails,
    toggleModalOrderDetails,
    selectedOrderId,
    
    // Notification modal states
    isNotificationCenterOpen,
    toggleNotificationCenter,
    isNotificationModalOpen,
    toggleNotificationModal,
    selectedNotification,
    
    // Form states for modals
    email,
    setEmail,
    password,
    setPassword,
  } = useNavbar();

  return (
    <>
      {/* Authentication Modals */}
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
        toggleForgotPassword={toggleForgotPassword}
      />
      
      <Register
        isOpenRegister={isOpenRegister}
        toggleModalRegister={toggleModalRegister}
        toggleModalLogin={toggleModalLogin}
      />
      
      <ForgotPassword
        isOpenForgotpassword={isOpenForgotPassword}
        setIsOpenForgotpassword={toggleForgotPassword}
      />
      
      {/* Cart Related Modals */}
      <Cart
        isOpenCart={isOpenCart}
        toggleModalCart={toggleModalCart}
        cartItems={cartItems}
        setCartItems={setCartItems}
        setCartNumber={setCartNumber}
        isLoggedIn={isLoggedIn}
        userData={userData}
        toggleModalLogin={toggleModalLogin}
        toggleModalCheckout={toggleModalCheckout}
        toggleModalInsertItem={toggleModalInsertItem}
      />
      
      <Checkout
        isOpenCheckout={isOpenCheckout}
        toggleModalCheckout={toggleModalCheckout}
        cartItems={cartItems}
        userData={userData}
        setCartItems={setCartItems}
        setCartNumber={setCartNumber}
      />
      
      <InsertItem
        isOpenInsertItem={isOpenInsertItem}
        toggleModalInsertItem={toggleModalInsertItem}
        cartItems={cartItems}
        setCartItems={setCartItems}
        setCartNumber={setCartNumber}
      />
      
      {/* Profile Modal */}
      <EditProfile
        isOpenEditProfile={isOpenEditProfile}
        setIsOpenEditProfile={setIsOpenEditProfile}
        userData={userData}
        setUserData={setUserData}
      />
      
      {/* Order Related Modals */}
      <HistoryOrders
        isOpenOrders={isOpenOrders}
        toggleModalOrders={toggleModalOrders}
        toggleModalOrderDetails={toggleModalOrderDetails}
        userData={userData}
      />
      
      <OrderDetails
        isOpenOrderDetails={isOpenOrderDetails}
        toggleModalOrderDetails={toggleModalOrderDetails}
        orderId={selectedOrderId}
        userData={userData}
      />
      
      {/* Notification Modals */}
      <NotificationCenter
        isOpen={isNotificationCenterOpen}
        onClose={toggleNotificationCenter}
        userData={userData}
        toggleNotificationModal={toggleNotificationModal}
        toggleModalOrderDetails={toggleModalOrderDetails}
      />
      
      <NotificationModal
        isOpen={isNotificationModalOpen}
        onClose={toggleNotificationModal}
        notification={selectedNotification}
        onViewOrder={toggleModalOrderDetails}
      />
    </>
  );
};

export default Modals;