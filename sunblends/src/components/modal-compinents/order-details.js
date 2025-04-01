// import React, { useState, useEffect } from "react";
// import axios from "axios";
// import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
// import {
//   faXmark,
//   faSpinner,
//   faArrowLeft,
//   faCheck,
//   faShoppingBag,
// } from "@fortawesome/free-solid-svg-icons";
// import "../../assets/css/modal.css";

// const OrderDetails = ({
//   isOpenOrderDetails,
//   toggleOrderDetails,
//   selectedOrderId,
//   order,
// }) => {
//   const [orderDetails, setOrderDetails] = useState(null);
//   const [cartItems, setCartItems] = useState([]);
//   const [isLoading, setIsLoading] = useState(false);
//   const [error, setError] = useState(null);

//   // We'll use this as a fallback image directly in the component
//   const fallbackImageSrc =
//     "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADwAAAA8CAYAAAA6/NlyAAAACXBIWXMAAAsTAAALEwEAmpwYAAABsUlEQVR4nO3av2oUURTH8c8aFexs7K0ELQQfwMbKN7ASfAQrwdoHsLGxsLERBBvBwkIQJJWVEIidhQmIpZUQMCDsJhPIwjK7O3Nn5pzZ+4XTDPf+frn3zOyZgSRJkiRJkvTfOwbcAZaAdeA7MA9cGCHfBHAfeAV8Bb4AK8DdOGZlp4DrwAfKLQPXauSdAlaHyPsRuFkjZ6uuAJ965D2Ix3rpDHAD+Bb/3gNOD9PRJPC8R+GbPdqPxzXYLfszcKlH+0ngeaTZBK6WNH4a6a1twIvAAZuF128DB0PamwPmCu83gKfA+ZKc+4V2a8DFko4vFdpsFZ6rGisd140yOxEDFG2WDNZaoV3ZgO/G666V2o1sLmkcOQJjw+pt1Vr6QkfnexjPw8bdXHzrTO31vgTsxuOP6ArMtJT3TvQNcKyl3KN1EngT/ay0lTzVYuEHwOm2O0mNH1O7wL0uOkmFHzK7wJ0uOkmFB0u7wO0uOkmFXzS7wK0uOkmFJ+NmN37sl/jZZTep8Gj9G/B/oCJJkiRJkiRJkiRJkiRJkiRJ0pD+AvsYaqkq8MmZAAAAAElFTkSuQmCC";

//   // Fetch order details when the modal opens or selectedOrderId changes
//   useEffect(() => {
//     if (isOpenOrderDetails && selectedOrderId) {
//       fetchOrderDetails(selectedOrderId);
//     }
//   }, [isOpenOrderDetails, selectedOrderId]);

//   const fetchOrderDetails = async (orderId) => {
//     setIsLoading(true);
//     setError(null);

//     try {
//       // If we already have basic order info, use it

//       // Otherwise fetch it
//       console.log("Fetching order data for ID:", orderId);
//       const orderResponse = await axios.get(
//         `http://127.0.0.1:8000/api/orders/${orderId}`
//       );
//       console.log("API response for order:", orderResponse.data);
//       setOrderDetails(orderResponse.data);

//       // Always fetch the cart items
//       const cartResponse = await axios.get(
//         `http://127.0.0.1:8000/api/orders/${orderId}/items`
//       );

//       console.log("Cart items response:", cartResponse.data);
//       if (cartResponse.data && cartResponse.data.success) {
//         setCartItems(cartResponse.data.items || []);
//       } else {
//         console.error(
//           "Cart items response format unexpected:",
//           cartResponse.data
//         );
//         setCartItems([]);
//       }
//     } catch (error) {
//       console.error("Error fetching order details:", error);
//       setError("Failed to load order details. Please try again.");
//     } finally {
//       setIsLoading(false);
//     }
//   };

//   // Calculate total price with safety checks
//   const calculateTotal = () => {
//     if (!cartItems || !cartItems.length) return 0;

