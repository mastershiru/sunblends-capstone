import React, { useState } from "react";
import "../assets/css/admin.css";
import { useNavigate } from "react-router-dom";
import Queue from "./admin-components/queue";
import Menu from "./admin-components/admin-menu";
import Logo from "../assets/images/logo.png";
import Dineinorders from "./admin-components/dineIn-orders";

const Adminsection = () => {
  const navigate = useNavigate();
  const [activeSection, setActiveSection] = useState("queue"); // Default section is "queue"

  // Handle logout
  const handleLogout = () => {
    const confirmLogout = window.confirm("Are you sure you want to log out?");
    if (confirmLogout) {
      alert("You have been logged out.");
      navigate("/");
    }
  };

  return (
    <div className="queue-container">
      <aside className="sidebar">
        <img src={Logo} alt="Sunblends" className="logo-admin" />
        <ul>
          <li
            className={activeSection === "queue" ? "active" : ""}
            onClick={() => setActiveSection("queue")}
          >
            Ordering Queue
          </li>
          <li
            className={activeSection === "menu" ? "active" : ""}
            onClick={() => setActiveSection("menu")}
          >
            Menu
          </li>
          <li
            className={activeSection === "dineinorders" ? "active" : ""}
            onClick={() => setActiveSection("dineinorders")}
          >
            Dine in Orders
          </li>
          {/* Add more menu items here */}
        </ul>
        <button onClick={handleLogout} className="logout">
          Log Out
        </button>
      </aside>
      {/* <div className="user-profile">
        <span>Renz Valencia</span>
      </div> */}
      <main className="queue-content">
        {activeSection === "queue" && <Queue />}
        {activeSection === "menu" && <Menu />}
        {activeSection === "dineinorders" && <Dineinorders />}
      </main>
    </div>
  );
};

export default Adminsection;
