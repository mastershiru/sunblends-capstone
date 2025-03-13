import React, { useState } from "react";
import { GoogleLogin } from "@react-oauth/google";
import { jwtDecode } from "jwt-decode";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faXmark } from "@fortawesome/free-solid-svg-icons";
import { useNavigate } from "react-router-dom";
import "../../assets/css/modal.css";
import axios from "axios";
import Forgotpassword from "./forgot.password";

function Login({
  isOpenLogin,
  toggleModalLogin,
  setIsLoggedIn,
  setUserData,
  email,
  setEmail,
  password,
  setPassword,
  toggleModalRegister,
}) {
  const [isOpenForgotpassword, setIsOpenForgotpassword] = useState(false);
  const navigate = useNavigate();

  const handleForgotpassword = () => {
    setIsOpenForgotpassword(true);
    toggleModalLogin(false);
  };

  // Updated login function that works with the Laravel controller
  function handleSubmit(event) {
    event.preventDefault();

    // Single login endpoint for both customers and employees
    axios
      .post("http://127.0.0.1:8000/api/login", {
        email,
        password,
      })
      .then((res) => {
        if (res.data.success) {
          alert(res.data.message);

          // Store token and user information
          localStorage.setItem("email", email);
          localStorage.setItem("token", res.data.token);

          // Handle different user types
          if (res.data.message.includes("Customer")) {
            setIsLoggedIn(true);
            setUserData(res.data.user);
            toggleModalLogin();

            // Redirect to dish page or stay on current page
          } else if (res.data.message.includes("Employee")) {
            // Redirect to dashboard or appropriate page
            window.location.href = res.data.redirect;
          }

          setPassword("");
        } else {
          alert("Login Failed: " + res.data.message);
          setEmail("");
          setPassword("");
        }
      })
      .catch((err) => {
        console.error("Login error:", err);
        const errorMessage =
          err.response?.data?.message || "An error occurred. Please try again.";
        alert(errorMessage);
        setEmail("");
        setPassword("");
      });
  }

  // Updated Google login function
  const handleGoogleLogin = (credentialResponse) => {
    const credentialResponseDecoded = jwtDecode(credentialResponse.credential);
    const { name, email, picture } = credentialResponseDecoded;

    axios
      .post(
        "http://127.0.0.1:8000/api/auth/google/callback",
        {
          customer_name: name, // ✅ Match backend
          customer_email: email, // ✅ Match backend
          customer_picture: picture, // ✅ Match backend
          Customer_Number: "N/A", // ✅ Provide a default value
        },
        { withCredentials: true }
      )
      .then((response) => {
        alert("Logged in with Google successfully.");
        setIsLoggedIn(true);

        console.log("Customer ID:", response.data.customer_id);
        // Save email and token
        localStorage.setItem("customer_id", response.data.customer_id);
        localStorage.setItem("email", email);
        localStorage.setItem("token", response.data.token);

        // Set user data
        setUserData({
          customer_id: response.data.customer_id,
          customer_name: name,
          customer_email: email,
          customer_picture: picture,
        });
        toggleModalLogin();
      })
      .catch((err) => {
        console.error("Google login error:", err.response?.data || err);
        alert(
          "Google login failed: " +
            (err.response?.data?.message || "Unknown error")
        );
      });
  };

  return (
    <>
      {isOpenLogin && (
        <div className="center" id="center">
          <div className="popup">
            <div
              className="close-btn"
              id="close-btn"
              onClick={() => toggleModalLogin(false)}
            >
              <FontAwesomeIcon icon={faXmark} />
            </div>
            <div className="form" id="login-form">
              <h2>Log In</h2>

              <form onSubmit={handleSubmit}>
                <div className="form-element">
                  <label htmlFor="email">Email</label>
                  <input
                    className="email"
                    type="text"
                    id="email"
                    name="email"
                    placeholder="Email"
                    required
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                  />
                </div>
                <div className="form-element">
                  <label htmlFor="password">Password</label>
                  <input
                    className="password"
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Password"
                    required
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                  />
                </div>
                <div className="form-element">
                  <button
                    className="forgot-password"
                    type="button"
                    onClick={handleForgotpassword}
                  >
                    Forgot password?
                  </button>
                </div>
                <div className="form-element">
                  <button
                    type="submit"
                    id="signin-button"
                    style={{ marginBottom: "0" }}
                  >
                    Sign In
                  </button>
                  <div className="form-element">
                    <button
                      type="button"
                      id="employee-login"
                      onClick={() =>
                        (window.location.href =
                          "http://127.0.0.1:8000/employee/login")
                      }
                    >
                      Employee Login
                    </button>
                  </div>
                  <div
                    className="google-login"
                    style={{
                      marginTop: "10px",
                    }}
                  >
                    <GoogleLogin
                      onSuccess={handleGoogleLogin}
                      onError={() => {
                        console.log("Login Failed");
                      }}
                    />
                  </div>
                </div>
                <p className="no-account">
                  Don't have an account?{" "}
                  <button
                    id="signup-btn"
                    onClick={(e) => {
                      e.preventDefault();
                      toggleModalLogin(false);
                      toggleModalRegister(true);
                    }}
                  >
                    Sign Up
                  </button>
                </p>
              </form>
            </div>
          </div>
        </div>
      )}
      <Forgotpassword
        isOpenForgotpassword={isOpenForgotpassword}
        setIsOpenForgotpassword={setIsOpenForgotpassword}
        toggleModalLogin={toggleModalLogin}
      />
    </>
  );
}

export default Login;