//     return cartItems.reduce((total, item) => {
//       const price = parseFloat(item.Item_Price) || 0;
//       const quantity = parseInt(item.Item_Quantity) || 0;
//       return total + price * quantity;
//     }, 0);
//   };

//   // Format date with error handling
//   const formatDate = (dateString) => {
//     if (!dateString) return "N/A";
//     try {
//       const date = new Date(dateString);
//       return date.toLocaleString();
//     } catch (e) {
//       console.error("Error formatting date:", e);
//       return "Invalid Date";
//     }
//   };

//   // Get status badge class with safe fallback
//   const getStatusBadgeClass = (status) => {
//     if (!status) return "#6c757d"; // Default gray for undefined/null status

//     switch (status.toLowerCase()) {
//       case "processing":
//         return "#007bff";
//       case "completed":
//         return "#28a745";
//       case "ready":
//         return "#17a2b8";
//       case "cancelled":
//         return "#dc3545";
//       default:
//         return "#6c757d";
//     }
//   };

//   // Close the modal
//   const closeModal = () => {
//     toggleOrderDetails(false);
//   };

//   // Get total items count
//   const getTotalItemsCount = () => {
//     if (!cartItems || !cartItems.length) return 0;

//     return cartItems.reduce((total, item) => {
//       return total + (parseInt(item.Item_Quantity) || 0);
//     }, 0);
//   };

//   return (
//     <>
//       {isOpenOrderDetails && (
//         <div className="modal-backdrop" id="order-details-modal">
//           <div
//             className="modal-content"
//             style={{
//               maxWidth: "600px",
//               width: "90%",
//               backgroundColor: "white",
//               borderRadius: "8px",
//               boxShadow: "0 5px 15px rgba(0,0,0,0.3)",
//               position: "relative",
//               overflow: "hidden",
//               maxHeight: "80vh",
//               overflowY: "auto",
//             }}
//           >
//             <div
//               className="modal-header"
//               style={{
//                 borderBottom: "1px solid #eee",
//                 padding: "15px 20px",
//                 display: "flex",
//                 justifyContent: "space-between",
//                 alignItems: "center",
//               }}
//             >
//               <div
//                 style={{ display: "flex", alignItems: "center", gap: "10px" }}
//               >
//                 <button
//                   onClick={closeModal}
//                   style={{
//                     border: "none",
//                     background: "none",
//                     fontSize: "1rem",
//                     cursor: "pointer",
//                     padding: "0",
//                     color: "#999",
//                     display: "flex",
//                     alignItems: "center",
//                     gap: "5px",
//                   }}
//                 >
//                   <FontAwesomeIcon icon={faArrowLeft} />
//                   Back
//                 </button>
//               </div>

//               <h2 style={{ margin: 0, fontSize: "1.25rem" }}>
//                 Order Details{" "}
//                 {orderDetails && `#${orderDetails.order_id || ""}`}
//               </h2>

//               <button
//                 onClick={closeModal}
//                 style={{
//                   border: "none",
//                   background: "none",
//                   fontSize: "1.5rem",
//                   cursor: "pointer",
//                   padding: "0",
//                   color: "#999",
//                 }}
//               >
//                 <FontAwesomeIcon icon={faXmark} />
//               </button>
//             </div>

