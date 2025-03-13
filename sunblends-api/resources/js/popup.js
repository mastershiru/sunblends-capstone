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
    backBtn.style.display = "none";
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


// document.addEventListener('DOMContentLoaded', function() {
//     // Get all the buttons with the class "dish-add-btn"
//     const addButtons = document.querySelectorAll('.dish-add-btn');
//     const cart = document.getElementById('view-cart');
    
    
//     // Function to add the dish to the cart
//     function addToCart(dishInfo) {
//         // Check if the dish is already in the cart
//         const existingCartItem = document.querySelector(`.cart-item[data-title="${dishInfo.title}"]`);
        
//         if (existingCartItem) {
//             // If the dish already exists, increment its quantity and update the total price
//             const quantityElement = existingCartItem.querySelector('.quantity');
//             let quantity = parseInt(quantityElement.textContent);
//             quantityElement.textContent = quantity + 1;
            
//             // Get the price of the dish from the data attribute
//             const price = parseFloat(existingCartItem.dataset.price);
            
//             // Update the total price
//             totalPrice += price;
//         } else {
//             // If the dish is not in the cart, add it as a new item
//             const cartItem = document.createElement('div');
//             cartItem.classList.add('cart-item');
//             cartItem.setAttribute('data-title', dishInfo.title);
//             cartItem.setAttribute('data-price', dishInfo.price); // Add the price as a data attribute
            
//             cartItem.innerHTML = `
//                 <div class="cart-item-content">
//                     <div class="item-info-container">
//                         <img src="${dishInfo.image}" alt="${dishInfo.title}">
//                         <p>${dishInfo.title}</p>
//                     </div>
//                     <div class="cart-btn-container">
//                         <button class="plus-btn cart-btn">+</button>
//                         <span class="quantity">1</span>
//                         <button class="minus-btn cart-btn">-</button>
//                     </div>
//                 </div>
//             `;
//             cart.appendChild(cartItem);
            
//             // Add event listeners for the plus and minus buttons
//             const plusButton = cartItem.querySelector('.plus-btn');
//             const minusButton = cartItem.querySelector('.minus-btn');
            
//             // Get the price of the dish from the data attribute
//             const price = parseFloat(cartItem.dataset.price);
            
//             // Update the total price
//             totalPrice += price;
            
//             plusButton.addEventListener('click', function() {
//                 const quantityElement = this.nextElementSibling;
//                 let quantity = parseInt(quantityElement.textContent);
                
//                 quantityElement.textContent = quantity + 1;
                
//                 // Update the total price
//                 totalPrice += price;
//                 updateTotalPriceDisplay();
//             });
            
//             minusButton.addEventListener('click', function() {
//                 const quantityElement = this.previousElementSibling;
//                 let quantity = parseInt(quantityElement.textContent);
                
//                 if (quantity > 1) {
//                     quantityElement.textContent = quantity - 1;
                    
//                     // Update the total price
//                     totalPrice -= price;
//                     updateTotalPriceDisplay();
//                 } else {
//                     // If quantity is 1, remove the cart item
//                     cart.removeChild(cartItem);
                    
//                     // Update the total price
//                     totalPrice -= price;
//                     updateTotalPriceDisplay();
//                 }
//             });
//         }
        
//         // Function to update the total price display
//         function updateTotalPriceDisplay() {
//             const totalPriceElement = document.querySelector('#total-price');
//             totalPriceElement.textContent = `Total: $${totalPrice.toFixed(2)}`;
//         }
        
//         // Call updateTotalPriceDisplay to initialize the total price
//         updateTotalPriceDisplay();
//     }

//     addButtons.forEach(function(button) {
//         button.addEventListener('click', function() {
//             const dishBox = this.closest('.dish-box');
//             const dishImage = dishBox.querySelector('.dist-img img').src;
//             const dishTitle = dishBox.querySelector('.dish-title .h3-title').textContent;
//             const dishPrice = parseFloat(dishBox.querySelector('.dist-bottom-row > ul > li:first-child > b').textContent.replace('$', '')); // Get the dish price
    
//             addToCart({ image: dishImage, title: dishTitle, price: dishPrice });
//         });
//     });

//     plusButton.addEventListener('click', function() {
//         const quantityElement = this.nextElementSibling;
//         let quantity = parseInt(quantityElement.textContent);
        
//         quantityElement.textContent = quantity + 1;
        
//         // Get the price of the dish from the data attribute
//         const price = parseFloat(this.closest('.cart-item').dataset.price);
        
//         // Update the total price
//         totalPrice += price;
//         updateTotalPriceDisplay();
//     });
    
//     minusButton.addEventListener('click', function() {
//         const quantityElement = this.previousElementSibling;
//         let quantity = parseInt(quantityElement.textContent);
        
//         if (quantity > 1) {
//             quantityElement.textContent = quantity - 1;
            
//             // Get the price of the dish from the data attribute
//             const price = parseFloat(this.closest('.cart-item').dataset.price);
            
//             // Update the total price
//             totalPrice -= price;
//             updateTotalPriceDisplay();
//         } else {
//             // If quantity is 1, remove the cart item
//             const cartItem = this.closest('.cart-item');
//             const price = parseFloat(cartItem.dataset.price);
            
//             // Update the total price
//             totalPrice -= price;
            
//             // Remove the cart item
//             cart.removeChild(cartItem);
            
//             // Update cart number
//             updateCartNumber();
            
//             // Update the total price display
//             updateTotalPriceDisplay();
//         }
//     });

// });