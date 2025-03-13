import React from "react";
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import "./assets/css/nav.css";
import "./assets/css/bootstrap.min.css";
import "./assets/css/style.css";
import Home from "./components/home";
import AdminSection from "./components/admin-section";
import AllMenu from "../src/components/allMenu"; // Import AllMenu component
import BookingTable from "./components/reservation";

function App() {
  return (
    <Router>
      <Routes>
        {/* Define the routes */}
        <Route path="/" element={<Home />} />
        <Route path="/admin-section" element={<AdminSection />} />
        <Route path="/allMenu" element={<AllMenu />} />
        <Route path="/reservation" element={<BookingTable />} />
      </Routes>
    </Router>
  );
}

export default App;
