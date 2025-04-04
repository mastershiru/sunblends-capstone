import React, { useState, useEffect } from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import {
  faChevronLeft,
  faXmark,
  faClock,
} from "@fortawesome/free-solid-svg-icons";
import { toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import "../../assets/css/modal.css";
import axios from "axios";

const Checkout = ({
  isOpenCheckout,
  setIsOpenCheckout,
  cartItems,
  cartTotal,
  toggleModalCart,
  handleCheckoutComplete,
}) => {
  const [paymentMethod, setPaymentMethod] = useState("");
  const [deliveryMethod, setDeliveryMethod] = useState("");
  const [deliveryFee, setDeliveryFee] = useState(0);
  const [notes, setNotes] = useState("");
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState(null);
  const [userData, setUserData] = useState(null);
  const [cashAmount, setCashAmount] = useState("");
  const [isWithinOperatingHours, setIsWithinOperatingHours] = useState(true);

  // Check if current time is within operating hours
  useEffect(() => {
    const checkOperatingHours = () => {
      // Get fresh time data each check, don't rely on cached Date objects
      const now = new Date();

      // Force Date to get current time from system by recreating it
      now.setTime(Date.now());

      const hour = now.getHours();
      const minutes = now.getMinutes();

      // Operating hours: 8:00 AM (8:00) to 5:00 PM (17:00)
      // Convert to minutes for easier comparison
      const currentTimeInMinutes = hour * 60 + minutes;
      const openingTimeInMinutes = 5 * 60; // 5:00 AM
      const closingTimeInMinutes = 17 * 60; // 5:00 PM

      // Within operating hours if current time is >= opening and < closing
      const isOpen =
        currentTimeInMinutes >= openingTimeInMinutes &&
        currentTimeInMinutes < closingTimeInMinutes;

      // Update state with current status
      setIsWithinOperatingHours(isOpen);

      // Log for debugging with timestamp to verify it's using current time
      console.log(
        `Check time: ${now.toLocaleTimeString()}, Hours: ${hour}, Minutes: ${minutes}, Status: ${
          isOpen ? "Open" : "Closed"
        }`
      );
    };

    // Check immediately when component mounts
    checkOperatingHours();

    // Check again whenever the modal is opened
    if (isOpenCheckout) {
      checkOperatingHours();
    }

    // Set up interval to check more frequently (every 15 seconds)
    // This helps catch system time changes more quickly
    const intervalId = setInterval(checkOperatingHours, 15000);

    // Clean up interval on unmount
    return () => clearInterval(intervalId);
  }, [isOpenCheckout]);

  // Fetch user data when component mounts
  useEffect(() => {
    const fetchUserData = async () => {
      const email = localStorage.getItem("email");
      if (!email) return;

      try {
        const response = await axios.post(
          "http://127.0.0.1:8000/api/getUserData",
          { email },
          { withCredentials: true }
        );
        if (response.data.success) {
          setUserData(response.data);
        }
      } catch (error) {
        console.error("Error fetching user data:", error);
      }
    };

    fetchUserData();
  }, [isOpenCheckout]); // Refresh when checkout is opened

  const handleDeliveryMethodChange = (value) => {
    setDeliveryMethod(value);
    // Set delivery fee based on method
    if (value === "delivery") {
      setDeliveryFee(40); // ₱40 delivery fee
    } else {
      setDeliveryFee(0); // No fee for pickup
    }
  };

  const totalAmount = cartTotal + deliveryFee;

  const validateForm = () => {
    setError(null);

    if (!isWithinOperatingHours) {
      setError(
        "Sorry, we're closed. Our operating hours are 8:00 AM to 5:00 PM."
      );
      return false;
    }

    if (!paymentMethod) {
      setError("Please select a payment method");
      return false;
    }

    if (!deliveryMethod) {
      setError("Please select a delivery method");
      return false;
    }

    if (deliveryMethod === "delivery" && !notes.trim()) {
      setError("Please enter a delivery address");
      return false;
    }

    if (
      paymentMethod === "Cash" &&
      (!cashAmount || parseFloat(cashAmount) < totalAmount)
    ) {
      setError("Cash amount must be equal to or greater than the total amount");
      return false;
    }

    return true;
  };

  const handleCheckout = async () => {
    if (!validateForm()) return;

    const email = localStorage.getItem("email");
    if (!email) {
      setError("You must be logged in to place an order");
      return;
    }

    setIsLoading(true);
    setError(null);

    // Calculate change amount if paying with cash
    const changeAmount =
      paymentMethod === "Cash" && cashAmount
        ? parseFloat(cashAmount) - totalAmount
        : 0;

    try {
      // First make sure we have a CSRF token
      await axios.get("http://127.0.0.1:8000/sanctum/csrf-cookie", {
        withCredentials: true,
      });

      // Place the order through our API
      const response = await axios.post(
        "http://127.0.0.1:8000/api/checkout",
        {
          email,
          paymentMethod,
          deliveryMethod,
          totalAmount,
          notes: deliveryMethod === "delivery" ? notes : null,
          cashAmount: paymentMethod === "Cash" ? parseFloat(cashAmount) : 0,
          changeAmount: changeAmount, // Add the calculated change amount
        },
        {
          withCredentials: true,
          headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
          },
        }
      );

      if (response.data.success) {
        // Order successful
        toast.success("Order placed successfully!", {
          position: "top-right",
          autoClose: 3000,
          hideProgressBar: false,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true,
          progress: undefined,
        });
        setIsOpenCheckout(false);
        setNotes("");
        setPaymentMethod("");
        setDeliveryMethod("");
        setDeliveryFee(0);
        setCashAmount("");
        handleCheckoutComplete(); // Clear cart in parent component
      } else {
        setError(response.data.message || "Failed to place order");
      }
    } catch (error) {
      console.error("Error placing order:", error);
      setError(
        error.response?.data?.message || "An error occurred during checkout"
      );
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <>
      {isOpenCheckout && (
        <div className="center" id="center">
          <div className="popup">
            <div className="back-btn" id="back-btn">
              <FontAwesomeIcon
                icon={faChevronLeft}
                onClick={() => {
                  toggleModalCart(true);
                  setIsOpenCheckout(false);
                }}
              />
            </div>
            <div
              className="close-btn"
              id="close-btn"
              onClick={() => setIsOpenCheckout(false)}
            >
              <FontAwesomeIcon icon={faXmark} />
            </div>

            <div className="checkout" id="checkout-section">
              <h2>Checkout</h2>

              {/* Operating Hours Banner */}
              {!isWithinOperatingHours && (
                <div
                  className="operating-hours-warning"
                  style={{
                    background: "#fff3cd",
                    color: "#856404",
                    padding: "10px",
                    borderRadius: "5px",
                    marginBottom: "15px",
                    display: "flex",
                    alignItems: "center",
                    justifyContent: "center",
                    fontSize: "14px",
                  }}
                >
                  <FontAwesomeIcon
                    icon={faClock}
                    style={{ marginRight: "8px" }}
                  />
                  <span>
                    <strong>We're currently closed.</strong> Our operating hours
                    are 8:00 AM to 5:00 PM.
                  </span>
                </div>
              )}

              {error && (
                <div className="alert alert-danger" role="alert">
                  {error}
                </div>
              )}

              <div className="checkout-items">
                {cartItems.length > 0 ? (
                  cartItems.map((item, index) => (
                    <div key={index} className="checkout-item-content">
                      <div className="item-info-container">
                        <img
                          src={
                            item.img.startsWith("http")
                              ? item.img
                              : `http://127.0.0.1:8000/${item.img}`
                          }
                          alt={item.title}
                          className="checkout-item-img"
                          style={{ width: "50px", height: "50px" }}
                          onError={(e) => {
                            e.target.onerror = null;
                            e.target.src =
                              "http://127.0.0.1:8000/images/placeholder-food.jpg";
                          }}
                        />
                        <div>
                          <p>{item.title}</p>
                          <p style={{ textAlign: "left" }}>₱{item.price}</p>
                        </div>
                      </div>
                      <div className="cart-btn-container">
                        <span
                          className="quantity"
                          style={{ marginRight: "15px" }}
                        >
                          {item.quantity}
                        </span>
                      </div>
                    </div>
                  ))
                ) : (
                  <p>Your cart is empty</p>
                )}
              </div>
              <hr />

              <div className="container">
                {/* Payment Method Section */}
                <div className="row">
                  <div className="col">
                    <p style={{ fontSize: "12px", margin: "0" }}>
                      Modes of Payment:
                    </p>
                    <div className="payment-methods">
                      <label>
                        <input
                          type="radio"
                          name="payment-method"
                          value="Cash"
                          checked={paymentMethod === "Cash"}
                          onChange={() => setPaymentMethod("Cash")}
                        />{" "}
                        Cash&nbsp;&nbsp;&nbsp;
                      </label>
                      <label>
                        <input
                          type="radio"
                          name="payment-method"
                          value="GCash"
                          checked={paymentMethod === "GCash"}
                          onChange={() => setPaymentMethod("GCash")}
                        />{" "}
                        GCash
                      </label>
                    </div>

                    {/* Cash Amount Input Field */}
                    {paymentMethod === "Cash" && (
                      <div
                        className="cash-amount-input"
                        style={{ marginTop: "10px" }}
                      >
                        <label
                          htmlFor="cash-amount"
                          style={{ fontSize: "12px", display: "block" }}
                        >
                          Cash Amount:
                        </label>
                        <input
                          id="cash-amount"
                          type="number"
                          className="notes"
                          placeholder="Enter cash amount"
                          value={cashAmount}
                          onChange={(e) => setCashAmount(e.target.value)}
                          min={totalAmount}
                          required
                          style={{
                            width: "100%",
                            padding: "8px",
                            marginTop: "4px",
                            fontSize: "14px",
                          }}
                        />
                      </div>
                    )}
                  </div>

                  {/* Fee Summary */}
                  <div className="col" style={{ marginTop: "10px" }}>
                    <div className="sub-total">
                      <label>
                        Subtotal: <span>₱{cartTotal.toFixed(2)}</span>
                      </label>
                      <label>
                        Delivery fee: <span>₱{deliveryFee.toFixed(2)}</span>
                      </label>
                    </div>
                    <hr style={{ margin: "0" }} />
                    <div className="total-payment">
                      <p style={{ margin: "0" }}>
                        <b>Total:</b> <span>₱{totalAmount.toFixed(2)}</span>
                      </p>

                      {/* Display Change Amount */}
                      {paymentMethod === "Cash" &&
                        cashAmount &&
                        parseFloat(cashAmount) >= totalAmount && (
                          <p
                            style={{
                              margin: "5px 0 0 0",
                              fontSize: "14px",
                              color: "green",
                            }}
                          >
                            Change: ₱
                            {(parseFloat(cashAmount) - totalAmount).toFixed(2)}
                          </p>
                        )}
                    </div>
                  </div>
                </div>

                {/* Delivery Method Section */}
                <div className="row">
                  <div className="col">
                    <p style={{ fontSize: "12px", margin: "0" }}>
                      Delivery Method:
                    </p>
                    <div className="delivery-methods">
                      <label>
                        <input
                          type="radio"
                          name="delivery-method"
                          value="pickup"
                          checked={deliveryMethod === "pickup"}
                          onChange={() => handleDeliveryMethodChange("pickup")}
                        />{" "}
                        Pickup&nbsp;&nbsp;&nbsp;
                      </label>
                      <label>
                        <input
                          type="radio"
                          name="delivery-method"
                          value="delivery"
                          checked={deliveryMethod === "delivery"}
                          onChange={() =>
                            handleDeliveryMethodChange("delivery")
                          }
                        />{" "}
                        Delivery
                      </label>
                    </div>
                  </div>

                  <div className="col">
                    <button
                      className="checkout-button"
                      onClick={handleCheckout}
                      disabled={isLoading || !isWithinOperatingHours}
                      style={{
                        opacity: !isWithinOperatingHours ? 0.5 : 1,
                        cursor: !isWithinOperatingHours
                          ? "not-allowed"
                          : "pointer",
                        backgroundColor: !isWithinOperatingHours
                          ? "#cccccc"
                          : "#ff8243",
                      }}
                    >
                      {isLoading ? "Processing..." : "Place Order"}
                    </button>

                    {/* Operating hours hint text */}
                    {!isWithinOperatingHours && (
                      <p
                        style={{
                          fontSize: "12px",
                          color: "#842029", // Darker red text
                          textAlign: "center",
                          marginTop: "5px",
                          fontWeight: "500",
                        }}
                      >
                        Orders can be placed between 8:00 AM and 5:00 PM
                      </p>
                    )}
                  </div>
                </div>

                {deliveryMethod === "delivery" && (
                  <input
                    className="notes"
                    type="text"
                    placeholder="Enter delivery address: Department/Floor/Room No."
                    style={{ marginTop: "10px" }}
                    value={notes}
                    onChange={(e) => setNotes(e.target.value)}
                    required
                  />
                )}

                {deliveryMethod === "pickup" && (
                  <input
                    className="notes"
                    type="time"
                    placeholder="Select pickup time"
                    style={{ marginTop: "10px" }}
                    value={notes}
                    onChange={(e) => setNotes(e.target.value)}
                    required
                  />
                )}
              </div>
            </div>
          </div>
        </div>
      )}
    </>
  );
};

export default Checkout;
