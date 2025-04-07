// src/App.js
import React from "react";
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import "./assets/css/nav.css";
import "./assets/css/bootstrap.min.css";
import "./assets/css/style.css";
import { NavbarProvider } from "./context/NavbarContext";
import Navbar from "./components/Navbar";
import Modals from "./components/modal-compinents"; // Using existing path
import Home from "./components/home";
import AdminSection from "./components/admin-section";
import AllMenu from "./components/allMenu";
import BookingTable from "./components/reservation";
import NotificationManager from "./components/notifications/Notification-manager";
import RouteGuard from './utils/RouteGuard';
import NotificationModal from "./components/modal-compinents/notification-modal";




function App() {
  return (
    <NavbarProvider>
      <Router>
        <NotificationManager/>
        <NotificationModal />
        <Navbar />
        <Modals />
        <RouteGuard>
        <Routes>
          <Route path="/" element={<Home />} />
          <Route path="/admin-section" element={<AdminSection />} />
          <Route path="/allMenu" element={<AllMenu />} />
          <Route path="/reservation" element={<BookingTable />} />
        </Routes>
        </RouteGuard>
      </Router>
    </NavbarProvider>
  );
}

export default App;