//             <div className="modal-body" style={{ padding: "20px" }}>
//               {isLoading ? (
//                 <div style={{ textAlign: "center", padding: "30px" }}>
//                   <FontAwesomeIcon icon={faSpinner} spin size="2x" />
//                   <p style={{ marginTop: "10px" }}>Loading order details...</p>
//                 </div>
//               ) : error ? (
//                 <div
//                   style={{ textAlign: "center", padding: "30px", color: "red" }}
//                 >
//                   <p>{error}</p>
//                   <button
//                     onClick={() =>
//                       selectedOrderId && fetchOrderDetails(selectedOrderId)
//                     }
//                     style={{
//                       padding: "8px 16px",
//                       backgroundColor: "#ff8243",
//                       color: "white",
//                       border: "none",
//                       borderRadius: "4px",
//                       cursor: "pointer",
//                       marginTop: "10px",
//                     }}
//                   >
//                     Try Again
//                   </button>
//                 </div>
//               ) : orderDetails ? (
//                 <>
//                   {/* Order Status Banner */}
//                   <div
//                     style={{
//                       backgroundColor: getStatusBadgeClass(orderDetails.status),
//                       padding: "15px",
//                       borderRadius: "6px",
//                       marginBottom: "20px",
//                       color: "white",
//                       display: "flex",
//                       alignItems: "center",
//                       justifyContent: "space-between",
//                     }}
//                   >
//                     <div
//                       style={{
//                         display: "flex",
//                         alignItems: "center",
//                         gap: "10px",
//                       }}
//                     >
//                       <div
//                         style={{
//                           backgroundColor: "rgba(255, 255, 255, 0.3)",
//                           width: "32px",
//                           height: "32px",
//                           borderRadius: "50%",
//                           display: "flex",
//                           alignItems: "center",
//                           justifyContent: "center",
//                         }}
//                       >
//                         <FontAwesomeIcon
//                           icon={
//                             orderDetails.status === "completed"
//                               ? faCheck
//                               : faShoppingBag
//                           }
//                         />
//                       </div>
//                       <div>
//                         <h3 style={{ margin: 0, fontSize: "1.2rem" }}>
//                           {orderDetails.status
//                             ? orderDetails.status.toUpperCase()
//                             : "PENDING"}
//                         </h3>
//                       </div>
//                     </div>

//                     <div>{formatDate(orderDetails.created_at)}</div>
//                   </div>

//                   {/* Customer Information */}
//                   <div
//                     className="customer-info"
//                     style={{
//                       backgroundColor: "#f9f9f9",
//                       padding: "15px",
//                       borderRadius: "6px",
//                       marginBottom: "20px",
//                     }}
//                   >
//                     <h3
//                       style={{
//                         fontSize: "1rem",
//                         marginTop: "0",
//                         marginBottom: "10px",
//                         color: "#555",
//                       }}
//                     >
//                       Customer Information
//                     </h3>

//                     <div
//                       style={{
//                         display: "grid",
//                         gridTemplateColumns:
//                           "repeat(auto-fill, minmax(200px, 1fr))",
//                         gap: "10px",
//                       }}
//                     >
//                       <div className="info-item">
//                         <strong>Name:</strong>{" "}
//                         {orderDetails.customer?.Customer_Name || "N/A"}
//                       </div>
//                       <div className="info-item">
//                         <strong>Email:</strong>{" "}
//                         {orderDetails.customer?.Customer_Email || "N/A"}
//                       </div>
//                       <div className="info-item">
//                         <strong>Phone:</strong>{" "}
//                         {orderDetails.customer?.Customer_Number || "N/A"}
//                       </div>
//                       <div className="info-item">
//                         <strong>Order Total:</strong> ₱
//                         {parseFloat(orderDetails.total_price || 0).toFixed(2)}
//                       </div>
//                       <div className="info-item">
//                         <strong>Items Count:</strong> {getTotalItemsCount()}
//                       </div>
//                     </div>
//                   </div>

//                   {/* Order Items */}
//                   <div className="order-items">
//                     <h3
//                       style={{
//                         fontSize: "1rem",
//                         marginTop: "0",
//                         marginBottom: "10px",
//                         color: "#555",
//                       }}
//                     >
//                       Order Items
//                     </h3>

//                     <div
//                       style={{
//                         borderRadius: "6px",
//                         overflow: "hidden",
//                         border: "1px solid #eee",
//                       }}
//                     >
//                       {/* Table Header */}
//                       <div
//                         style={{
//                           display: "grid",
//                           gridTemplateColumns: "80px 1fr auto auto",
//                           backgroundColor: "#f9f9f9",
//                           padding: "10px 15px",
//                           fontWeight: "bold",
//                           borderBottom: "1px solid #eee",
//                         }}
//                       >
//                         <div>Image</div>
//                         <div>Item</div>
//                         <div style={{ textAlign: "center" }}>Qty</div>
//                         <div style={{ textAlign: "right" }}>Price</div>
//                       </div>

