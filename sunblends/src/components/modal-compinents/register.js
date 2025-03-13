import React from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faXmark } from "@fortawesome/free-solid-svg-icons";
import axios from "axios";

function Register({ isOpenRegister, toggleModalRegister, toggleModalLogin }) {
  //handleRegister start
  const handleRegister = async () => {
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm-password").value;

    // Check if passwords match
    if (password !== confirmPassword) {
        alert("Passwords do not match");
        document.getElementById("password").value = "";
        document.getElementById("confirm-password").value = "";
        return;
    }

    const formData = new FormData();
    formData.append("Customer_Name", document.getElementById("name").value);
    formData.append("Customer_Email", document.getElementById("email").value);
    formData.append("Customer_Number", document.getElementById("phoneNumber").value);
    formData.append("Customer_Password", password);
    formData.append("Customer_Password_confirmation", confirmPassword);
    formData.append("Customer_Img", document.querySelector("input[type='file']").files[0]);

    try {
        const response = await axios.post(
            "http://127.0.0.1:8000/api/register",
            formData,
            {
                headers: { "Content-Type": "multipart/form-data" },
            }
        );

        // Clear text boxes
        document.getElementById("name").value = "";
        document.getElementById("email").value = "";
        document.getElementById("phoneNumber").value = "";
        document.querySelector("input[type='file']").value = "";

        alert(response.data.message); // Adjusted to display success message

        toggleModalRegister(false);
    } catch (error) {
        console.error("Error during registration:", error);

        if (error.response && error.response.data.errors) {
            const errorMessages = Object.values(error.response.data.errors)
                .map(msgArray => msgArray.join("\n"))
                .join("\n");
            alert("Registration failed:\n" + errorMessages);
        } else {
            alert("Registration failed. Please try again.");
        }
    }
};

  // const handleRegister = async () => {
  //   const password = document.getElementById("password").value;
  //   const confirmPassword = document.getElementById("confirm-password").value;

  //   // Check if passwords match
  //   if (password !== confirmPassword) {
  //     alert("Passwords do not match");
  //     // Clear txt boxes
  //     document.getElementById("password").value = "";
  //     document.getElementById("confirm-password").value = "";
  //     return;
  //   }

  //   const formData = new FormData();
  //   formData.append("name", document.getElementById("name").value);
  //   formData.append("email", document.getElementById("email").value);
  //   formData.append(
  //     "phoneNumber",
  //     document.getElementById("phoneNumber").value
  //   );
  //   formData.append("password", document.getElementById("password").value);
  //   formData.append(
  //     "confirmPassword",
  //     document.getElementById("confirm-password").value
  //   );
  //   formData.append(
  //     "image",
  //     document.querySelector("input[type='file']").files[0]
  //   );

  //   try {
  //     const response = await axios.post(
  //       "http://127.0.0.1:8000/api/register",
  //       formData,
  //       {
  //         headers: { "Content-Type": "multipart/form-data" },
  //       }
  //     );
  //     // Clear txt boxes
  //     document.getElementById("name").value = "";
  //     document.getElementById("email").value = "";
  //     document.getElementById("phoneNumber").value = "";
  //     document.getElementById("password").value = "";
  //     document.getElementById("confirm-password").value = "";
  //     document.querySelector("input[type='file']").value = "";

  //     alert(response.data);

  //     toggleModalRegister(false);
  //   } catch (error) {
  //     console.error("Error during registration:", error);

  //     if (
  //       error.response &&
  //       error.response.data &&
  //       error.response.data.code === "ER_DUP_ENTRY"
  //     ) {
  //       alert(
  //         "The email you entered is already registered. Please use a different email."
  //       );
  //     } else {
  //       alert("Registration failed. Please try again.");
  //     }

  //     // Clear txt boxes
  //     document.getElementById("name").value = "";
  //     document.getElementById("email").value = "";
  //     document.getElementById("phoneNumber").value = "";
  //     document.getElementById("password").value = "";
  //     document.getElementById("confirm-password").value = "";
  //     document.querySelector("input[type='file']").value = "";
  //   }
  // };
  //end

  return (
    <>
      {isOpenRegister && (
        <div className="center" id="center">
          <div className="popup">
            <div
              className="close-btn"
              id="close-btn"
              onClick={() => toggleModalRegister(false)}
            >
              <i>
                <FontAwesomeIcon icon={faXmark} />
              </i>
            </div>
            <div className="form" id="login-form">
              <h2>Register</h2>
              <div className="form-element">
                <label htmlFor="name">Full Name</label>
                <input
                  type="text"
                  id="name"
                  name="name"
                  placeholder="Full Name"
                  required
                />
              </div>
              <div className="form-element">
                <label htmlFor="email">Email</label>
                <input
                  type="text"
                  id="email"
                  name="email"
                  placeholder="Email"
                  required
                />
              </div>
              <div className="form-element">
                <label htmlFor="phoneNumber">Phone Number</label>
                <input
                  type="text"
                  id="phoneNumber"
                  name="phoneNumber"
                  placeholder="Phone Number"
                />
              </div>
              <div className="form-element">
                <label htmlFor="password">Password</label>
                <input
                  type="password"
                  id="password"
                  name="password"
                  placeholder="Password"
                  required
                />
              </div>
              <div className="form-element">
                <label htmlFor="confirm-password">Confirm Password</label>
                <input
                  type="password"
                  id="confirm-password"
                  name="confirm-password"
                  placeholder="Confirm Password"
                  required
                />
              </div>
              <div className="form-element">
                <input type="file" />
              </div>
              <div className="form-element">
                <button
                  id="popup-register-form"
                  className="signup-btn"
                  onClick={handleRegister}
                  style={{ marginBottom: "0" }}
                >
                  Sign Up
                </button>
              </div>
              <p style={{ fontSize: "15px", margin: "0" }}>
                Already have an account?{" "}
                <button
                  id="signup-button"
                  className="signup-button"
                  onClick={(e) => {
                    e.preventDefault();
                    toggleModalRegister(false);
                    toggleModalLogin(true);
                  }}
                >
                  Sign In here
                </button>
              </p>
            </div>
          </div>
        </div>
      )}
    </>
  );
}

export default Register;
