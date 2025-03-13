// Fetches orders data from the server
function fetchOrders() {
    axios.get('http://localhost:3001/Profiles/orders') // Adjust the URL to your endpoint
        .then(response => {
            const data = response.data;
            renderOrdersTable(data);
        })
        .catch(error => {
            console.error('Error fetching orders:', error);
            alert('Failed to fetch orders data.');
        });
}

// Function to render the orders table
function renderOrdersTable(data) {
    const container = document.getElementById('main-content');
    
    // Clear previous content
    container.innerHTML = '';
    
    // Create the Bootstrap table
    const table = document.createElement('table');
    table.classList.add('table', 'table-striped');
    
    // Create table headers
    const thead = document.createElement('thead');
    const trHeader = document.createElement('tr');
    const headers = ['Order ID', 'Customer ID', 'Order Date', 'Total Price', 'Status', 'Actions'];
    
    headers.forEach(header => {
        const th = document.createElement('th');
        th.textContent = header;
        trHeader.appendChild(th);
    });
    thead.appendChild(trHeader);
    table.appendChild(thead);
    
    // Create table body
    const tbody = document.createElement('tbody');
    
    data.forEach(order => {
        const tr = document.createElement('tr');
        
        // Add order data to the table row
        const orderIdCell = document.createElement('td');
        orderIdCell.textContent = order.orderId;
        tr.appendChild(orderIdCell);
        
        const customerIdCell = document.createElement('td');
        customerIdCell.textContent = order.customerId;
        tr.appendChild(customerIdCell);
        
        const orderDateCell = document.createElement('td');
        // Format the date to 'YYYY-MM-DD'
        const formattedDate = new Date(order.orderDate).toISOString().split('T')[0];
        orderDateCell.textContent = formattedDate;
        tr.appendChild(orderDateCell);
        
        const totalPriceCell = document.createElement('td');
        const totalPrice = parseFloat(order.totalPrice);
        totalPriceCell.textContent = !isNaN(totalPrice) ? totalPrice.toFixed(2) : 'Invalid';
        tr.appendChild(totalPriceCell);
        
        const statusCell = document.createElement('td');
        statusCell.textContent = order.status;
        tr.appendChild(statusCell);
        
        // Create action buttons cell
        const actionsCell = document.createElement('td');
        
        // Create Edit button
        const editButton = document.createElement('button');
        editButton.classList.add('btn', 'btn-primary', 'btn-sm');
        editButton.textContent = 'Edit';
        editButton.setAttribute('data-bs-toggle', 'modal');
        editButton.setAttribute('data-bs-target', '#editOrderModal');
        editButton.addEventListener('click', () => editOrder(order.orderId));
        actionsCell.appendChild(editButton);
        
        // Create Delete button
        const deleteButton = document.createElement('button');
        deleteButton.classList.add('btn', 'btn-danger', 'btn-sm');
        deleteButton.textContent = 'Delete';
        deleteButton.addEventListener('click', () => deleteOrder(order.orderId));
        actionsCell.appendChild(deleteButton);
        
        tr.appendChild(actionsCell);
        tbody.appendChild(tr);
    });
    
    table.appendChild(tbody);
    container.appendChild(table);
}

// Function to handle editing an order
function editOrder(orderId) {
    // Fetch the specific order data from the server
    axios.get(`http://localhost:3001/Profiles/orders/${orderId}`)
        .then(response => {
            const order = response.data;

            // Populate the form fields in the modal with the fetched order data
            document.getElementById('editOrderId').value = order.orderId;
            document.getElementById('editOrderCustomer').value = order.customerId;
            document.getElementById('editOrderStatus').value = order.status;
            document.getElementById('editOrderDate').value = order.orderDate;

            // Show the edit order modal using Bootstrap's modal functionality
            const editOrderModal = new bootstrap.Modal(document.getElementById('editOrderModal'));
            editOrderModal.show();
        })
        .catch(error => {
            console.error('Error fetching order data:', error);
            alert('Failed to fetch order data.');
        });
}

// Function to handle form submission for editing an order
function submitEditOrderForm() {
    // Prevent form from submitting the default way
    event.preventDefault();

    // Get the form element
    const form = document.getElementById('editOrderForm');
    
    // Get form data
    const formData = new FormData(form);
    
    // Extract data from form data
    const orderId = formData.get('id');
    const customerId = formData.get('customer');
    const status = formData.get('status');
    const date = formData.get('date');
    
    // API endpoint URL for updating an order
    const url = `http://localhost:3001/Profiles/orders/${orderId}`;
    
    // Send a PUT request to update the order
    axios.put(url, {
        customerId: customerId,
        status: status,
        date: date
    })
    .then(response => {
        // Handle successful response (e.g., refresh orders list)
        console.log('Order edited successfully:', response.data);
        alert('Order edited successfully!');
        
        // Hide the edit order modal
        const editOrderModal = bootstrap.Modal.getInstance(document.getElementById('editOrderModal'));
        editOrderModal.hide();
        
        // Refresh the list of orders
        fetchOrders();
    })
    .catch(error => {
        // Handle errors (e.g., display an error message)
        console.error('Error editing order:', error);
        alert('Failed to edit order.');
    });
}

// Add event listener for form submission
document.getElementById('editOrderForm').addEventListener('submit', submitEditOrderForm);

// Function to handle deleting an order
function deleteOrder(orderId) {
    // API endpoint URL for deleting an order
    const url = `http://localhost:3001/Profiles/orders/delete/${orderId}`;
    
    // Send a DELETE request to delete the order
    axios.delete(url)
        .then(response => {
            // Handle successful response (e.g., refresh orders list)
            console.log('Order deleted successfully:', response.data);
            alert('Order deleted successfully!');
            
            // Refresh the list of orders
            fetchOrders();
        })
        .catch(error => {
            // Handle errors (e.g., display an error message)
            console.error('Error deleting order:', error);
            alert('Failed to delete order.');
        });
}

// Fetch orders when the orders link is clicked
document.getElementById('orders-link').addEventListener('click', fetchOrders);