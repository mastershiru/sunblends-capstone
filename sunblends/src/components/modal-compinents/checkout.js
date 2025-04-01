import React, { useState, useEffect } from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faChevronLeft, faXmark } from "@fortawesome/free-solid-svg-icons";
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
        alert("Order placed successfully!");
        setIsOpenCheckout(false);
        setNotes("");
        setPaymentMethod("");
        setDeliveryMethod("");
        setDeliveryFee(0);
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
                      disabled={isLoading}
                    >
                      {isLoading ? "Processing..." : "Place Order"}
                    </button>
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
