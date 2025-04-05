import React, { useState, useEffect } from "react";
import axios from "axios";
import {
  faPlus,
  faSearch,
  faSpinner,
  faStar,
  faFireFlameCurved,
  faAward
} from "@fortawesome/free-solid-svg-icons";
import imagebg from "../assets/images/menu-bg.png";
import titleshape from "../assets/images/title-shape.svg";
import Navbar from "./Navbar";
import { useLocation } from "react-router-dom";
import NotificationModal from "./modal-compinents/notification-modal";
import NotificationsCenter from "./modal-compinents/notification-center";
import NotificationManager from "./notifications/Notification-manager";
import { useNavbar } from "../context/NavbarContext";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { useNavigate } from "react-router-dom";
import { ToastContainer, toast } from "react-toastify";

const AllMenu = () => {
  const [menuItems, setMenuItems] = useState([]);
  const [addingToCart, setAddingToCart] = useState({});
  const [searchTerm, setSearchTerm] = useState("");
  const [categories, setCategories] = useState([]);
  const [selectedCategory, setSelectedCategory] = useState("");
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

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
    toggleNotificationCenter,
  } = useNavbar();

  // API URL
  const API_BASE_URL = "http://127.0.0.1:8000/api";

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
      setLoading(true);
      try {
        // First try the advanced menu endpoint to get ratings data
        const response = await axios.get(`${API_BASE_URL}/advanced-menu`, { 
          withCredentials: true 
        });
        
        if (response.data.success) {
          console.log("Advanced menu data loaded successfully:", response.data);
          // Combine all menu items into one array for display
          const allItems = [
            ...(response.data.featured || []),
            ...(response.data.most_ordered || []),
            ...(response.data.highest_rated || []),
            ...(response.data.regular || [])
          ];
          
          // Remove duplicates by ID
          const uniqueItems = Array.from(
            new Map(allItems.map(item => [item.id, item])).values()
          );
          
          // Filter for available items
          const availableItems = uniqueItems.filter(
            dish => dish.isAvailable !== 0 && dish.isAvailable !== false
          );
          
          setMenuItems(availableItems);
          
          // Extract unique categories
          setCategories([
            ...new Set(availableItems.map(dish => dish.Dish_Type)),
          ]);
        } else {
          // Fallback to regular menu endpoint
          console.log("Advanced menu failed, trying fallback...");
          const fallbackResponse = await axios.get(`${API_BASE_URL}/menu-items`, { 
            withCredentials: true 
          });
          
          const availableItems = fallbackResponse.data.filter(
            dish => dish.isAvailable !== 0 && dish.isAvailable !== false
          );
          
          setMenuItems(availableItems);
          setCategories([
            ...new Set(availableItems.map(dish => dish.Dish_Type)),
          ]);
        }
      } catch (error) {
        console.error("Error fetching menu:", error);
        setError("Failed to load menu items. Please try again later.");
      } finally {
        setLoading(false);
      }
    };

    fetchMenuItems();
  }, [API_BASE_URL]);

  // Filter menu items
  const filteredMenu = menuItems.filter(
    dish =>
      (dish.Dish_Type?.toLowerCase().includes(searchTerm.toLowerCase()) ||
        dish.Dish_Title?.toLowerCase().includes(searchTerm.toLowerCase())) &&
      (selectedCategory === "" || dish.Dish_Type === selectedCategory)
  );

  // Add to cart handler using context function
  const handleAddToCart = async dish => {
    // Set the specific dish as loading
    setAddingToCart(prev => ({ ...prev, [dish.id]: true }));
  
    const email = localStorage.getItem("email");
  
    try {
      if (isLoggedIn && email) {
        // For logged-in users, add to server-side cart
        await axios.post(
          `${API_BASE_URL}/addToCart`,
          {
            email: email,
            dish_id: dish.id,
            quantity: 1,
          },
          { withCredentials: true }
        );
      }
  
      // Always update local UI cart
      addToCart({
        id: dish.id,
        img: dish.Dish_Img,
        title: dish.Dish_Title,
        price: dish.Dish_Price,
        quantity: 1,
      });
  
      addToCartNumber();
      toast.success(`${dish.Dish_Title} added to cart!`);
    } catch (error) {
      console.error("Error adding item to cart:", error);
      alert("Failed to add item to cart. Please try again.");
    } finally {
      // Clear loading state for this dish
      setAddingToCart(prev => ({ ...prev, [dish.id]: false }));
    }
  };

  return (
    <>
      <ToastContainer
        position="top-right"
        autoClose={2000}
        closeButton={false}
        hideProgressBar={false}
        newestOnTop={false}
        pauseOnFocusLoss
        draggable
        pauseOnHover
        style={{ marginTop: "60px" }}
      />
      <div>
        <NotificationManager />

        {/* Removed NotificationModal and NotificationsCenter - these are handled by Navbar */}

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
                  <div className="sec-title text-center">
                    <p className="sec-sub-title mb-3">our menu</p>
                    <h2 className="h2-title">
                      discover our <span>delicious options</span>
                    </h2>
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
                    placeholder="Search dishes or categories"
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

              {loading && (
                <div className="text-center py-5">
                  <FontAwesomeIcon icon={faSpinner} spin size="2x" />
                  <p className="mt-3">Loading menu items...</p>
                </div>
              )}

              {error && (
                <div className="text-center py-5 text-danger">
                  <p>{error}</p>
                  <button
                    className="btn btn-outline-primary mt-3"
                    onClick={() => window.location.reload()}
                  >
                    Try Again
                  </button>
                </div>
              )}

              {!loading && !error && (
                <div className="menu-list-row">
                  <div className="row g-xxl-5 bydefault_show" id="menu-dish">
                    {filteredMenu.length > 0 ? (
                      filteredMenu.map((dish) => (
                        <div
                          key={dish.id}
                          className={`col-lg-4 col-sm-6 dish-box-wp ${
                            dish.Dish_Type?.toLowerCase() || "other"
                          }`}
                          data-cat={dish.Dish_Type?.toLowerCase() || "other"}
                        >
                          <div className="dish-box text-center">
                            {/* Feature badges */}
                            {(dish.feature_type === "most_ordered" ||
                              (dish.feature_type &&
                                dish.feature_type.type === "most_ordered")) && (
                              <div className="feature-badge most-ordered">
                                <FontAwesomeIcon icon={faFireFlameCurved} />{" "}
                                Popular
                              </div>
                            )}
                            {(dish.feature_type === "highest_rated" ||
                              (dish.feature_type &&
                                dish.feature_type.type ===
                                  "highest_rated")) && (
                              <div className="feature-badge highest-rated">
                                <FontAwesomeIcon icon={faAward} /> Top Rated
                              </div>
                            )}

                            <div className="dist-img">
                              <img
                                src={dish.Dish_Img}
                                alt={dish.Dish_Title}
                                onError={(e) => {
                                  e.target.onerror = null;
                                  e.target.src =
                                    "assets/images/placeholder-food.jpg";
                                }}
                              />
                            </div>

                            {/* Enhanced rating display */}
                            <div className="dish-rating">
                              {parseFloat(dish.Dish_Rating) > 0 ? (
                                <>
                                  <span>
                                    {parseFloat(dish.Dish_Rating).toFixed(1)}
                                  </span>
                                  {[1, 2, 3, 4, 5].map((star, index) => (
                                    <FontAwesomeIcon
                                      key={index}
                                      icon={faStar}
                                      style={{
                                        color:
                                          star <= Math.round(dish.Dish_Rating)
                                            ? "#ffd700"
                                            : "#ccc",
                                        marginLeft: "2px",
                                        fontSize: "0.8em",
                                      }}
                                    />
                                  ))}
                                  <small>({dish.ratings_count || 0})</small>
                                </>
                              ) : (
                                <span
                                  style={{ fontSize: "0.8em", color: "#999" }}
                                >
                                  No ratings
                                </span>
                              )}
                            </div>

                            <div className="dish-title">
                              <h3 className="h3-title">{dish.Dish_Title}</h3>
                              <p>{dish.Dish_Type}</p>
                            </div>

                            <div className="dish-info">
                              {/* <ul>
                              <li>
                                <p>Type</p>
                                <b>{dish.Dish_Type}</b>
                              </li>
                              <li className="person">
                                <p>Persons</p>
                                <b>{dish.Dish_Persons || 1}</b>
                              </li>
                            </ul> */}
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
                      ))
                    ) : (
                      <div className="col-12 text-center py-4">
                        <p>No menu items match your search criteria.</p>
                        {searchTerm && (
                          <button
                            className="btn btn-sm btn-outline-secondary mt-2"
                            onClick={() => {
                              setSearchTerm("");
                              setSelectedCategory("");
                            }}
                          >
                            Clear Search
                          </button>
                        )}
                      </div>
                    )}
                  </div>
                </div>
              )}
            </div>
          </div>
        </section>
      </div>
    </>
  );
};

export default AllMenu;