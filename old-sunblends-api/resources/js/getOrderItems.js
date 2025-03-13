// Function to fetch order items from the server
function fetchOrderItems() {
    axios.get(`http://localhost:3001/Profiles/items`) // Adjust the URL to your endpoint
        .then(response => {
            const data = response.data;
            renderOrderItemsTable(data);
        })
        .catch(error => {
            console.error('Error fetching order items:', error);
            alert('Failed to fetch order items data.');
        });
}

// Function to render the order items table
function renderOrderItemsTable(data) {
    const container = document.getElementById('main-content');
    
    // Clear previous content
    container.innerHTML = '';
    
    // Create a Bootstrap table
    const table = document.createElement('table');
    table.classList.add('table', 'table-striped');
    
    // Create table headers
    const thead = document.createElement('thead');
    const trHeader = document.createElement('tr');
    const headers = ['Item ID', 'Item Name', 'Quantity', 'Price', 'Actions'];
    
    headers.forEach(header => {
        const th = document.createElement('th');
        th.textContent = header;
        trHeader.appendChild(th);
    });
    thead.appendChild(trHeader);
    table.appendChild(thead);
    
    // Create table body
    const tbody = document.createElement('tbody');
    
    data.forEach(item => {
        const tr = document.createElement('tr');
        
        // Add order item data to the table row
        const itemIdCell = document.createElement('td');
        itemIdCell.textContent = item.itemId;
        tr.appendChild(itemIdCell);
        
        const itemNameCell = document.createElement('td');
        itemNameCell.textContent = item.itemName;
        tr.appendChild(itemNameCell);
        
        const quantityCell = document.createElement('td');
        quantityCell.textContent = item.quantity;
        tr.appendChild(quantityCell);
        
        const priceCell = document.createElement('td');
        priceCell.textContent = parseFloat(item.price).toFixed(2);
        tr.appendChild(priceCell);
        
        // Create action buttons cell
        const actionsCell = document.createElement('td');
        
        // Create Edit button
        const editButton = document.createElement('button');
        editButton.classList.add('btn', 'btn-primary', 'btn-sm');
        editButton.textContent = 'Edit';
        editButton.setAttribute('data-bs-toggle', 'modal');
        editButton.setAttribute('data-bs-target', '#editOrderItemModal');
        editButton.addEventListener('click', () => editOrderItem(item.itemId));
        actionsCell.appendChild(editButton);
        
        // Create Delete button
        const deleteButton = document.createElement('button');
        deleteButton.classList.add('btn', 'btn-danger', 'btn-sm');
        deleteButton.textContent = 'Delete';
        deleteButton.addEventListener('click', () => deleteOrderItem(item.itemId));
        actionsCell.appendChild(deleteButton);
        
        tr.appendChild(actionsCell);
        tbody.appendChild(tr);
    });
    
    table.appendChild(tbody);
    container.appendChild(table);
}

// Function to handle editing an order item
function editOrderItem(itemId) {
    // Fetch the specific order item data from the server
    axios.get(`http://localhost:3001/Profiles/orders/items/${itemId}`)
        .then(response => {
            const item = response.data;

            // Populate the form fields in the modal with the fetched item data
            document.getElementById('editOrderItemId').value = item.itemId;
            document.getElementById('editItemName').value = item.itemName;
            document.getElementById('editItemQuantity').value = item.quantity;
            document.getElementById('editItemPrice').value = item.price;

            // Show the edit order item modal using Bootstrap's modal functionality
            const editOrderItemModal = new bootstrap.Modal(document.getElementById('editOrderItemModal'));
            editOrderItemModal.show();
        })
        .catch(error => {
            console.error('Error fetching order item data:', error);
            alert('Failed to fetch order item data.');
        });
}

// Function to handle form submission for editing an order item
function submitEditOrderItemForm() {
    // Prevent form from submitting the default way
    event.preventDefault();

    // Get the form element
    const form = document.getElementById('editOrderItemForm');
    
    // Get form data
    const formData = new FormData(form);
    
    // Extract data from form data
    const itemId = formData.get('id');
    const itemName = formData.get('itemName');
    const quantity = parseInt(formData.get('quantity'));
    const price = parseFloat(formData.get('price'));
    
    // API endpoint URL for updating an order item
    const url = `http://localhost:3001/Profiles/orders/items/${itemId}`;
    
    // Send a PUT request to update the order item
    axios.put(url, {
        itemName: itemName,
        quantity: quantity,
        price: price
    })
    .then(response => {
        // Handle successful response (e.g., refresh order items list)
        console.log('Order item edited successfully:', response.data);
        alert('Order item edited successfully!');
        
        // Hide the edit order item modal
        const editOrderItemModal = bootstrap.Modal.getInstance(document.getElementById('editOrderItemModal'));
        editOrderItemModal.hide();
        
        // Refresh the order items list (pass the appropriate order ID)
        const orderId = response.data.orderId; // Obtain the orderId from the response data if necessary
        fetchOrderItems(orderId);
    })
    .catch(error => {
        // Handle errors (e.g., display an error message)
        console.error('Error editing order item:', error);
        alert('Failed to edit order item.');
    });
}

// Add event listener for form submission
document.getElementById('editOrderItemForm').addEventListener('submit', submitEditOrderItemForm);

// Function to handle deleting an order item
function deleteOrderItem(itemId) {
    // API endpoint URL for deleting an order item
    const url = `http://localhost:3001/Profiles/orders/items/${itemId}`;
    
    // Send a DELETE request to delete the order item
    axios.delete(url)
        .then(response => {
            // Handle successful response (e.g., refresh order items list)
            console.log('Order item deleted successfully:', response.data);
            alert('Order item deleted successfully!');
            
            // Refresh the order items list (pass the appropriate order ID)
            const orderId = response.data.orderId; // Obtain the orderId from the response data if necessary
            fetchOrderItems(orderId);
        })
        .catch(error => {
            // Handle errors (e.g., display an error message)
            console.error('Error deleting order item:', error);
            alert('Failed to delete order item.');
        });
}

// Set up event listener for the "Dishes" nav-link
document.getElementById('order-items-link').addEventListener('click', () => {
    fetchOrderItems();
});
