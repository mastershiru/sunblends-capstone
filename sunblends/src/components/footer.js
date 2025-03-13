import React from "react";
import FotterLogo from "../assets/images/logo.png";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import {
  faSquareFacebook,
  faInstagram,
  faTiktok,
} from "@fortawesome/free-brands-svg-icons";

const Footer = () => {
  return (
    <footer className="site-footer" id="contact">
      <div className="top-footer section">
        <div className="sec-wp">
          <div className="container">
            <div className="row row-footer">
              {/* Footer Logo and Info Section */}
              <div className="col-lg-4">
                <div className="footer-info">
                  <div className="footer-logo">
                    <a href="index.html">
                      <img
                        src={FotterLogo}
                        alt="Logo"
                        style={{ height: "70px", width: "130px" }}
                      />
                    </a>
                  </div>
                  <p className="footer-text">
                    Contact us via our social media accounts for inquiries and
                    updates.
                  </p>
                  <div className="social-icon">
                    <ul>
                      <li>
                        <a href="https://facebook.com">
                          <FontAwesomeIcon icon={faSquareFacebook} />
                        </a>
                      </li>
                      <li>
                        <a href="/">
                          <FontAwesomeIcon icon={faInstagram} />
                        </a>
                      </li>
                      <li>
                        <a href="/">
                          <FontAwesomeIcon icon={faTiktok} />
                        </a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>

              {/* Footer Links Section */}
              <div className="col-lg-4 links">
                <div className="footer-menu food-nav-menu">
                  <h3 className="footer-h3-title">Links</h3>
                  <ul className="column-2">
                    <li>
                      <a href="#home">Home</a>
                    </li>
                    <li>
                      <a href="#about">About</a>
                    </li>
                    <li>
                      <a href="#menu">Menu</a>
                    </li>
                    <li>
                      <a href="#contact">Contact</a>
                    </li>
                  </ul>
                </div>
              </div>

              {/* Company Links Section */}
              <div className="col-lg-4 company">
                <div className="footer-menu">
                  <h3 className="footer-h3-title">Company</h3>
                  <ul>
                    <li>
                      <a href="/">Terms & Conditions</a>
                    </li>
                    <li>
                      <a href="/">Privacy Policy</a>
                    </li>
                    <li>
                      <a href="/">Cookie Policy</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </footer>
  );
};

export default Footer;
