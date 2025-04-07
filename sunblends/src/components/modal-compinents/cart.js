import React, { useState, useEffect } from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faXmark } from "@fortawesome/free-solid-svg-icons";
import "../../assets/css/modal.css";
import Checkout from "./checkout";
import axios from "axios";

const Cart = ({
  isOpenCart,
  toggleModalCart,
  cartItems,
  setCartItems,
  setCartNumber,
  isLoggedIn,
  toggleModalLogin,
  setIsOpenCart,
}) => {
  const [isOpenCheckout, setIsOpenCheckout] = useState(false);
  const [isCheckoutComplete, setIsCheckoutComplete] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState(null);

  // Calculate cart total
  const cartTotal = cartItems.reduce(
    (total, item) => total + item.price * item.quantity,
    0
  );

  // Fetch cart items from server when component mounts and when isLoggedIn changes
  useEffect(() => {
    if (isLoggedIn && isOpenCart) {
      fetchCartFromServer();
    }
  }, [isLoggedIn, isOpenCart]);

  // Fetch cart data from Laravel backend
  const fetchCartFromServer = async () => {
    const email = localStorage.getItem("email");
    if (!email) return;

    setIsLoading(true);
    setError(null);

    try {
      const response = await axios.post(
        "https://api.sunblends.store/api/getCartItems",
        { email },
        { 
          withCredentials: true,
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          }
        }
      );

      if (response.data.success) {
        setCartItems(response.data.cart_items);
        setCartNumber(
          response.data.cart_items.reduce((sum, item) => sum + item.quantity, 0)
        );
      }
    } catch (error) {
      console.error("Failed to fetch cart items:", error);
      setError("Failed to load cart items");
    } finally {
      setIsLoading(false);
    }
  };

  // Handle increment quantity
  const handleIncrement = async (item) => {
    const email = localStorage.getItem("email");

    if (!isLoggedIn || !email) {
      // For guest users or when not logged in
      setCartItems((prevItems) => {
        const updatedItems = [...prevItems];
        const index = updatedItems.findIndex(
          (i) =>
            (i.id && item.id && i.id === item.id) ||
            (i.title === item.title && i.price === item.price)
        );

        if (index !== -1) {
          updatedItems[index].quantity += 1;
          setCartNumber((prev) => prev + 1);
        }
        return updatedItems;
      });
      return;
    }

    // For logged-in users
    try {
      setError(null);
      const response = await axios.post(
        "https://api.sunblends.store/api/updateCartItem",
        {
          cart_id: item.id,
          quantity: item.quantity + 1,
        },
        { withCredentials: true }
      );

      if (response.data.success) {
        // Refresh cart after successful update
        fetchCartFromServer();
      } else {
        setError(response.data.message || "Failed to update item");
      }
    } catch (error) {
      console.error("Error updating cart:", error);
      setError("Failed to update item quantity");
    }
  };

  // Handle decrement quantity
  const handleDecrement = async (item) => {
    const email = localStorage.getItem("email");

    if (!isLoggedIn || !email) {
      // For guest users or when not logged in
      setCartItems((prevItems) => {
        const updatedItems = [...prevItems];
        const index = updatedItems.findIndex(
          (i) =>
            (i.id && item.id && i.id === item.id) ||
            (i.title === item.title && i.price === item.price)
        );

        if (index !== -1) {
          if (updatedItems[index].quantity > 1) {
            updatedItems[index].quantity -= 1;
            setCartNumber((prev) => prev - 1);
          } else {
            // Remove item if quantity would be 0
            updatedItems.splice(index, 1);
            setCartNumber((prev) => prev - 1);
          }
        }
        return updatedItems;
      });
      return;
    }

    // For logged-in users
    try {
      setError(null);

      if (item.quantity === 1) {
        // Remove item if quantity is 1
        const response = await axios.post(
          "https://api.sunblends.store/api/removeCartItem",
          { cart_id: item.id },
          { withCredentials: true }
        );

        if (response.data.success) {
          fetchCartFromServer();
        } else {
          setError(response.data.message || "Failed to remove item");
        }
      } else {
        // Decrease quantity
        const response = await axios.post(
          "https://api.sunblends.store/api/updateCartItem",
          {
            cart_id: item.id,
            quantity: item.quantity - 1,
          },
          { withCredentials: true }
        );

        if (response.data.success) {
          fetchCartFromServer();
        } else {
          setError(response.data.message || "Failed to update item");
        }
      }
    } catch (error) {
      console.error("Error updating cart:", error);
      setError("Failed to update item quantity");
    }
  };

  const handleCheckout = () => {
    if (cartItems.length === 0) {
      alert("Cart is empty.");
      toggleModalCart(false);
      return;
    }

    if (!isLoggedIn) {
      toggleModalCart(false);
      toggleModalLogin();
    } else {
      setIsOpenCheckout(true);
      toggleModalCart(false);
    }
  };

  // Update cart after successful checkout
  const handleCheckoutComplete = () => {
    setIsCheckoutComplete(true);
    setCartItems([]);
    setCartNumber(0);
    setIsOpenCheckout(false);
  };

  return (
    <>
      {isOpenCart && (
        <div className="center" id="center">
          <div className="popup">
            <div className="close-btn" id="close-btn">
              <FontAwesomeIcon
                icon={faXmark}
                onClick={() => toggleModalCart(false)}
              />
            </div>
            <div className="cart" id="cart-section">
              <h2>Cart</h2>

              {error && (
                <div className="alert alert-danger" role="alert">
                  {error}
                </div>
              )}

              {isLoading ? (
                <div className="text-center py-3">Loading cart items...</div>
              ) : (
                <div className="cart-items">
                  {cartItems.length > 0 ? (
                    cartItems.map((item, index) => (
                      <div key={item.id || index} className="cart-item-content">
                        <div className="item-info-container">
                          <img
                            src={
                              item.img.startsWith("http")
                                ? item.img // External URL
                                : `http://127.0.0.1:8000/${item.img}` // Local storage image
                            }
                            alt={item.title}
                            className="cart-item-img"
                            style={{ width: "50px", height: "50px" }}
                            onError={(e) => {
                              e.target.onerror = null;
                              e.target.src =
                                "http://127.0.0.1:8000/images/placeholder-food.jpg";
                            }}
                          />
                          <div>
                            <p>{item.title}</p>
                            <p style={{ textAlign: "left" }}>
                              ₱
                              {typeof item.price === "number"
                                ? item.price.toFixed(2)
                                : item.price}
                            </p>
                          </div>
                        </div>
                        <div className="cart-btn-container">
                          <button
                            className="cart-btn"
                            onClick={() => handleDecrement(item)}
                            disabled={isLoading}
                          >
                            -
                          </button>
                          <span className="quantity">{item.quantity}</span>
                          <button
                            className="cart-btn"
                            onClick={() => handleIncrement(item)}
                            disabled={isLoading}
                          >
                            +
                          </button>
                        </div>
                      </div>
                    ))
                  ) : (
                    <p>Your cart is empty.</p>
                  )}
                </div>
              )}

              <div>
                {cartItems.length > 0 && (
                  <>
                    <p>
                      <b>Total:</b> ₱
                      {typeof cartTotal === "number"
                        ? cartTotal.toFixed(2)
                        : cartTotal}
                    </p>
                    <button
                      className="checkout-btn"
                      onClick={handleCheckout}
                      disabled={isLoading}
                    >
                      {isLoading ? "Loading..." : "Checkout"}
                    </button>
                  </>
                )}
              </div>
            </div>
          </div>
        </div>
      )}
      <Checkout
        isOpenCheckout={isOpenCheckout}
        setIsOpenCheckout={setIsOpenCheckout}
        cartItems={cartItems}
        cartTotal={cartTotal}
        toggleModalCart={toggleModalCart}
        handleCheckoutComplete={handleCheckoutComplete}
      />
    </>
  );
};

export default Cart;
