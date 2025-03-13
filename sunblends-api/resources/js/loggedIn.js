
// Function to decode JWT token
function decodeToken(token) {
  try {
      // Decode token
      const decoded = JSON.parse(atob(token.split('.')[1]));
      return decoded;
  } catch (error) {
      console.error('Error decoding token:', error);
      return null;
  }
}

// Function to update UI based on authentication state
function updateUI(decodedToken) {
  const user = localStorage.getItem('username');
  const email = localStorage.getItem('email');
  const admin = localStorage.getItem('isAdmin');
  const loginButton = document.getElementById("show-login");
  const logoutButton = document.getElementById("logout-button");
  // const adminLink = document.getElementById("admin-link");
  const usernameDisplay = document.getElementById("username-display");
  const profileAccount = document.getElementById("show-account");
  const nameAccount = document.getElementById("AccName");
  const emailAccount = document.getElementById("AccEmail");

  if (decodedToken) {
      // User is logged in
      loginButton.style.display = "none";
      logoutButton.style.display = "inline";
      profileAccount.style.display = "inline";
      // usernameDisplay.textContent = decodedToken.username;
      nameAccount.textContent = "Username: " + user
      emailAccount.textContent = email;

      // if (decodedToken.isAdmin) {
      //     adminLink.style.display = "inline";
      // } else {
      //     adminLink.style.display = "none";
      // }
  } else {
      // User is not logged in
      loginButton.style.display = "inline";
      logoutButton.style.display = "none";
      adminLink.style.display = "none";
      usernameDisplay.textContent = "";
  }
}

// Function to check if user is logged in based on token in localStorage
function checkLoggedIn() {
  const token = localStorage.getItem('accessToken');
  if (token) {
      // Token found, decode it
      const decodedToken = decodeToken(token);
      if (decodedToken) {
          // Update UI based on authentication state
          updateUI(decodedToken);
          return true; // User is logged in
      }
  }
  return false; // User is not logged in
}

// Function to handle logout
function logout() {
  localStorage.removeItem('accessToken');
  localStorage.removeItem('username');
  localStorage.removeItem('id');
  localStorage.removeItem('status');
  localStorage.removeItem('email');
  localStorage.removeItem('isAdmin');
  alert("Logout Successful");
  console.log("Logout Successful")
  navigateHome();
  updateUI(null); // Update UI to reflect logout
}

// Function to handle login
// function login() {
//   // Perform login authentication
//   // Once authenticated, set the token in localStorage
//   const accessToken = "YOUR_ACCESS_TOKEN_HERE"; // Replace with actual token
//   localStorage.setItem('accessToken', accessToken);
//   const decodedToken = decodeToken(accessToken);
//   updateUI(decodedToken); // Update UI to reflect login
// }

// Initialize UI based on initial authentication state
document.addEventListener('DOMContentLoaded', function() {
  checkLoggedIn();
});
