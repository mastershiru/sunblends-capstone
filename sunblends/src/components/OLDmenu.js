// import React from "react";
// import imagebg from "../assets/images/menu-bg.png";
// import dish1 from "../assets/images/dish/dish1.png";
// import dish2 from "../assets/images/dish/dish2.png";
// import dish3 from "../assets/images/dish/dish3.png";
// import dish4 from "../assets/images/dish/dish4.png";
// import dish5 from "../assets/images/dish/dish5.png";
// import dish6 from "../assets/images/dish/dish6.png";
// import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
// import { faPlus } from "@fortawesome/free-solid-svg-icons";
// import titleshape from "../assets/images/title-shape.svg";

// const MenuSection = ({ addToCartNumber, addToCart }) => {
//   return (
//     <section
//       style={{ backgroundImage: `url(${imagebg})` }}
//       className="our-menu section bg-light repeat-img"
//       id="menu"
//     >
//       <div className="sec-wp">
//         <div className="container">
//           <div className="row">
//             <div className="col-lg-12">
//               <div className="sec-title text-center mb-5">
//                 <p className="sec-sub-title mb-3">our menu</p>
//                 <h2 className="h2-title">
//                   wake up early, <span>eat fresh & healthy</span>
//                 </h2>
//                 <div className="sec-title-shape mb-4">
//                   <img src={titleshape} alt="" />
//                 </div>
//               </div>
//             </div>
//           </div>
//           <div className="best-selling-tab-wp">
//             <div className="row">
//               <div className="col-lg-12 m-auto">
//                 <div className="best-selling-tab text-center">
//                   <img src="assets/images/menu-1.png" alt="" />
//                   BEST SELLING
//                 </div>
//               </div>
//             </div>
//           </div>

//           <div className="menu-list-row">
//             <div className="row g-xxl-5 bydefault_show" id="menu-dish">
//               {[
//                 {
//                   img: dish1,
//                   rating: 5,
//                   title: "Breakfast egg & bacon",
//                   calories: "120 calories",
//                   type: "Non Veg",
//                   persons: 2,
//                   price: 99,
//                 },
//                 {
//                   img: dish2,
//                   rating: 4.3,
//                   title: "Fish & Chips",
//                   calories: "100 calories",
//                   type: "Fish",
//                   persons: 1,
//                   price: 359,
//                 },
//                 {
//                   img: dish3,
//                   rating: 4,
//                   title: "Spaghetti w/ Feta Cheese",
//                   calories: "161.88 calories",
//                   type: "Pasta",
//                   persons: 1,
//                   price: 399,
//                 },
//                 {
//                   img: dish4,
//                   rating: 4.5,
//                   title: "Ramen",
//                   calories: "436 calories",
//                   type: "Noodles",
//                   persons: 1,
//                   price: 379,
//                 },
//                 {
//                   img: dish5,
//                   rating: 5,
//                   title: "Ground Beef Kebabs",
//                   calories: "322.3 calories",
//                   type: "Meat",
//                   persons: 1,
//                   price: 99,
//                 },
//                 {
//                   img: dish6,
//                   rating: 5,
//                   title: "Tomato Basil Penne Pasta",
//                   calories: "502 calories",
//                   type: "Pasta",
//                   persons: 1,
//                   price: 159,
//                 },
//               ].map((dish, index) => (
//                 <div
//                   key={index}
//                   className={`col-lg-4 col-sm-6 dish-box-wp ${
//                     dish.type === "Lunch" ? "lunch" : ""
//                   }`}
//                   data-cat={dish.type.toLowerCase()}
//                 >
//                   <div className="dish-box text-center">
//                     <div className="dist-img">
//                       <img src={dish.img} alt="" />
//                     </div>
//                     <div className="dish-rating">
//                       {dish.rating}
//                       <i className="uil uil-star"></i>
//                     </div>
//                     <div className="dish-title">
//                       <h3 className="h3-title">{dish.title}</h3>
//                       <p>{dish.calories}</p>
//                     </div>
//                     <div className="dish-info">
//                       <ul>
//                         <li>
//                           <p>Type</p>
//                           <b>{dish.type}</b>
//                         </li>
//                         <li>
//                           <p>Persons</p>
//                           <b>{dish.persons}</b>
//                         </li>
//                       </ul>
//                     </div>
//                     <div className="dist-bottom-row">
//                       <ul>
//                         <li>
//                           <b>₱{dish.price}</b>
//                         </li>
//                         <li>
//                           <button
//                             className="dish-add-btn"
//                             onClick={() => {
//                               addToCart({
//                                 img: dish.img,
//                                 title: dish.title,
//                                 price: dish.price,
//                               });
//                               addToCartNumber();
//                             }}
//                           >
//                             <FontAwesomeIcon icon={faPlus} />
//                           </button>
//                         </li>
//                       </ul>
//                     </div>
//                   </div>
//                 </div>
//               ))}
//             </div>
//           </div>
//         </div>
//       </div>
//     </section>
//   );
// };

// export default MenuSection;

// import React, { useEffect, useState } from "react";
// import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
// import { faPlus } from "@fortawesome/free-solid-svg-icons";
// import axios from "axios";
// import titleshape from "../assets/images/title-shape.svg";
// import imagebg from "../assets/images/menu-bg.png";

