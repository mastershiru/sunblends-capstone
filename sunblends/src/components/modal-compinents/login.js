import React, { useState, useEffect } from "react";
import { GoogleLogin } from "@react-oauth/google";
import { jwtDecode } from "jwt-decode";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faXmark } from "@fortawesome/free-solid-svg-icons";
import { useNavigate } from "react-router-dom";
import "../../assets/css/modal.css";
import axios from "axios";
import Forgotpassword from "./forgot.password";
import TokenManager from "../../utils/tokenManager";
import { toast } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";

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

  const API_BASE_URL = process.env.REACT_APP_API_URL || "https://api.sunblends.store/api";

  // Configure axios defaults for CSRF and credentials
  useEffect(() => {
    // Enable sending cookies with cross-origin requests
    axios.defaults.withCredentials = true;
    
    // Get CSRF cookie from the API
    axios.get(`${API_BASE_URL.replace('/api', '')}/sanctum/csrf-cookie`)
      .catch(error => {
        console.error("Error fetching CSRF cookie:", error);
      });
  }, [API_BASE_URL]);

  const handleForgotpassword = () => {
    setIsOpenForgotpassword(true);
    toggleModalLogin(false);
  };

  function handleSubmit(event) {
    event.preventDefault();

    // Show loading toast
    const loadingToastId = toast.loading("Logging in...");

    axios
      .post(`${API_BASE_URL}/login`, {
        email,
        password,
      }, {
        withCredentials: true // Ensure cookies are sent with the request
      })
      .then((res) => {
        toast.dismiss(loadingToastId);
        
        if (res.data.success) {
          toast.success(res.data.message, {
            position: "top-right",
            autoClose: 2000,
          });

          // Store token securely with TokenManager
          if (res.data.token) {
            TokenManager.setToken(res.data.token, res.data.user);

            // Set minimal session indicator in sessionStorage
            sessionStorage.setItem("user_id", res.data.user.customer_id);
            sessionStorage.setItem("is_authenticated", "true");
          }

          // Handle different user types
          if (res.data.message.includes("Customer")) {
            setIsLoggedIn(true);
            setUserData(res.data.user);
            toggleModalLogin();
          } else if (res.data.message.includes("Employee")) {
            // Redirect to dashboard or appropriate page
            window.location.href = res.data.redirect;
          }

          setPassword("");
        } else {
          toast.error("Login Failed: " + res.data.message, {
            position: "top-right",
          });
          setEmail("");
          setPassword("");
        }
      })
      .catch((err) => {
        toast.dismiss(loadingToastId);
        console.error("Login error:", err);
        const errorMessage =
          err.response?.data?.message || "An error occurred. Please try again.";
        toast.error(errorMessage, {
          position: "top-right",
        });
        setPassword("");
      });
  }

  const handleGoogleLogin = (credentialResponse) => {
    const credentialResponseDecoded = jwtDecode(credentialResponse.credential);
    const { name, email, picture } = credentialResponseDecoded;

    // Check if email is from the organization domain
    if (!email.endsWith("@tua.edu.ph")) {
      toast.error("Please use your TUA organizational email (@tua.edu.ph) to login.", {
        position: "top-right",
      });
      return;
    }

    // Show loading toast
    const loadingToastId = toast.loading("Logging in with Google...");

    axios
      .post(
        `${API_BASE_URL}/auth/google/callback`,
        {
          customer_name: name,
          customer_email: email,
          customer_picture: picture,
          Customer_Number: "N/A",
        },
        { 
          withCredentials: true,
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          }
        }
      )
      .then((response) => {
        toast.dismiss(loadingToastId);
        toast.success("Logged in successfully!", {
          position: "top-right",
          autoClose: 2000,
        });

        // Store token securely with TokenManager
        if (response.data.token) {
          const userData = {
            customer_id: response.data.customer_id,
            customer_name: name,
            customer_email: email,
            customer_picture: picture,
          };

          TokenManager.setToken(response.data.token, userData);

          // Set minimal session indicator in sessionStorage
          sessionStorage.setItem("user_id", response.data.customer_id);
          sessionStorage.setItem("is_authenticated", "true");
        }

        setIsLoggedIn(true);

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
        toast.dismiss(loadingToastId);
        console.error("Google login error:", err.response?.data || err);
        toast.error(
          "Google login failed: " +
            (err.response?.data?.message || "Unknown error"),
          {
            position: "top-right",
          }
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

                  <div
                    className="google-login"
                    style={{
                      marginTop: "10px",
                    }}
                  >
                    <GoogleLogin
                      onSuccess={handleGoogleLogin}
                      onError={() => {
                        toast.error("Google login failed", {
                          position: "top-right",
                        });
                      }}
                    />
                  </div>
                </div>
                <div className="form-element">
                  <button
                    type="button"
                    id="employee-login"
                    onClick={() =>
                      (window.location.href =
                        `${API_BASE_URL.replace('/api', '')}/employee/login`)
                    }
                  >
                    Employee Login
                  </button>
                </div>
                {/* <p className="no-account">
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
                </p> */}
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