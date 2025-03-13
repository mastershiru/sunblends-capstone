// Web Annimation
ScrollReveal({
  reset: true,
  distance: "60px",
  duration: 1000,
  delay: 0,
});
ScrollReveal().reveal(".h1-title, .tagline, .img1, .text2", { origin: "left" });
ScrollReveal().reveal(".banner-img-wp, .img2, .text1", { origin: "right" });
ScrollReveal().reveal(".banner-btn, .sec-title, .best-selling-tab-wp", {
  origin: "bottom",
});
ScrollReveal().reveal(".dish-box", { easing: "ease-out", interval: 300 });
ScrollReveal().reveal(".about-video-img", {
  duration: 800,
  easing: "ease-out",
  scale: 1.2,
});
ScrollReveal().reveal(".row .info, .social-icon li", {
  origin: "bottom",
  interval: 300,
});
ScrollReveal().reveal(
  ".right-content .h3-title, .footer-logo,.footer-menu ul li,.footer-table-info ul li, .footer-text",
  { origin: "left", interval: 300 }
);

// Function to toggle the mobile menu
const toggleMobileMenu = () => {
  $("#navbar").toggleClass("active");
};

// Function to toggle the user dropdown menu
const toggleUserDropdown = () => {
  const dropdown = $(".dropdown-content");
  // dropdown.addClass('swing-in-top-fwd');
  $(".dropdown-content").toggleClass("dropdown-open");
};

// Set up event listeners when the document is ready
$(document).ready(function () {
  // $('#mobile').on('click', toggleMobileMenu);
  $("#user").on("click", toggleUserDropdown);
});
