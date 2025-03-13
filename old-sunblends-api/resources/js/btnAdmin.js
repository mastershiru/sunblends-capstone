// Function to conditionally render the admin button based on the `isAdmin` value
function renderAdminButton() {
    // Check the value of `isAdmin`
    // In this example, we assume `isAdmin` is stored in local storage
    // Replace with your logic to check `isAdmin` as needed
    const isAdmin = localStorage.getItem('isAdmin') === 'true';

    // Get the navbar element
    const navbar = document.getElementById('navbar');

    // Check if `isAdmin` is true
    if (isAdmin) {
        // Create a new list item element
        const listItem = document.createElement('li');

        // Create a button element for the admin panel
        const adminButton = document.createElement('button');
        adminButton.textContent = 'Admin Panel'; // Text for the button
        adminButton.className = 'btn btn-success'; // Bootstrap classes for styling
        adminButton.id = 'admin-button'; // ID for the button

        // Add an event listener to handle button clicks (e.g., navigate to the admin panel)
        adminButton.addEventListener('click', function() {
            // Redirect to admin panel
            window.location.href = 'admin.html';
        });

        // Append the button to the list item
        listItem.appendChild(adminButton);

        // Append the list item to the navbar
        navbar.appendChild(listItem);
    }
}

// Call the function to render the admin button when the page loads
document.addEventListener('DOMContentLoaded', function() {
    renderAdminButton();
});
