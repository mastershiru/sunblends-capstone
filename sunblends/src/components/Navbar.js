// src/components/Navbar.js
import React from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import {
  faCartShopping,
  faCircleUser,
  faBars,
  faPen,
} from "@fortawesome/free-solid-svg-icons";
import Logo from "../assets/images/logo.png";
import { Link } from "react-router-dom";
import { useNavbar } from "../context/NavbarContext";

const Navbar = () => {
  const {
    isLoggedIn,
    userData,
    cartNumber,
    isDropdownOpen,
    dropdownRef,
    isOpen,
    hasNewNotification,
    notificationBadgeCount,
    toggleDropdown,
    toggleNavbar,
    toggleModalLogin,
    toggleModalRegister,
    toggleModalCart,
    toggleModalOrders,
    toggleNotificationCenter,
    handleLogout,
    setIsOpenEditProfile,
  } = useNavbar();

  return (
    <nav className="scale-in-ver-top">
      <Link to="/#home">
        <img className="logo" src={Logo} alt="Sunblends Logo" />
      </Link>

      <div style={{ display: "flex" }}>
        <ul id="navbar">
          <li>
            <Link to="/#menu">Menu</Link>
          </li>
          <li>
            <Link to="/#about">About</Link>
          </li>
          <li>
            <Link to="/#contact">Contact</Link>
          </li>
          <li>
            <Link to="/reservation">Reservation</Link>
          </li>

          <li className="for-mobile">
            <button style={{ display: "none" }} id="mobile-account-button">
              Account
            </button>
          </li>
          <li className="for-mobile">
            <button id="mobile-show-login">Login</button>
          </li>
          {/* cart icon */}
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
          <i
            className="header-btn"
            style={{ boxShadow: "none", position: "relative" }}
          >
            {/* Show notification badge if there are unread notifications */}
            {hasNewNotification && (
              <span
                onClick={(e) => {
                  e.stopPropagation(); // Prevent dropdown toggle
                  toggleNotificationCenter(); // Open notification center instead
                }}
                style={{
                  position: "absolute",
                  top: "0",
                  right: "0",
                  width: "12px",
                  height: "12px",
                  borderRadius: "50%",
                  backgroundColor: "#ff8243",
                  border: "2px solid white",
                  zIndex: 10,
                  cursor: "pointer",
                }}
              ></span>
            )}

            {isLoggedIn && userData && userData.customer_picture ? (
              <img
                src={
                  userData?.customer_picture
                    ? userData.customer_picture.startsWith("http")
                      ? userData.customer_picture // Use external Google profile image
                      : `http://127.0.0.1:8000/storage/${userData.customer_picture}` // Local image from Laravel
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
                    {userData && userData.customer_picture ? (
                      <img
                        src={
                          userData?.customer_picture
                            ? userData.customer_picture.startsWith("http")
                              ? userData.customer_picture // Use external Google profile image
                              : `http://127.0.0.1:8000/storage/${userData.customer_picture}` // Local image from Laravel
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
                      onClick={(e) => {
                        e.stopPropagation();
                        setIsOpenEditProfile(true);
                      }}
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
                    {userData ? userData.customer_name : "Loading..."}
                  </p>

                  {/* Notifications section */}
                  <div className="dropdown-section">
                    <div>
                      <div
                        style={{
                          display: "flex",
                          alignItems: "center",
                          gap: "5px",
                        }}
                      >
                        {notificationBadgeCount > 0 && (
                          <span
                            style={{
                              backgroundColor: "#ff8243",
                              color: "white",
                              fontSize: "0.7rem",
                              padding: "0px 6px",
                              borderRadius: "9999px",
                              fontWeight: "bold",
                            }}
                          >
                            {notificationBadgeCount}
                          </span>
                        )}
                      </div>

                      <button
                        className="notification-button"
                        onClick={(e) => {
                          e.stopPropagation();
                          toggleNotificationCenter();
                        }}
                      >
                        Notifications
                      </button>
                    </div>
                  </div>

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
      {/* Mobile navbar */}
      <div
        className={`navbar-collapse collapse ${isOpen ? "show" : ""}`}
        id="navbarContent"
      >
        <ul className="navbar-nav ml-lg-4 pt-3 pt-lg-0">
          <div className="row" style={{ textAlign: "center" }}>
            <div
              className="col-auto"
              style={{ padding: "0", position: "relative" }}
            >
              <i style={{ boxShadow: "none" }}>
                {isLoggedIn && userData && userData.customer_picture ? (
                  <img
                    src={
                      userData?.customer_picture
                        ? userData.customer_picture.startsWith("http")
                          ? userData.customer_picture // Use external Google profile image
                          : `http://127.0.0.1:8000/storage/${userData.customer_picture}` // Local image from Laravel
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
              {/* Notification badge in mobile view */}
              {hasNewNotification && isLoggedIn && (
                <span
                  onClick={() => toggleNotificationCenter()}
                  style={{
                    position: "absolute",
                    top: "0",
                    right: "0",
                    width: "12px",
                    height: "12px",
                    borderRadius: "50%",
                    backgroundColor: "#ff8243",
                    border: "2px solid white",
                    cursor: "pointer",
                  }}
                ></span>
              )}
            </div>
            <div
              className="col-auto"
              style={{
                textAlign: "center",
                marginTop: "10px",
              }}
            >
              <span
                className="user-name"
                style={{
                  fontSize: "20px",
                  color: "#AAAAAA",
                }}
              >
                {userData ? userData.customer_name : "Loading..."}
              </span>
            </div>
            <div
              className="col"
              style={{ fontSize: "30px", textAlign: "right" }}
            >
              <button
                className=""
                id="show-cart"
                onClick={toggleModalCart}
                style={{
                  border: "none",
                  color: "#6E6E6E",
                  background: "transparent",
                  textAlign: "right",
                }}
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
          <ul className="mobile-nav-links">
            <li className="nav-link">
              <Link to="/#menu">Menu</Link>
            </li>
            <li className="nav-link">
              <Link to="/#about">About</Link>
            </li>
            <li className="nav-link">
              <Link to="/#contact">Contact</Link>
            </li>
            <li className="nav-link">
              <Link to="/reservation">Reservation</Link>
            </li>
            <li
              className="nav-link"
              onClick={() => {
                toggleModalOrders();
                toggleNavbar();
              }}
            >
              <button
                style={{
                  color: "black",
                  textAlign: "left",
                  backgroundColor: "transparent",
                  fontFamily: "Poppins, sans-serif",
                  fontWeight: "200",
                  padding: "0 0 0 10px",
                  margin: "0",
                }}
              >
                Orders
              </button>
            </li>

            {isLoggedIn && (
              <li
                className="nav-link"
                onClick={() => {
                  toggleNotificationCenter();
                  toggleNavbar();
                }}
              >
                <button
                  style={{
                    color: "black",
                    textAlign: "left",
                    backgroundColor: "transparent",
                    fontFamily: "Poppins, sans-serif",
                    fontWeight: "200",
                    padding: "0 0 0 10px",
                    margin: "0",
                  }}
                >
                  Notifications
                </button>
                {notificationBadgeCount > 0 && (
                  <span
                    style={{
                      backgroundColor: "#ff8243",
                      color: "white",
                      fontSize: "0.7rem",
                      padding: "0px 6px",
                      borderRadius: "9999px",
                      fontWeight: "bold",
                    }}
                  >
                    {notificationBadgeCount}
                  </span>
                )}
              </li>
            )}
          </ul>

          <button
            style={{ marginTop: "20px" }}
            id="auth-button"
            onClick={isLoggedIn ? handleLogout : toggleModalLogin}
          >
            {isLoggedIn ? "Logout" : "Login"}
          </button>
        </ul>
      </div>
    </nav>
  );
};

export default Navbar;