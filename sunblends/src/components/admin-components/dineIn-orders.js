import React, { useState, useEffect } from "react";
import axios from "axios";

function Dineinorders() {
  const [menus, setMenus] = useState([]);
  const [order, setOrder] = useState([]);
  const [customerName, setCustomerName] = useState("");

  useEffect(() => {
    fetchMenuData();
  }, []);

  const fetchMenuData = () => {
    fetch("http://localhost:8081/getMenu")
      .then((response) => response.json())
      .then((data) => setMenus(data))
      .catch((error) => console.error("Error fetching menu data:", error));
  };

  const addToOrder = (menu) => {
    setOrder((prevOrder) => {
      const existingItem = prevOrder.find(
        (item) => item.Dish_ID === menu.Dish_ID
      );
      if (existingItem) {
        return prevOrder.map((item) =>
          item.Dish_ID === menu.Dish_ID
            ? { ...item, quantity: item.quantity + 1 }
            : item
        );
      }
      return [...prevOrder, { ...menu, quantity: 1 }];
    });
  };

  const updateQuantity = (id, delta) => {
    setOrder((prevOrder) =>
      prevOrder
        .map((item) =>
          item.Dish_ID === id
            ? { ...item, quantity: item.quantity + delta }
            : item
        )
        .filter((item) => item.quantity > 0)
    );
  };

  const calculateTotal = () =>
    order.reduce((total, item) => total + item.Dish_Price * item.quantity, 0);

  const handleSubmitOrder = async () => {
    const totalPayment = calculateTotal();
    if (!customerName || totalPayment <= 0) {
      alert("Please enter a customer name and ensure your order is not empty.");
      return;
    }

    const orderData = {
      customerName,
      orderDateTime: new Date().toISOString(),
      orderStatus: "Pending",
      orderType: "Dine-in",
      totalPayment,
      orderItems: order.map((item) => ({
        Item_Img: item.Dish_Img,
        Item_Title: item.Dish_Title,
        Item_Quantity: item.quantity,
        Item_Price: item.Dish_Price,
      })),
    };

    try {
      // Send the order and order items to the backend
      const response = await axios.post(
        "http://localhost:8081/insertDineinOrder",
        orderData
      );
      console.log("Order submitted:", response.data);
      alert("Order submitted successfully!");

      // Optionally, clear the order and customer name after submission
      setOrder([]);
      setCustomerName("");
    } catch (error) {
      console.error("Error submitting order:", error);
      alert("There was an error submitting your order. Please try again.");
    }
  };

  return (
    <div>
      <header>
        <h2>Dine in Orders</h2>
      </header>

      <div className="row">
        <div className="col" style={{ padding: "0" }}>
          <ul className="categories">
            <li>Appetizers</li>
            <li>Main Course</li>
            <li>Side Dishes</li>
            <li>Dessert</li>
            <li>Beverages</li>
            <li>Breakfast Foods</li>
          </ul>
          <table>
            <thead>
              <tr>
                <th></th>
                <th>Title</th>
                <th>Type</th>
                <th>Price</th>
                <th>Available</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              {menus.length > 0 ? (
                menus.map((menu) => (
                  <tr key={menu.Dish_ID}>
                    <td>
                      <img
                        src={`http://localhost:8081/${menu.Dish_Img}`}
                        alt={menu.Dish_Title}
                        width="50"
                      />
                    </td>
                    <td>{menu.Dish_Title}</td>
                    <td>{menu.Dish_Type}</td>
                    <td>₱{menu.Dish_Price}</td>
                    <td
                      style={{
                        color: menu.isAvailable ? "green" : "red",
                        textAlign: "center",
                      }}
                    >
                      {menu.isAvailable ? "Yes" : "No"}
                    </td>
                    <td>
                      <button
                        className="add-order"
                        onClick={() => addToOrder(menu)}
                      >
                        Add
                      </button>
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan="6">No menu items available</td>
                </tr>
              )}
            </tbody>
          </table>
        </div>

        <div className="col" style={{ padding: "30px" }}>
          <h6 style={{ padding: "10px" }}>Your Order</h6>
          <div className="cart-items">
            {order.length > 0 ? (
              order.map((item, index) => (
                <div key={index} className="cart-item-content">
                  <div className="item-info-container">
                    <img
                      src={`http://localhost:8081/${item.Dish_Img}`}
                      alt={item.Dish_Title}
                      className="cart-item-img"
                      style={{ width: "50px", height: "50px" }}
                    />
                    <div>
                      <p>{item.Dish_Title}</p>
                      <p style={{ textAlign: "left" }}>₱{item.Dish_Price}</p>
                    </div>
                  </div>
                  <div className="cart-btn-container">
                    <button
                      className="cart-btn"
                      onClick={() => updateQuantity(item.Dish_ID, -1)}
                    >
                      -
                    </button>
                    <span className="quantity">{item.quantity}</span>
                    <button
                      className="cart-btn"
                      onClick={() => updateQuantity(item.Dish_ID, 1)}
                    >
                      +
                    </button>
                  </div>
                </div>
              ))
            ) : (
              <p>Add to order</p>
            )}
          </div>
          <div>
            <p>Subtotal: ₱{calculateTotal()}</p>
            <p>
              Customer Name:{" "}
              <input
                type="text"
                value={customerName}
                onChange={(e) => setCustomerName(e.target.value)}
              />
            </p>
            <button
              style={{ background: "green", color: "white" }}
              onClick={handleSubmitOrder}
            >
              Proceed Order
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}

export default Dineinorders;
