// Declare variables for DOM elements
const loginForm = document.querySelector("#login-form");
const registerForm = document.querySelector("#register-form");
const center = document.querySelector("#center");
const popup = document.querySelector(".popup");
const closeBtn = document.querySelector("#close-btn");
const backBtn = document.querySelector("#back-btn");
const centerClass = document.querySelector(".center");
const account = document.querySelector("#profile-account");
const desktopaccountBtn = document.querySelector("#show-account");
const mobileaccountBtn = document.querySelector("#mobile-account-button");
const cart = document.querySelector("#view-cart");


// Function to handle showing the login popup
const showLoginPopup = () => {
    loginForm.style.display = "block";
    registerForm.style.display = "none";
    closeBtn.style.display = "block";
    center.style.zIndex = "99";
    account.style.display = "none";
    cart.style.display = "none";
    popup.classList.add("active");
    center.classList.add("slide-in-elliptic-top-fwd");
    center.classList.remove("slide-out-elliptic-bottom-bck");
    
};

// closing the popup
document.querySelector(".popup .close-btn").addEventListener("click", function () {
    popup.classList.remove("active");
    center.classList.remove("slide-in-elliptic-top-fwd");
    center.classList.add("slide-out-elliptic-bottom-bck");
    center.style.zIndex = "0";
});
//login popup on desktop
document.querySelector("#show-login").addEventListener("click", showLoginPopup);
//login popup on mobile
document.querySelector("#mobile-show-login").addEventListener("click", showLoginPopup);


// Function to handle showing the register form popup
const showRegisterFormPopup = () => {
    registerForm.style.display = "block";
    registerForm.classList.add("flip-in-ver-left");
    loginForm.style.display = "none";
    cart.style.display = "none";
    backBtn.style.display = "block";
    closeBtn.style.display = "none";

    // back from register form
    document.querySelector("#back-btn").addEventListener("click", function () {
        registerForm.style.display = "none";
        registerForm.classList.remove("flip-in-ver-left");  
        loginForm.classList.add("flip-in-ver-right");
        loginForm.style.display = "block";
        backBtn.style.display = "none";
        closeBtn.style.display = "block";
        closeBtn.style.zIndex = "1";
    });
};
// showing the register form popup and account popup
document.querySelector("#popup-register-form").addEventListener("click", showRegisterFormPopup);

// showing the account popup
const showAccountPopup = () => {
    account.style.display = "block";
    popup.classList.add("active");
    loginForm.style.display = "none";
    registerForm.style.display = "none";
    
    // Hide cart popup if it's currently displayed
    cart.style.display = "none";

    backBtn.style.display = "none";
    center.style.zIndex = "99";
    center.classList.remove("slide-out-elliptic-bottom-bck");
};

// showing the account popup
document.querySelector("#show-account").addEventListener("click", showAccountPopup);
document.querySelector("#mobile-account-button").addEventListener("click", showAccountPopup);



const showCartPopup = () => {
    cart.style.display = "block";
    account.style.display = "block";
    popup.classList.add("active");
    loginForm.style.display = "none";
    registerForm.style.display = "none";  
    // Hide account popup if it's currently displayed
    account.style.display = "none";
    backBtn.style.display = "none";
    center.style.zIndex = "99";
    center.classList.add("slide-in-elliptic-top-fwd");
    center.classList.remove("slide-out-elliptic-bottom-bck");
};


 //checkout button onlick
// document.querySelector(".checkout-btn").addEventListener("click", () => {
    
//     if(!localStorage.getItem('accessToken')){
//         alert("You Need a Account to place a online Order "+ "\n\nAlternatively you can call on our Phones to place a order")
//     }else{
//         alert("Your order is successfully been checkout")
//     }
    
// });

const cartIsEmptyMessage = () => {
    // "cart is empty" message
    alert("Cart is empty");
};

// Event listener for showing the register form popup and account popup
document.querySelector("#show-cart").addEventListener("click", () => {
    // Check if the cart is empty
    const cartNumber = document.querySelector(".cart-number").textContent.trim();
    if (cartNumber === "0") {
        // Cart is empty, show message
        cartIsEmptyMessage();
    } 
    else {
        // Cart is not empty, show popup
        showCartPopup();
    }
});

