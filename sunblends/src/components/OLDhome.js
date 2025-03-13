import React, { useState, useEffect, useRef } from "react";
import Coffee from "../assets/images/coffee.jpg";
import Logo from "../assets/images/logo.png";
import Login from "./modal-compinents/login";
import About from "./about";
import TblReservation from "./table-reservation";
import Register from "./modal-compinents/register";
import Cart from "./modal-compinents/cart";
import Menu from "./menu";
import Promo from "./promo";
import Footer from "./footer";
import ScroolBacktoTop from "./back.to.Top.btn";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import {
  faMagnifyingGlass,
  faCartShopping,
  faCircleUser,
  faBars,
  faPen,
} from "@fortawesome/free-solid-svg-icons";
import Orders from "./modal-compinents/history-orders";
import EditProfile from "./modal-compinents/edit-profile";

export default function Home() {
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const [isDropdownOpen, setIsDropdownOpen] = useState(false);
  const dropdownRef = useRef(null);
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [userData, setUserData] = useState(null);

  //navbar menu
  const [isOpen, setIsOpen] = useState(false);

  const toggleNavbar = () => {
    setIsOpen((prevState) => !prevState);
  };

  // Check if the user is already logged in when the component mounts
  useEffect(() => {
    const storedUserData = localStorage.getItem("userData");
    const storedIsLoggedIn = localStorage.getItem("isLoggedIn");

    if (storedIsLoggedIn === "true") {
      setIsLoggedIn(true);
      setUserData(JSON.parse(storedUserData));
    }
  }, []);

  // Store user data and login state to localStorage on login
  useEffect(() => {
    if (isLoggedIn && userData) {
      localStorage.setItem("isLoggedIn", "true");
      localStorage.setItem("userData", JSON.stringify(userData));
    }
  }, [isLoggedIn, userData]);

  //dropdown
  const toggleDropdown = () => {
    setIsDropdownOpen((prev) => !prev);
  };
  //click outside ng dropdown mag cclose
  const handleClickOutside = (event) => {
    if (dropdownRef.current && !dropdownRef.current.contains(event.target)) {
      setIsDropdownOpen(false);
    }
  };

  useEffect(() => {
    document.addEventListener("mousedown", handleClickOutside);
    return () => {
      document.removeEventListener("mousedown", handleClickOutside);
    };
  }, []);

  //Login
  const [isOpenLogin, setIsOpenLogin] = useState(false);
  const toggleModalLogin = () => {
    setIsOpenLogin(!isOpenLogin);
  };
  //Register
  const [isOpenRegister, setIsOpenRegister] = useState(false);
  const toggleModalRegister = () => {
    setIsOpenRegister(!isOpenRegister);
  };
  //Edit Profile
  const [isOpenEditProfile, setIsOpenEditProfile] = useState(false);

  //Cart
  const [cartNumber, setCartNumber] = useState(0);
  const [isOpenCart, setIsOpenCart] = useState(false);
  const toggleModalCart = () => {
    if (cartNumber === -1) {
      alert("Your cart is empty.");
    } else {
      setIsOpenCart(!isOpenCart);
    }
  };
  const addToCartNumber = () => {
    setCartNumber((prev) => prev + 1);
  };

  const [cartItems, setCartItems] = useState([]);

  const addToCart = (dish) => {
    setCartItems((prev) => {
      // Check if the dish already exists in the cart
      const existingItem = prev.find((item) => item.title === dish.title);

      if (existingItem) {
        // If it exists, increase the quantity
        return prev.map((item) =>
          item.title === dish.title
            ? { ...item, quantity: item.quantity + 1 }
            : item
        );
      } else {
        // If it doesn't exist, add the dish with a quantity of 1
        return [...prev, { ...dish, quantity: 1 }];
      }
    });
  };

  //checkout
  const [isOpenCheckout, setIsOpenCheckout] = useState(false);
  const toggleModalCheckout = (state) => setIsOpenCheckout(state);

  //Orders
  const [isOpenOrders, setIsOpenOrders] = useState(false);
  const toggleModalOrders = () => {
    setIsOpenOrders(!isOpenOrders);
  };

  //logout
  const handleLogout = () => {
    const confirmLogout = window.confirm("Are you sure you want to log out?");
    if (confirmLogout) {
      setIsLoggedIn(false);
      setEmail("");
      setPassword("");
      setUserData(null);
      localStorage.removeItem("isLoggedIn");
      localStorage.removeItem("userData");
      alert("You have been logged out.");
      toggleModalLogin(true);
    }
  };

  return (
    <>
      <ScroolBacktoTop />
      <nav className="scale-in-ver-top">
        <a href="#home">
          <img className="logo" src={Logo} alt="Sunblends Logo" />
        </a>

        <div style={{ display: "flex" }}>
          <ul id="navbar">
            <li>
              <a href="#menu">Menu</a>
            </li>
            <li>
              <a href="#about">About</a>
            </li>
            <li>
              <a href="#contact">Contact</a>
            </li>

            <li className="for-mobile">
              <button style={{ display: "none" }} id="mobile-account-button">
                Account
              </button>
            </li>
            <li className="for-mobile">
              <button id="mobile-show-login">Login</button>
            </li>

            <form action="#" className="header-search-form for-des">
              <input
                type="search"
                className="form-input"
                placeholder="Search Here..."
              />
              <button type="submit" style={{ color: "black" }}>
                <i>
                  <FontAwesomeIcon icon={faMagnifyingGlass} />
                </i>
              </button>
            </form>

            <button
              className="header-btn header-cart"
              id="show-cart"
              onClick={toggleModalCart}
              style={{ border: "none", color: "#6E6E6E" }}
            >
              <i>
                <FontAwesomeIcon icon={faCartShopping} />
              </i>
              <span className="cart-number">{cartNumber}</span>
            </button>
          </ul>

          <div ref={dropdownRef} id="user" onClick={toggleDropdown}>
            <i className="header-btn" style={{ boxShadow: "none" }}>
              {isLoggedIn && userData && userData.Customer_Img ? (
                <img
                src={
                  userData?.Customer_Img
                    ? userData.Customer_Img.startsWith("http")
                      ? userData.Customer_Img // Use external Google profile image
                      : `http://127.0.0.1:8000/storage/${userData.Customer_Img}` // Local image from Laravel
                    : "/default-profile.png" // Fallback image if empty
                }

                  alt="Profile"
                  className="profile-img"
                  style={{
                    width: "50px",
                    height: "40px",
                    borderRadius: "50%",
                  }}
                />
              ) : (
                <FontAwesomeIcon
                  icon={faCircleUser}
                  style={{ height: "38px", color: "#6E6E6E" }}
                />
              )}
            </i>

            {isDropdownOpen && (
              <div
                className={`dropdown-content ${
                  isDropdownOpen ? "dropdown-open" : ""
                }`}
              >
                {isLoggedIn ? (
                  <>
                    <div
                      className="profile "
                      style={{
                        display: "flex",
                        justifyContent: "center",
                        alignItems: "center",
                      }}
                    >
                      {userData && userData.Customer_Img ? (
                        <img
                          // src={`http://localhost:8081/${userData.Customer_Img}`}
                          src={
                            userData?.Customer_Img
                              ? userData.Customer_Img.startsWith("http")
                                ? userData.Customer_Img // Use external Google profile image
                                : `http://127.0.0.1:8000/storage/${userData.Customer_Img}` // Local image from Laravel
                              : "/default-profile.png" // Fallback image if empty
                          }
                          alt="Profile"
                          className="profile-img "
                          style={{
                            height: "50px",
                            width: "50px",
                            borderRadius: "50%",
                          }}
                        />
                      ) : (
                        // <span className="cart-number">{cartNumber}</span>
                        <FontAwesomeIcon
                          icon={faCircleUser}
                          style={{
                            height: "50px",
                            width: "50px",
                            borderRadius: "50%",
                            color: "#6E6E6E",
                          }}
                        />
                      )}
                      <FontAwesomeIcon
                        icon={faPen} // FontAwesome pencil icon
                        className="pen-icon"
                        onClick={() => setIsOpenEditProfile(true)} // Example onClick action
                      />
                    </div>
                    <p
                      className="user-name"
                      style={{
                        fontSize: "10px",
                        margin: "0",
                        textAlign: "center",
                        color: "#AAAAAA",
                      }}
                    >
                      {/* customer_name database */}
                      {userData ? userData.Customer_Name : "Loading..."}
                    </p>{" "}
                    <button onClick={toggleModalOrders}>Orders</button>
                    <button id="logout-button" onClick={handleLogout}>
                      Logout
                    </button>
                  </>
                ) : (
                  <>
                    <button id="show-login" onClick={toggleModalLogin}>
                      Login
                    </button>
                    <button id="show-register" onClick={toggleModalRegister}>
                      Register
                    </button>
                  </>
                )}
              </div>
            )}
          </div>

          <div id="mobile">
            {/* <button className="bar-icon">
              <FontAwesomeIcon icon={faEllipsisVertical} />
              <FontAwesomeIcon className="icon" icon={faBars} />
            </button> */}
            <button
              className="navbar-toggler bar-icon"
              onClick={toggleNavbar}
              aria-controls="navbarContent"
              aria-expanded={isOpen}
              aria-label="Toggle navigation"
            >
              <span className="navbar-toggler-icon">
                <FontAwesomeIcon className="icon" icon={faBars} />
              </span>
            </button>
          </div>
        </div>
        {/* //menu navbar  */}
        <div
          className={`navbar-collapse collapse ${isOpen ? "show" : ""}`}
          id="navbarContent"
        >
          <ul className="navbar-nav ml-lg-4 pt-3 pt-lg-0">
            <div className="row" style={{ textAlign: "center" }}>
              <div className="col-auto" style={{ padding: "0" }}>
                <i style={{ boxShadow: "none" }}>
                  {isLoggedIn && userData && userData.Customer_Img ? (
                    <img
                    src={
                      userData?.Customer_Img
                        ? userData.Customer_Img.startsWith("http")
                          ? userData.Customer_Img // Use external Google profile image
                          : `http://127.0.0.1:8000/storage/${userData.Customer_Img}` // Local image from Laravel
                        : "/default-profile.png" // Fallback image if empty
                    }
                      alt="Profile"
                      className="profile-img"
                      style={{
                        width: "50px",
                        borderRadius: "50%",
                      }}
                    />
                  ) : (
                    <FontAwesomeIcon
                      icon={faCircleUser}
                      style={{ height: "38px", color: "#6E6E6E" }}
                    />
                  )}
                </i>
              </div>
              <div className="col-auto" style={{
                    textAlign: "center",
                    marginTop: "10px",
                  }}>
                <span
                  className="user-name"
                  style={{
                    fontSize: "20px",
                    color: "#AAAAAA",
                  }}
                >
                  {/* customer_name database */}
                  {userData ? userData.Customer_Name : "Loading..."}
                </span>{" "}
                {/* <br />
                <span style={{ margin: "0", padding: "0", fontSize: "13px" }}>
                {userData ? userData.Customer_Email : "Loading..."}
                </span> */}
              </div>
              <div
                className="col"
                style={{ fontSize: "30px", textAlign: "right" }}
              >

                <button
                className=""
              id="show-cart"
              onClick={toggleModalCart}
              style={{ border: "none", color: "#6E6E6E", background: "transparent", textAlign: "right"}}
            >
              <i>
                <FontAwesomeIcon icon={faCartShopping} />
              </i>
              <span
          style={{
            position: "absolute",
            top: "100px",
            right: "55px",
            width: "22px",
            height: "22px",
            display: "flex",
            justifyContent: "center",
            alignItems: "center",
            borderRadius: "50%",
            fontSize: "12px",
            color: "#ff8243",
            textAlign: "center",
            fontWeight: "bold",
          }}
        >
          {cartNumber}
        </span>
            </button>
              </div>
            </div>
            <li className="nav-link">
              <a href="#menu">Menu</a>
            </li>
            <li className="nav-link">
              <a href="#about">About</a>
            </li>
            <li className="nav-link">
              <a href="#contact">Contact</a>
            </li>
            <button 
      id="auth-button" 
      onClick={isLoggedIn ? handleLogout : toggleModalLogin}
    >
      {isLoggedIn ? "Logout" : "Login"}
    </button>
          </ul>
        </div>
      </nav>

      <Login
        isOpenLogin={isOpenLogin}
        toggleModalLogin={toggleModalLogin}
        setIsLoggedIn={setIsLoggedIn}
        email={email}
        setEmail={setEmail}
        password={password}
        setPassword={setPassword}
        setUserData={setUserData}
        toggleModalRegister={toggleModalRegister}
      />
      <EditProfile
        isOpenEditProfile={isOpenEditProfile}
        setIsOpenEditProfile={setIsOpenEditProfile}
      />
      <Register
        isOpenRegister={isOpenRegister}
        toggleModalRegister={toggleModalRegister}
        toggleModalLogin={toggleModalLogin}
      />

      <Cart
        isOpenCart={isOpenCart}
        toggleModalCart={toggleModalCart}
        cartItems={cartItems}
        setCartItems={setCartItems}
        setCartNumber={setCartNumber}
        isLoggedIn={isLoggedIn} // Pass this prop
        toggleModalLogin={toggleModalLogin} // Use the actual function
        toggleModalCheckout={toggleModalCheckout} // Pass down the function
      />
      <Orders
        isOpenOrders={isOpenOrders}
        toggleModalOrders={toggleModalOrders}
      />

      <section className="main-banner" id="home">
        <div className="sec-wp">
          <div className="container">
            <div className="row">
              <div className="col-lg-6">
                <div className="banner-text">
                  <h1 className="h1-title">
                    SHE LOVED <span>TEA,</span> I AM COFFEE. WE BLEND.
                  </h1>
                  <p className="tagline">
                    We offer a wonderful range of uniquely delectable food and
                    beverages.
                  </p>
                  <div className="banner-btn mt-4">
                    <a href="#menu" className="sec-btn">
                      Order Now
                    </a>
                  </div>
                </div>
              </div>
              <div className="col-lg-6">
                <div className="banner-img-wp">
                  <div
                    className="banner-img"
                    style={{
                      backgroundImage: `url(${Coffee})`,
                    }}
                  ></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <Menu addToCartNumber={addToCartNumber} addToCart={addToCart} />
      <Promo />

      <About />
      <TblReservation />
      <Footer />
    </>
  );
}