// const MenuSection = ({ addToCartNumber, addToCart }) => {
//   const [menuItems, setMenuItems] = useState([]); // State to store menu items

//   // Fetch menu items from the backend when the component mounts
//   useEffect(() => {
//     axios
//       .get("http://localhost:8081/getMenu") // Endpoint to fetch menu items
//       .then((response) => {
//         setMenuItems(response.data); // Set the fetched data to state
//       })
//       .catch((error) => {
//         console.error("Error fetching menu:", error);
//       });
//   }, []);

//   return (
//     <section
//       className="our-menu section bg-light repeat-img"
//       id="menu"
//       style={{ backgroundImage: `url(${imagebg})` }}
//     >
//       <div className="sec-wp">
//         <div className="container">
//           <div className="row">
//             <div className="col-lg-12">
//               <div className="sec-title text-center mb-5">
//                 <p className="sec-sub-title mb-3">our menu</p>
//                 <h2 className="h2-title">
//                   wake up early, <span>eat fresh & healthy</span>
//                 </h2>
//                 <div className="sec-title-shape mb-4">
//                   <img src={titleshape} alt="" />
//                 </div>
//               </div>
//             </div>
//           </div>

//           <div className="best-selling-tab-wp">
//             <div className="row">
//               <div className="col-lg-12 m-auto">
//                 <div className="best-selling-tab text-center">
//                   <img src="assets/images/menu-1.png" alt="" />
//                   BEST SELLING
//                 </div>
//               </div>
//             </div>
//           </div>

//           <div className="menu-list-row">
//             <div className="row g-xxl-5 bydefault_show" id="menu-dish">
//               {menuItems.map((dish, index) => (
//                 <div
//                   key={index}
//                   className={`col-lg-4 col-sm-6 dish-box-wp ${
//                     dish.Dish_Type === "Lunch" ? "lunch" : ""
//                   }`}
//                   data-cat={dish.Dish_Type.toLowerCase()}
//                 >
//                   <div className="dish-box text-center">
//                     <div className="dist-img">
//                       <img
//                         src={`http://localhost:8081/${dish.Dish_Img}`} // Update with image path from server
//                         alt={dish.Dish_Title}
//                       />
//                     </div>
//                     <div className="dish-rating">
//                       {dish.Dish_Rating}
//                       <i className="uil uil-star"></i>
//                     </div>
//                     <div className="dish-title">
//                       <h3 className="h3-title">{dish.Dish_Title}</h3>
//                       <p>{dish.Dish_Price} calories</p>
//                     </div>
//                     <div className="dish-info">
//                       <ul>
//                         <li>
//                           <p>Type</p>
//                           <b>{dish.Dish_Type}</b>
//                         </li>
//                         <li>
//                           <p>Persons</p>
//                           <b>{dish.Dish_Persons}</b>
//                         </li>
//                       </ul>
//                     </div>
//                     <div className="dist-bottom-row">
//                       <ul>
//                         <li>
//                           <b>₱{dish.Dish_Price}</b>
//                         </li>
//                         <li>
//                           <button
//                             className="dish-add-btn"
//                             onClick={() => {
//                               addToCart({
//                                 img: dish.Dish_Img,
//                                 title: dish.Dish_Title,
//                                 price: dish.Dish_Price,
//                               });
//                               addToCartNumber();
//                             }}
//                           >
//                             <FontAwesomeIcon icon={faPlus} />
//                           </button>
//                         </li>
//                       </ul>
//                     </div>
//                   </div>
//                 </div>
//               ))}
//             </div>
//           </div>
//         </div>
//       </div>
//     </section>
//   );
// };

// export default MenuSection;

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
        withCredentials: true 
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
    setAddingToCart(prev => ({ ...prev, [dish.id]: true }));
    
    const email = localStorage.getItem("email");
    
    try {
      if (isLoggedIn && email) {
        // For logged-in users, add to server-side cart
        await axios.post(
          "http://127.0.0.1:8000/api/addToCart", 
          {
            email: email,
            dish_id: dish.id,
            quantity: 1
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
        quantity: 1
      });
      
      addToCartNumber();
      
    } catch (error) {
      console.error("Error adding item to cart:", error);
      alert("Failed to add item to cart. Please try again.");
    } finally {
      // Clear loading state for this dish
      setAddingToCart(prev => ({ ...prev, [dish.id]: false }));
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
                          e.target.src = 'assets/images/placeholder-food.jpg';
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
                        <li>
                          <p>Persons</p>
                          <b>{dish.Dish_Persons || 1}</b>
                        </li>
                      </ul>
                    </div>
                    <div className="dist-bottom-row">
                      <ul>
                        <li>
                          <b>₱{typeof dish.Dish_Price === 'number' ? dish.Dish_Price.toFixed(2) : dish.Dish_Price}</b>
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
            <button 
              className="btn btn-lg btn-primary" 
              onClick={() => navigate('/allMenu')}
            >
              See more
            </button>
          </div>
        </div>
      </div>
    </section>
  );
};

export default MenuSection;