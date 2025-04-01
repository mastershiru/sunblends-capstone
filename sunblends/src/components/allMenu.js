import React, { useState, useEffect } from "react";
import axios from "axios";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faPlus, faSearch, faSpinner } from "@fortawesome/free-solid-svg-icons";
import imagebg from "../assets/images/menu-bg.png";
import titleshape from "../assets/images/title-shape.svg";
import Navbar from "./Navbar";
import { useLocation } from "react-router-dom";
import NotificationModal from "./modal-compinents/notification-modal";
import NotificationsCenter from "./modal-compinents/notification-center";
import NotificationManager from "./notifications/Notification-manager";
import { useNavbar } from "../context/NavbarContext";

const AllMenu = () => {
  const [menuItems, setMenuItems] = useState([]);
  const [addingToCart, setAddingToCart] = useState({});
  const [searchTerm, setSearchTerm] = useState("");
  const [categories, setCategories] = useState([]);
  const [selectedCategory, setSelectedCategory] = useState("");
  
  // Get all necessary values and functions from NavbarContext
  const { 
    isLoggedIn,
    userData,
    addToCartNumber,
    addToCart,
    statusModalOpen,
    statusModalData,
    viewOrderDetails,
    toggleStatusModal,
    isNotificationCenterOpen,
    notifications,
    clearNotifications,
    toggleNotificationCenter
  } = useNavbar();

  // Navigate to other sections
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

  // Fetch menu items
  useEffect(() => {
    const fetchMenuItems = async () => {
      try {
        const response = await axios.get(
          "http://127.0.0.1:8000/api/menu-items"
        );
        const availableItems = response.data.filter(
          (dish) => dish.isAvailable !== 0 && dish.isAvailable !== false
        );
        setMenuItems(availableItems);

        // Extract unique categories once
        setCategories([
          ...new Set(availableItems.map((dish) => dish.Dish_Type)),
        ]);
      } catch (error) {
        console.error("Error fetching menu:", error);
      }
    };

    fetchMenuItems();
  }, []);

  // Filter menu items
  const filteredMenu = menuItems.filter(
    (dish) =>
      (dish.Dish_Type.toLowerCase().includes(searchTerm.toLowerCase()) ||
        dish.Dish_Title.toLowerCase().includes(searchTerm.toLowerCase())) &&
      (selectedCategory === "" || dish.Dish_Type === selectedCategory)
  );

  // Add to cart handler
  const handleAddToCart = async (dish) => {
    // Set the specific dish as loading
    setAddingToCart((prev) => ({ ...prev, [dish.id]: true }));

    try {
      if (isLoggedIn && userData) {
        // For logged-in users, add to server-side cart using TokenManager via context
        await axios.post(
          "http://127.0.0.1:8000/api/addToCart",
          {
            email: userData.customer_email,
            dish_id: dish.id,
            quantity: 1,
          },
          { 
            headers: {
              'Authorization': `Bearer ${localStorage.getItem('token')}`,
              'Accept': 'application/json'
            },
            withCredentials: true 
          }
        );
      }

      // Add to context cart
      addToCart({
        id: dish.id,
        img: dish.Dish_Img,
        title: dish.Dish_Title,
        price: dish.Dish_Price,
        quantity: 1,
      });

      // Increment cart count
      
    } catch (error) {
      console.error("Error adding item to cart:", error);
      alert("Failed to add item to cart. Please try again.");
    } finally {
      // Clear loading state for this dish
      setAddingToCart((prev) => ({ ...prev, [dish.id]: false }));
    }
  };

  return (
    <div>
      <NotificationManager />

      <NotificationModal
        isOpen={statusModalOpen}
        onClose={toggleStatusModal}
        data={statusModalData}
        onViewOrder={viewOrderDetails}
      />

      {/* Notifications center modal */}
      <NotificationsCenter
        isOpen={isNotificationCenterOpen}
        onClose={() => toggleNotificationCenter(false)}
        notifications={notifications}
        onMarkAllAsRead={clearNotifications}
        onViewDetails={viewOrderDetails}
      />
      
      <Navbar />

      <section
        className="our-menu section bg-light repeat-img"
        id="menu"
        style={{ backgroundImage: `url(${imagebg})` }}
      >
        <div className="sec-wp">
          <div className="container">
            <div className="row">
              <div className="col-lg-12">
                <div className="sec-title text-center ">
                  <p className="sec-sub-title mb-3">our menu</p>

                  <div className="sec-title-shape mb-4">
                    <img src={titleshape} alt="" />
                  </div>
                </div>
              </div>
            </div>
            {/* Search bar */}
            <div className="search-bar mb-4 text-center">
              <div className="search-container">
                <FontAwesomeIcon icon={faSearch} className="search-icon" />
                <input
                  type="text"
                  placeholder="Search"
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                  className="search-input"
                />
              </div>
            </div>
            {/* Horizontal Category List */}
            <div className="category-list">
              <button
                className={`category-item ${
                  selectedCategory === "" ? "active" : ""
                }`}
                onClick={() => setSelectedCategory("")}
              >
                All
              </button>
              {categories.map((category, index) => (
                <button
                  key={index}
                  className={`category-item ${
                    selectedCategory === category ? "active" : ""
                  }`}
                  onClick={() => setSelectedCategory(category)}
                >
                  {category}
                </button>
              ))}
            </div>

            <div className="menu-list-row">
              <div className="row g-xxl-5 bydefault_show" id="menu-dish">
                {filteredMenu.map((dish) => (
                  <div
                    key={dish.id}
                    className={`col-lg-4 col-sm-6 dish-box-wp ${dish.Dish_Type.toLowerCase()}`}
                    data-cat={dish.Dish_Type.toLowerCase()}
                  >
                    <div className="dish-box text-center">
                      <div className="dist-img">
                        <img
                          src={dish.Dish_Img}
                          alt={dish.Dish_Title}
                          onError={(e) => {
                            e.target.onerror = null;
                            e.target.src = "assets/images/placeholder-food.jpg";
                          }}
                        />
                      </div>
                      <div className="dish-rating">
                        {dish.Dish_Rating}
                        <i className="uil uil-star"></i>
                      </div>
                      <div className="dish-title">
                        <h3 className="h3-title">{dish.Dish_Title}</h3>
                        <p>{dish.category}</p>
                      </div>
                      <div className="dish-info">
                        <ul>
                          <li>
                            <p>Type</p>
                            <b>{dish.Dish_Type}</b>
                          </li>
                          <li className="person">
                            <p>Persons</p>
                            <b>{dish.Dish_Persons || 1}</b>
                          </li>
                        </ul>
                      </div>
                      <div className="dist-bottom-row">
                        <ul>
                          <li>
                            <b className="price">
                              ₱
                              {typeof dish.Dish_Price === "number"
                                ? dish.Dish_Price.toFixed(2)
                                : dish.Dish_Price}
                            </b>
                          </li>
                          <li>
                            <button
                              className="dish-add-btn"
                              onClick={() => handleAddToCart(dish)}
                              disabled={addingToCart[dish.id]}
                            >
                              {addingToCart[dish.id] ? (
                                <FontAwesomeIcon icon={faSpinner} spin />
                              ) : (
                                <FontAwesomeIcon icon={faPlus} />
                              )}
                            </button>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                ))}

                {menuItems.length === 0 && (
                  <div className="col-12 text-center py-4">
                    <p>No menu items available right now.</p>
                  </div>
                )}
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  );
};

export default AllMenu;