function navigateHome() {
    window.location.href = "/"; // Replace "/" with the URL of your home page
  }

  function setAuthState(authData) {
    // Store authentication data in localStorage
    localStorage.setItem("username", authData.username);
    localStorage.setItem("id", authData.id);
    localStorage.setItem("status", authData.status);
    localStorage.setItem("email", authData.email);
    
  }

// Function to handle login
const login = () => {
    // Get user input from the login form
    const password = document.getElementById("password").value;
    const email = document.getElementById("email").value;
    const admin = "";

    // Construct data object with user credentials
    const data = { password: password, email: email , isAdmin: admin};

    // Check if the email is for an admin
    
        // Send POST request to admin login endpoint
        axios.post("http://localhost:3001/Profiles/login", data)
            .then((response) => {
                // Handle response from the server
                if (response.data.error) {
                    alert(response.data.error);
                } else {
                    // Store access token in local storage
                    localStorage.setItem("accessToken", response.data.token);
                    localStorage.setItem("isAdmin", response.data.isAdmin);
                    // Set authentication state
                    setAuthState({ username: response.data.username, id: response.data.id, status: true, email: response.data.email});

                    if (localStorage.getItem("isAdmin") == "true") {
                    // Redirect to admin page
                    window.location.href = "/admin.html"; 
                    $('.popup').removeClass('active');
                    }
                    else{
                    navigateHome();
                    // Close the login popup
                    document.querySelector(".popup").classList.remove("active");
                    }
                }
            })
            .catch((error) => {
                console.error("Error logging in:", error);
                alert("Error logging in. Please try again.");
            });
    
};

// Function to fetch authentication status
const fetchAuthStatus = () => {
    return new Promise((resolve, reject) => {
        axios.get('http://localhost:3001/Profiles/auth', {
            headers: {
                accessToken: localStorage.getItem('accessToken'),
            },
        })
        .then((response) => {
            if (response.data.error) { 
                resolve({ username: "", id: 0, status: false }); // Resolve with default values
            } else {
                resolve({
                    username: response.data.username, 
                    id: response.data.id, 
                    status: true,
                });
            }
        })
        .catch((error) => {
            console.error('Error while fetching authentication status:', error);
            resolve({ username: "", id: 0, status: false }); // Resolve with default values in case of error
        });
    });
};

// Fetch profiles and authentication status on page load
window.onload = () => {
    // fetchProfiles();
    fetchAuthStatus();
};


// Event listener for sign-in button click
// document.querySelector(".popup #signin-button").addEventListener("click", login);

// Function to handle logout functionality
const handleLogout = () => {
    const desktopLoginText = document.querySelector("#show-login").innerText;
    const mobileLoginText = document.querySelector("#mobile-show-login").innerText;
    const accountBtn = document.querySelector("#show-account");
    
    if (desktopLoginText === "Logout" && mobileLoginText === "Logout") {
        alert("Successfully logged out");

        // Hide account-related buttons
        accountBtn.style.display = "none";
        mobileaccountBtn.style.display = "none";

        // Change button text back to "Login"
        document.querySelector("#show-login").innerText = "Login";
        document.querySelector("#mobile-show-login").innerText = "Login";
        
    }
};

// Event listeners for logout button clicks
document.querySelector("#show-login").addEventListener("click", handleLogout);
document.querySelector("#mobile-show-login").addEventListener("click", handleLogout);


//CART NUMBER ADD
document.addEventListener('DOMContentLoaded', function() {
    // Get all the buttons with the class "dish-add-btn"
    const addButtons = document.querySelectorAll('.dish-add-btn');
    // Get the cart number element
    const counter = document.querySelector('.cart-number');

    let count = 0; // Initialize count

    // Update the counter display
    function updateCounter() {
        counter.textContent = count;
    }

    // Increment count when any of the buttons are clicked
    addButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            if (!button.classList.contains('clicked')) {
                count++;
                button.classList.add('clicked'); // Add the 'clicked' class to disable further clicks
                updateCounter();
            }
        });
    });

    // Initialize the counter display
    updateCounter();

    // Update count when a dish is removed from the cart
    function updateCountOnRemove() {
        const cartItems = document.querySelectorAll('.cart-item');
        count = cartItems.length;
        updateCounter();
    }

    // Call updateCountOnRemove() whenever a dish is removed
    const cart = document.getElementById('view-cart');
    cart.addEventListener('click', function(event) {
        if (event.target.classList.contains('minus-btn')) {
            updateCountOnRemove();
        }
    });
});


