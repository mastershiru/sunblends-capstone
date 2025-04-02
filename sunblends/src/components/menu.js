import React, { useEffect, useState } from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faPlus, faSpinner, faStar, faFireFlameCurved, faAward } from "@fortawesome/free-solid-svg-icons";
import axios from "axios";
import titleshape from "../assets/images/title-shape.svg";
import imagebg from "../assets/images/menu-bg.png";
import { useNavigate } from "react-router-dom";

const MenuSection = ({ addToCartNumber, addToCart }) => {
  const [menuItems, setMenuItems] = useState([]);
  const [featuredItems, setFeaturedItems] = useState([]);
  const [mostOrdered, setMostOrdered] = useState([]);
  const [highestRated, setHighestRated] = useState([]);
  const [regularItems, setRegularItems] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [addingToCart, setAddingToCart] = useState({});
  const navigate = useNavigate();
  const isLoggedIn = localStorage.getItem("email") !== null;
  const API_BASE_URL = "http://127.0.0.1:8000/api";

  // Fetch menu items from the backend
  useEffect(() => {
    setLoading(true);
    
    // Add a force refresh parameter if needed
    const forceRefresh = new URLSearchParams(window.location.search).get('refresh') === 'true';
    
    // Use the advanced menu endpoint from MenuApiController
    axios
      .get(`${API_BASE_URL}/advanced-menu${forceRefresh ? '?refresh=true' : ''}`, {
        withCredentials: true,
      })
      .then((response) => {
        if (response.data.success) {
          console.log("Menu data loaded successfully:", response.data);
          setMenuItems(response.data.menu_items || []);
          setFeaturedItems(response.data.featured || []);
          setMostOrdered(response.data.most_ordered || []);
          setHighestRated(response.data.highest_rated || []);
          setRegularItems(response.data.regular || []);
        } else {
          // Fallback to regular menu endpoint from MenuApiController
          console.log("Advanced menu failed, trying fallback...");
          return axios.get(`${API_BASE_URL}/menu-items`, {
            withCredentials: true,
          });
        }
      })
      .then((fallbackResponse) => {
        // Only process fallback if we got one
        if (fallbackResponse && fallbackResponse.data) {
          console.log("Loaded fallback menu items:", fallbackResponse.data);
          setMenuItems(fallbackResponse.data);
        }
        setLoading(false);
      })
      .catch((error) => {
        console.error("Error fetching menu:", error);
        
        // Try one more fallback
        console.log("Trying final fallback to index endpoint...");
        axios.get(`${API_BASE_URL}/menu`, { withCredentials: true })
          .then(finalResponse => {
            if (finalResponse && finalResponse.data) {
              console.log("Loaded from final fallback:", finalResponse.data);
              setMenuItems(finalResponse.data);
              setLoading(false);
            } else {
              setError("Failed to load menu items");
              setLoading(false);
            }
          })
          .catch(finalError => {
            console.error("All menu endpoints failed:", finalError);
            setError("Failed to load menu items");
            setLoading(false);
          });
      });
  }, []);

  // Handle adding item to cart
  const handleAddToCart = async (dish) => {
    // Set the specific dish as loading
    setAddingToCart((prev) => ({ ...prev, [dish.id]: true }));

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
    } catch (error) {
      console.error("Error adding item to cart:", error);
      alert("Failed to add item to cart. Please try again.");
    } finally {
      // Clear loading state for this dish
      setAddingToCart((prev) => ({ ...prev, [dish.id]: false }));
    }
  };

  // Render a dish card
  const renderDishCard = (dish) => (
    <div
      key={dish.id}
      className={`col-lg-4 col-sm-6 dish-box-wp ${dish.Dish_Type?.toLowerCase() || 'other'}`}
      data-cat={dish.Dish_Type?.toLowerCase() || 'other'}
    >
      <div className="dish-box text-center">
        {/* Feature badge */}
        {(dish.feature_type === 'most_ordered' || (dish.feature_type && dish.feature_type.type === 'most_ordered')) && (
          <div className="feature-badge most-ordered">
            <FontAwesomeIcon icon={faFireFlameCurved} /> Popular
          </div>
        )}
        {(dish.feature_type === 'highest_rated' || (dish.feature_type && dish.feature_type.type === 'highest_rated')) && (
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
              e.target.src = "assets/images/placeholder-food.jpg";
            }}
          />
        </div>
        
        <div className="dish-rating">
          {parseFloat(dish.Dish_Rating) > 0 ? (
            <>
              <span>{parseFloat(dish.Dish_Rating).toFixed(1)}</span>
              {[1, 2, 3, 4, 5].map((star, index) => (
                <FontAwesomeIcon 
                  key={index}
                  icon={faStar} 
                  style={{
                    color: star <= Math.round(dish.Dish_Rating) ? "#ffd700" : "#ccc",
                    marginLeft: "2px",
                    fontSize: "0.8em"
                  }}
                />
              ))}
              <small>({dish.ratings_count || 0})</small>
            </>
          ) : (
            <span style={{ fontSize: "0.8em", color: "#999" }}>No ratings</span>
          )}
        </div>
        
        <div className="dish-title">
          <h3 className="h3-title">{dish.Dish_Title}</h3>
          <p>{dish.Dish_Type}</p>
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
  );

  if (loading) {
    return (
      <div className="text-center py-5">
        <FontAwesomeIcon icon={faSpinner} spin size="2x" />
        <p className="mt-3">Loading menu items...</p>
      </div>
    );
  }

  if (error) {
    return (
      <div className="text-center py-5 text-danger">
        <p>{error}</p>
        <button
          className="btn btn-outline-primary mt-3"
          onClick={() => window.location.reload()}
        >
          Try Again
        </button>
        <div className="text-center mt-4">
          <button className="more-menu" onClick={() => navigate("/allMenu")}>
            View All Menu
          </button>
        </div>
      </div>
    );
  }

  // If no featured items are available, use regular items
  const fallbackItems = regularItems.length > 0 
    ? regularItems 
    : menuItems.slice(0, 6);

  // Use up to 3 dishes from each category, or more if one category is lacking
  const limitedMostOrdered = mostOrdered.slice(0, 3);
  const limitedHighestRated = highestRated.slice(0, 3);
  
  // Create combined display items only if we have enough in both categories
  const hasBothCategories = limitedMostOrdered.length > 0 && limitedHighestRated.length > 0;
  
  // Decide which items to display in the main grid
  let displayItems = [];
  if (hasBothCategories) {
    // If we have both, show separate sections
    displayItems = []; // Empty, because we'll show separate sections
  } else if (featuredItems.length > 0) {
    // If only one category has items, use the featured combined list
    displayItems = featuredItems.slice(0, 6);
  } else {
    // Last resort - use regular dishes
    displayItems = fallbackItems.slice(0, 6);
  }

  // Fill in with regular items if we don't have enough featured items
  if (displayItems.length < 6 && regularItems.length > 0) {
    const neededItems = 6 - displayItems.length;
    const additionalItems = regularItems.slice(0, neededItems);
    displayItems = [...displayItems, ...additionalItems];
  }

  return (
    <section
      className="our-menu section bg-light repeat-img"
      id="menu"
      style={{ backgroundImage: `url(${imagebg})` }}
    >
      <div className="sec-wp">
        <div className="container">
          <div className="row">
            <div className="col-lg-12">
              <div className="sec-title text-center mb-5">
                <p className="sec-sub-title mb-3">our menu</p>
                <h2 className="h2-title">
                  wake up early, <span>eat fresh & healthy</span>
                </h2>
                <div className="sec-title-shape mb-4">
                  <img src={titleshape} alt="" />
                </div>
              </div>
            </div>
          </div>

          {/* TWO SEPARATE SECTIONS APPROACH */}
          {hasBothCategories ? (
            <>
              {/* MOST ORDERED SECTION */}
              {limitedMostOrdered.length > 0 && (
                <>
                  <div className="best-selling-tab-wp">
                    <div className="row">
                      <div className="col-lg-12 m-auto">
                        <div className="best-selling-tab text-center">
                          <FontAwesomeIcon icon={faFireFlameCurved} style={{ color: "#ff8243", marginRight: "10px" }} />
                          MOST ORDERED
                        </div>
                      </div>
                    </div>
                  </div>

                  <div className="menu-list-row">
                    <div className="row g-xxl-5 bydefault_show" id="most-ordered-dishes">
                      {limitedMostOrdered.map(dish => renderDishCard(dish))}
                    </div>
                  </div>
                </>
              )}

              {/* HIGHEST RATED SECTION */}
              {limitedHighestRated.length > 0 && (
                <>
                  <div className="best-selling-tab-wp mt-5">
                    <div className="row">
                      <div className="col-lg-12 m-auto">
                        <div className="best-selling-tab text-center highest-rated-tab">
                          <FontAwesomeIcon icon={faAward} style={{ color: "#4a6ac8", marginRight: "10px" }} />
                          TOP RATED
                        </div>
                      </div>
                    </div>
                  </div>

                  <div className="menu-list-row">
                    <div className="row g-xxl-5 bydefault_show" id="highest-rated-dishes">
                      {limitedHighestRated.map(dish => renderDishCard(dish))}
                    </div>
                  </div>
                </>
              )}
            </>
          ) : (
            /* SINGLE COMBINED SECTION APPROACH (when we don't have enough of both categories) */
            <>
              {(featuredItems.length > 0 || fallbackItems.length > 0) && (
                <>
                  <div className="best-selling-tab-wp">
                    <div className="row">
                      <div className="col-lg-12 m-auto">
                        <div className="best-selling-tab text-center">
                          <FontAwesomeIcon icon={faFireFlameCurved} style={{ color: "#ff8243", marginRight: "10px" }} />
                          FEATURED DISHES
                        </div>
                      </div>
                    </div>
                  </div>

                  <div className="menu-list-row">
                    <div className="row g-xxl-5 bydefault_show" id="menu-dish">
                      {displayItems.map(dish => renderDishCard(dish))}
                    </div>
                  </div>
                </>
              )}
            </>
          )}

          {/* No items available message */}
          {((hasBothCategories && limitedMostOrdered.length === 0 && limitedHighestRated.length === 0) 
            || (!hasBothCategories && displayItems.length === 0)) && (
            <div className="col-12 text-center py-4">
              <p>No menu items available right now. Check back soon!</p>
            </div>
          )}

          <div className="text-center mt-4">
            <button className="more-menu" onClick={() => navigate("/allMenu")}>
              View All Menu
            </button>
          </div>
        </div>
      </div>
      
      {/* Admin tools for refreshing ratings */}
      {localStorage.getItem('isAdmin') === 'true' && (
        <div className="admin-tools" style={{ position: 'fixed', bottom: '20px', right: '20px', zIndex: 1000 }}>
          <button 
            className="btn btn-sm btn-primary"
            onClick={() => {
              // Call the refresh API
              axios.get(`${API_BASE_URL}/refresh-all-dish-ratings`)
                .then(response => {
                  alert(`Ratings refreshed: ${response.data.message}`);
                  // Reload the page with refresh=true
                  window.location.href = `${window.location.pathname}?refresh=true`;
                })
                .catch(err => {
                  console.error("Error refreshing ratings:", err);
                  alert("Failed to refresh ratings");
                });
            }}
          >
            Refresh Ratings
          </button>
        </div>
      )}
      
      
    </section>
  );
};

export default MenuSection;