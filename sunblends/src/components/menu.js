import React, { useEffect, useState } from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faPlus, faSpinner } from "@fortawesome/free-solid-svg-icons";
import axios from "axios";
import titleshape from "../assets/images/title-shape.svg";
import imagebg from "../assets/images/menu-bg.png";
import { useNavigate } from "react-router-dom";

const MenuSection = ({ addToCartNumber, addToCart }) => {
  const [menuItems, setMenuItems] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [addingToCart, setAddingToCart] = useState({});
  const navigate = useNavigate();
  const isLoggedIn = localStorage.getItem("email") !== null;

  // Fetch menu items from the backend
  useEffect(() => {
    setLoading(true);
    axios
      .get("http://127.0.0.1:8000/api/menu-items", {
        withCredentials: true,
      })
      .then((response) => {
        setMenuItems(response.data);
        setLoading(false);
      })
      .catch((error) => {
        console.error("Error fetching menu:", error);
        setError("Failed to load menu items");
        setLoading(false);
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
          "http://127.0.0.1:8000/api/addToCart",
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
            More
          </button>
        </div>
      </div>
    );
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

          <div className="best-selling-tab-wp">
            <div className="row">
              <div className="col-lg-12 m-auto">
                <div className="best-selling-tab text-center">
                  <img src="assets/images/menu-1.png" alt="" />
                  BEST SELLING
                </div>
              </div>
            </div>
          </div>

          <div className="menu-list-row">
            <div className="row g-xxl-5 bydefault_show" id="menu-dish">
              {menuItems.map((dish) => (
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

          <div className="text-center mt-4">
            <button className="more-menu" onClick={() => navigate("/allMenu")}>
              More
            </button>
          </div>
        </div>
      </div>
    </section>
  );
};

export default MenuSection;
