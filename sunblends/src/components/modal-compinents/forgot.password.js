import React from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faChevronLeft } from "@fortawesome/free-solid-svg-icons";
import "../../assets/css/modal.css";
const Forgotpassword = ({
  isOpenForgotpassword,
  setIsOpenForgotpassword,
  toggleModalLogin,
}) => {
  return (
    <>
      {isOpenForgotpassword && (
        <div className="center" id="center">
          <div className="popup">
            <div className="back-btn" id="back-btn">
              <FontAwesomeIcon
                icon={faChevronLeft}
                onClick={() => {
                  toggleModalLogin(true);
                  setIsOpenForgotpassword(false);
                }} // Close the modal
              />
            </div>

            <div
              className="forgot-password-section"
              id="forgot-password-section"
            >
              <h2>Forgot Password</h2>
              <div className="form-element">
                <p style={{ textAlign: "center", marginBottom: "0" }}>
                  Enter your email
                </p>
                <input
                  className="email"
                  type="text"
                  id="email"
                  name="email"
                  placeholder="Email"
                />
              </div>
              <div className="form-element">
                <button>Continue</button>
              </div>
            </div>
          </div>
        </div>
      )}
    </>
  );
};

export default Forgotpassword;
