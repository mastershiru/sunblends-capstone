import React, { useState, useEffect } from "react";
import axios from "axios";
import Orderdetails from "../modal-compinents/order-details";

function Queue() {
  const [orders, setOrders] = useState([]);
  const [isOpenOrderdetails, setIsOpenOrderdetails] = useState(false);
  const [selectedOrderId, setSelectedOrderId] = useState(null); // State to store the selected Order_ID
  const toggleModalOrderdetails = (orderId) => {
    setSelectedOrderId(orderId); // Set the selected order ID
    setIsOpenOrderdetails(!isOpenOrderdetails);
  };

  // Fetch orders from the backend (both orders and walkin_orders)
  useEffect(() => {
    const fetchOrders = async () => {
      try {
        const response = await axios.get("http://localhost:8081/getAllOrders"); // Modify the backend endpoint accordingly
        setOrders(response.data); // Set orders state with fetched data
      } catch (error) {
        console.error("Error fetching orders:", error);
      }
    };

    fetchOrders();
  }, []);

  // Handle order status change with confirmation
  // Handle order status change with confirmation
  const handleStatusChange = (orderId, newStatus) => {
    const confirmUpdate = window.confirm(
      `Are you sure you want to update the Order Status for Order No.: ${orderId} to ${newStatus}?`
    );

    if (confirmUpdate) {
      setOrders((prevOrders) =>
        prevOrders.map((order) =>
          order.Order_ID === orderId
            ? { ...order, Order_Status: newStatus }
            : order
        )
      );
      updateOrderStatus(orderId, newStatus);
    } else {
      setOrders((prevOrders) =>
        prevOrders.map((order) =>
          order.Order_ID === orderId
            ? { ...order, Order_Status: order.Order_Status }
            : order
        )
      );
    }
  };

  // Update order status in the database
  const updateOrderStatus = async (orderId, newStatus) => {
    try {
      const response = await axios.post(
        "http://localhost:8081/updateOrderStatus",
        { orderId, newStatus }
      );

      if (response.data === "Order status updated successfully") {
        alert(`Order ${orderId} updated to ${newStatus}`);
      } else {
        alert("Failed to update the order status.");
      }
    } catch (error) {
      console.error("Error updating order status:", error);
      alert("Failed to update order status.");
    }
  };

  return (
    <>
      <header>
        <h2>Queue</h2>
      </header>
      <table>
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Order Date</th>
            <th>Order Type</th>
            <th>Order Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          {orders.length > 0 ? (
            orders.map((order) => (
              <tr key={order.Order_ID}>
                <td>{order.Order_ID}</td>
                <td>{new Date(order.Order_DateTime).toLocaleString()}</td>
                <td>{order.Order_Type}</td>
                <td>
                  <div
                    className="drop-down-order-status"
                    style={{ padding: "10px" }}
                  >
                    <select
                      className="select-status"
                      value={order.Order_Status}
                      onChange={(e) =>
                        handleStatusChange(order.Order_ID, e.target.value)
                      }
                      style={{
                        color:
                          order.Order_Status === "Pending"
                            ? "yellow"
                            : order.Order_Status === "Delivered"
                            ? "green"
                            : order.Order_Status === "Cancelled"
                            ? "red"
                            : "black",
                      }}
                    >
                      <option value="">
                        {order.Order_Status || "Select Status"}
                      </option>
                      <option value="Pending">Pending</option>
                      <option value="Delivered">Delivered</option>
                      <option value="Cancelled">Cancelled</option>
                    </select>
                  </div>
                </td>

                <td>
                  <button
                    className="view-order"
                    onClick={() => toggleModalOrderdetails(order.Order_ID)}
                  >
                    View
                  </button>
                </td>
              </tr>
            ))
          ) : (
            <tr>
              <td colSpan="5">No orders available</td>
            </tr>
          )}
        </tbody>
      </table>

      {/* Pass the selected Order_ID to the Orderdetails component */}
      <Orderdetails
        isOpenOrderdetails={isOpenOrderdetails}
        setIsOpenOrderdetails={setIsOpenOrderdetails}
        selectedOrderId={selectedOrderId} // Send the selected order ID
      />
    </>
  );
}

export default Queue;
