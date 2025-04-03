// src/components/home.js - simplified version
import React, { useEffect } from "react";
import Coffee from "../assets/images/coffee.gif";
import About from "./about";
import TblReservation from "./table-reservation";
import Menu from "./menu";
import Promo from "./promo";
import Footer from "./footer";
import ScroolBacktoTop from "./back.to.Top.btn";
import { useLocation } from "react-router-dom";
import { useNavbar } from "../context/NavbarContext";

export default function Home() {
  const location = useLocation();
  const { setCartNumber } = useNavbar();

  // navigate to other screen like home to All menu, all menu to home
  useEffect(() => {
    if (location.hash) {
      const sectionId = location.hash.substring(1);
      const section = document.getElementById(sectionId);
      if (section) {
        section.scrollIntoView({ behavior: "smooth" });
      }
    }
  }, [location]);

  // Add to cart functionality for this specific page
  const addToCartNumber = () => {
    setCartNumber((prev) => prev + 1);
  };

  const [cartItems, setCartItems] = React.useState([]);

  const addToCart = (dish) => {
    setCartItems((prev) => {
      // Check if dish already exists in cart
      const existingItem = prev.find((item) => item.title === dish.title);

      if (existingItem) {
        // If it exists, increase quantity
        return prev.map((item) =>
          item.title === dish.title
            ? { ...item, quantity: item.quantity + 1 }
            : item
        );
      } else {
        // If it doesn't exist, add with quantity 1
        return [...prev, { ...dish, quantity: 1 }];
      }
    });
  };

  return (
    <>
      <ScroolBacktoTop />

      <section className="main-banner" id="home">
        <div className="sec-wp">
          <div className="container">
            <div className="row">
              <div className="col-lg-6">
                <div className="banner-text">
                  <h1 className="h1-title">
                    SHE LOVED <span>TEA</span>, <br></br> I AM COFFEE. <br></br> WE BLEND.
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
