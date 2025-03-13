function handleFormSubmission() {
    // Retrieve selected ordering option
    const orderingOption = document.querySelector('input[name="orderingOption"]:checked').value;
    
    // Gather all necessary order data
    const orderData = {
        orderingOption, // Include the ordering option
        items: cart.map(item => ({
            dishId: item.id,
            quantity: item.quantity,
            price: item.Price // Include other item details as needed
        })),
        // Add other order data as necessary, such as customer ID, date, etc.
    };
    
    // Submit the order data to the server
    axios.post('http://localhost:3001/Profiles/orders', orderData)
        .then(response => {
            console.log('Order submitted successfully:', response.data);
            
            // Handle successful submission
            // Display success popup
            alert('Order submitted successfully!');
            
            // Clear the cart after successful submission
            cart = [];
            viewCart.innerHTML = '';
            updateCartTotalPrice();
            updateCartNumber();
        })
        .catch(error => {
            console.error('Error submitting order:', error);
            // Handle error (e.g., display an error message)
        });
}
 
 
//checkout button onlick
document.querySelector(".checkout-btn").addEventListener("click", () => {
    
    if(!localStorage.getItem('accessToken')){
        alert("You Need a Account to place a online Order "+ "\n\nAlternatively you can call on our Phones to place a order")
    }else{
        handleFormSubmission();
    }
    
});