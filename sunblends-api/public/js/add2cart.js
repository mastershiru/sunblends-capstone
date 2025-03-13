document.addEventListener('DOMContentLoaded', function() {
    // Initialize variables
    let dishData = [];
    let totalPrice = 0;
    let cart = [];

    // Fetch data from the server using Axios
    axios.get('http://localhost:3001/Profiles/menus')
        .then(function(response) {
            dishData = response.data;
            createDishBoxes(dishData);
            createCategoryFilterButtons(dishData);
        })
        .catch(function(error) {
            console.error('Error fetching data:', error);
        });

    // Function to create category filter buttons
    function createCategoryFilterButtons(data) {
        const categoryFilterContainer = document.querySelector('#category-filter-buttons');
        
        // Get a list of unique categories
        const uniqueCategories = [...new Set(data.map(dish => dish.Category))];
        
        // Create a button for each category
        uniqueCategories.forEach(category => {
            const button = document.createElement('button');
            button.textContent = category;
            button.type = 'button';
            // Add Bootstrap classes for styling
            button.className = 'btn btn-success me-2';
            
            // Add event listener to filter dishes based on category
            button.addEventListener('click', function() {
                filterByCategory(category);
            });
            
            // Append the button to the category filter container
            categoryFilterContainer.appendChild(button);
        });
        
        // Create a button to clear the filter and display all dishes
        const clearButton = document.createElement('button');
        clearButton.textContent = 'Clear Filter';
        clearButton.type = 'button';
        // Add Bootstrap classes for styling
        clearButton.className = 'btn btn-success';
        
        // Add event listener to clear the filter and display all dishes
        clearButton.addEventListener('click', function() {
            createDishBoxes(dishData);
        });
        
        // Append the clear filter button to the category filter container
        categoryFilterContainer.appendChild(clearButton);
    }

    // Function to filter dishes by category
    function filterByCategory(category) {
        // Filter the dish data based on the selected category
        const filteredDishes = dishData.filter(dish => dish.Category === category);
        
        // Clear the existing dish boxes
        const menuRow = document.querySelector('#menu-dish');
        menuRow.innerHTML = '';

        // Create new dish boxes for the filtered dishes
        createDishBoxes(filteredDishes);
    }

    // Function to create dish boxes
    function createDishBoxes(data) {
        const menuRow = document.querySelector('#menu-dish');
        menuRow.innerHTML = ''; // Clear existing dish boxes

        data.forEach(dish => {
            const dishBox = document.createElement('div');
            dishBox.className = 'col-lg-4 col-sm-6 dish-box';
            const imageUrl = `assets/images/dish/dish${dish.id}.png`;
            
            dishBox.innerHTML = `
                <div class="dish-box text-center">
                    <div class="dist-img">
                        <img src="${imageUrl}" alt="${dish.Dish}">
                    </div>
                    <div class="dish-title">
                        <h3 class="h3-title">${dish.Dish}</h3>
                        <p>${dish.Calories} calories</p>
                    </div>
                    <div class="dish-info">
                        <ul>
                            <li>
                                <p>Type</p>
                                <b>${dish.Category}</b>
                            </li>
                            <li>
                                <p>Persons</p>
                                <b>${dish.Persons}</b>
                            </li>
                        </ul>
                    </div>
                    <div class="dist-bottom-row">
                        <ul>
                            <li>
                                <b>$${dish.Price.toFixed(2)}</b>
                            </li>
                            <li>
                                <button class="dish-add-btn" data-dish-id="${dish.id}">
                                    <i class="uil uil-plus"></i>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            `;

            // Append the dish box to the menu row
            menuRow.appendChild(dishBox);
        });

        // Add event listeners for "Add" buttons
        document.querySelectorAll('.dish-add-btn').forEach(button => {
            button.addEventListener('click', function() {
                const dishId = parseInt(button.getAttribute('data-dish-id'));
                const dish = dishData.find(d => d.id === dishId);

                if (dish) {
                    addToCart(dish);
                } else {
                    console.error('Dish not found for ID:', dishId);
                }
            });
        });
    }

    // Function to add a dish to the cart
    function addToCart(dish) {
        // Find if the dish is already in the cart
        let cartItem = cart.find(item => item.id === dish.id);

        if (cartItem) {
            // If the item is already in the cart, increase the quantity and update total price
            cartItem.quantity++;
            cartItem.totalPrice += dish.Price;
        } else {
            // If the dish is not already in the cart, add a new item to the cart
            cartItem = {
                id: dish.id,
                dishName: dish.Dish,
                quantity: 1,
                unitPrice: dish.Price,
                totalPrice: dish.Price
            };
            cart.push(cartItem);
        }

        // Add the dish to the view cart
        addToCartView(dish, cartItem);

        // Update the cart display and total price
        updateCartDisplay();
        updateCartTotalPrice();
    }

    // Function to add a dish to the view cart and handle quantity changes
    function addToCartView(dish, cartItem) {
        const viewCart = document.querySelector('#view-cart');
        let cartItemElement = viewCart.querySelector(`.cart-item[data-id="${dish.id}"]`);

        if (!cartItemElement) {
            // Create a new cart item element
            cartItemElement = document.createElement('div');
            cartItemElement.className = 'cart-item';
            cartItemElement.setAttribute('data-id', dish.id);
            
            cartItemElement.innerHTML = `
                <div class="cart-item-content">
                    <div class="item-info-container">
                        <img src="assets/images/dish/dish${dish.id}.png" alt="${dish.Dish}">
                        <p>${dish.Dish}</p>
                    </div>
                    <div class="cart-btn-container">
                        <button class="minus-btn">-</button>
                        <span class="quantity">${cartItem.quantity}</span>
                        <button class="plus-btn">+</button>
                    </div>
                    <div class="item-price">$${cartItem.totalPrice.toFixed(2)}</div>
                </div>
            `;
            // Add the new cart item to the view-cart element
            viewCart.appendChild(cartItemElement);

            // Add event listeners for quantity buttons
            const plusBtn = cartItemElement.querySelector('.plus-btn');
            const minusBtn = cartItemElement.querySelector('.minus-btn');

            plusBtn.addEventListener('click', () => increaseQuantity(cartItemElement, dish));
            minusBtn.addEventListener('click', () => decreaseQuantity(cartItemElement, dish));
        } else {
            // Update the existing cart item element
            const quantityElement = cartItemElement.querySelector('.quantity');
            const priceElement = cartItemElement.querySelector('.item-price');

            quantityElement.textContent = cartItem.quantity;
            priceElement.textContent = `$${cartItem.totalPrice.toFixed(2)}`;
        }
    }

    // Function to increase the quantity of a cart item
    function increaseQuantity(cartItemElement, dish) {
        const cartItem = cart.find(item => item.id === dish.id);
        
        if (cartItem) {
            cartItem.quantity++;
            cartItem.totalPrice += dish.Price;
            
            const quantityElement = cartItemElement.querySelector('.quantity');
            const priceElement = cartItemElement.querySelector('.item-price');
            
            quantityElement.textContent = cartItem.quantity;
            priceElement.textContent = `$${cartItem.totalPrice.toFixed(2)}`;
            
            updateCartDisplay();
            updateCartTotalPrice();
        }
    }

    // Function to decrease the quantity of a cart item
    function decreaseQuantity(cartItemElement, dish) {
        const cartItem = cart.find(item => item.id === dish.id);
        
        if (cartItem && cartItem.quantity > 0) {
            cartItem.quantity--;
            cartItem.totalPrice -= dish.Price;
            
            if (cartItem.quantity === 0) {
                cart = cart.filter(item => item.id !== dish.id);
                cartItemElement.remove();
            } else {
                const quantityElement = cartItemElement.querySelector('.quantity');
                const priceElement = cartItemElement.querySelector('.item-price');
                
                quantityElement.textContent = cartItem.quantity;
                priceElement.textContent = `$${cartItem.totalPrice.toFixed(2)}`;
            }
            
            updateCartDisplay();
            updateCartTotalPrice();
        }
    }

    // Function to update the cart's total price display
    function updateCartTotalPrice() {
        const totalPriceElement = document.querySelector('#total-price');
        totalPrice = cart.reduce((sum, item) => sum + item.totalPrice, 0);
        totalPriceElement.textContent = `$${totalPrice.toFixed(2)}`;
    }

    // Function to update the cart display
    function updateCartDisplay() {
        // Update the cart number display
        const cartNumberElement = document.querySelector('.cart-number');
        cartNumberElement.textContent = cart.reduce((totalQuantity, item) => totalQuantity + item.quantity, 0);
    }

    // Checkout function to handle form submission
    function handleFormSubmission() {
        // Retrieve the selected ordering option
        const orderingOption = document.querySelector('input[name="orderingOption"]:checked').value;
        let pickupDateTime = null;

        if (orderingOption === 'advance') {
            const dateInput = document.querySelector('#pickup-date').value;
            const timeInput = document.querySelector('#pickup-time').value;
            pickupDateTime = new Date(`${dateInput}T${timeInput}`).toISOString();
        }

        // Capture the current date and time
        const currentDate = new Date();
        const orderDate = currentDate.toISOString(); // Format date in ISO format
        
        // Retrieve the customer ID from localStorage
        const customerId = localStorage.getItem('id');
    
        // Create the order object with cart data
        const order = {
            orderingOption: orderingOption,
            pickupDateTime: pickupDateTime,
            orderDate: orderDate,
            totalPrice: totalPrice,
            customerId: customerId,
            items: cart // Use the cart array as the order items
        };
    
        // Send the order data to the server using Axios
        axios.post('http://localhost:3001/Profiles/orders', order)
            .then(response => {
                console.log('Order submitted successfully:', response.data);
                // Handle successful submission (e.g., clear cart, show success message)
                alert('Order submitted successfully!');
                // Clear the cart and other relevant data
                cart = [];
                totalPrice = 0;
                updateCartTotalPrice();
                updateCartDisplay();
                document.querySelector('#view-cart').innerHTML = ''; // Clear the view-cart element
            })
            .catch(error => {
                console.error('Error submitting order:', error);
                // Handle error (e.g., show an error message)
                alert('Error submitting order. Please try again.');
            });
    }

    // Checkout button click event listener
    document.querySelector('.checkout-btn').addEventListener('click', () => {
        if (!localStorage.getItem('accessToken')) {
            alert("You need an account to place an online order. Alternatively, you can call us to place an order.");
        } else {
            handleFormSubmission();
        }
    });

    // Add event listener for ordering option change to show/hide date and time inputs
    document.querySelectorAll('input[name="orderingOption"]').forEach(option => {
        option.addEventListener('change', function() {
            const pickupDetails = document.querySelector('#pickup-details');
            if (this.value === 'advance') {
                pickupDetails.style.display = 'block';
            } else {
                pickupDetails.style.display = 'none';
            }
        });
    });
});