//                       {/* Item Rows */}
//                       {cartItems.length > 0 ? (
//                         cartItems.map((item, index) => (
//                           <div
//                             key={index}
//                             style={{
//                               display: "grid",
//                               gridTemplateColumns: "80px 1fr auto auto",
//                               padding: "10px 15px",
//                               alignItems: "center",
//                               borderBottom:
//                                 index < cartItems.length - 1
//                                   ? "1px solid #eee"
//                                   : "none",
//                             }}
//                           >
//                             <div>
//                               {/* Using inline image with error handling */}
//                               <img
//                                 src={item.Item_Img || fallbackImageSrc}
//                                 alt={item.Item_Title || "Menu Item"}
//                                 style={{
//                                   width: "60px",
//                                   height: "60px",
//                                   objectFit: "cover",
//                                   borderRadius: "4px",
//                                   backgroundColor: "#f0f0f0",
//                                 }}
//                                 onError={(e) => {
//                                   // Fall back to inline image on error
//                                   e.target.onerror = null; // Prevent infinite loop
//                                   e.target.src = fallbackImageSrc;
//                                 }}
//                               />
//                             </div>
//                             <div style={{ paddingLeft: "10px" }}>
//                               <div style={{ fontWeight: "500" }}>
//                                 {item.Item_Title || "Unknown Item"}
//                               </div>
//                               <div
//                                 style={{ fontSize: "0.8rem", color: "#666" }}
//                               >
//                                 {item.Item_Category || "Uncategorized"}
//                               </div>
//                             </div>
//                             <div
//                               style={{ textAlign: "center", fontWeight: "500" }}
//                             >
//                               {item.Item_Quantity || 0}
//                             </div>
//                             <div style={{ textAlign: "right" }}>
//                               <div>
//                                 ₱{parseFloat(item.Item_Price || 0).toFixed(2)}
//                               </div>
//                               <div style={{ fontWeight: "500" }}>
//                                 ₱
//                                 {(
//                                   parseFloat(item.Item_Price || 0) *
//                                   parseInt(item.Item_Quantity || 0)
//                                 ).toFixed(2)}
//                               </div>
//                             </div>
//                           </div>
//                         ))
//                       ) : (
//                         <div
//                           style={{
//                             padding: "20px",
//                             textAlign: "center",
//                             color: "#666",
//                           }}
//                         >
//                           No items found for this order.
//                         </div>
//                       )}

//                       {/* Total Row */}
//                       {cartItems.length > 0 && (
//                         <div
//                           style={{
//                             display: "flex",
//                             justifyContent: "space-between",
//                             padding: "15px",
//                             backgroundColor: "#f9f9f9",
//                             fontWeight: "bold",
//                             borderTop: "1px solid #eee",
//                           }}
//                         >
//                           <div>Total</div>
//                           <div>₱{calculateTotal().toFixed(2)}</div>
//                         </div>
//                       )}
//                     </div>
//                   </div>
//                 </>
//               ) : (
//                 <div
//                   style={{
//                     textAlign: "center",
//                     padding: "30px",
//                     color: "#666",
//                   }}
//                 >
//                   <p>No order selected</p>
//                 </div>
//               )}
//             </div>

//             <div
//               className="modal-footer"
//               style={{
//                 borderTop: "1px solid #eee",
//                 padding: "15px 20px",
//                 display: "flex",
//                 justifyContent: "flex-end",
//                 gap: "10px",
//               }}
//             >
//               <button
//                 onClick={closeModal}
//                 style={{
//                   padding: "8px 16px",
//                   backgroundColor: "#f5f5f5",
//                   border: "1px solid #ddd",
//                   borderRadius: "4px",
//                   cursor: "pointer",
//                 }}
//               >
//                 Close
//               </button>
//             </div>
//           </div>
//         </div>
//       )}
//     </>
//   );
// };

// export default OrderDetails;
