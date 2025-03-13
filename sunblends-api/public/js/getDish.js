function fetchDishes() {
    axios.get('http://localhost:3001/Profiles/menus') // Adjust the URL to your endpoint
        .then(response => {
            const data = response.data;
            renderDishesTable(data);
        })
        .catch(error => {
            console.error('Error fetching dishes:', error);
        });
}

function renderDishesTable(data) {
    const container = document.getElementById('main-content');

    // Create a Bootstrap table
    const table = document.createElement('table');
    table.classList.add('table', 'table-striped');

    // Create table headers
    const thead = document.createElement('thead');
    const tr = document.createElement('tr');
    const headers = ['ID', 'Dish Name', 'Price', 'Calories', 'Category', 'Actions'];
    headers.forEach(header => {
        const th = document.createElement('th');
        th.textContent = header;
        tr.appendChild(th);
    });
    thead.appendChild(tr);
    table.appendChild(thead);

    // Create table body
    const tbody = document.createElement('tbody');
    data.forEach(dish => {
        const tr = document.createElement('tr');

        // Add dish data
        const idCell = document.createElement('td');
        idCell.textContent = dish.id;
        tr.appendChild(idCell);

        const nameCell = document.createElement('td');
        nameCell.textContent = dish.Dish;
        tr.appendChild(nameCell);

        const priceCell = document.createElement('td');
        // Check if dish.price is a number and use toFixed if possible
        priceCell.textContent = dish.Price.toFixed(2);      
        tr.appendChild(priceCell);

        const caloriesCell = document.createElement('td');
        caloriesCell.textContent = dish.Calories;
        tr.appendChild(caloriesCell);

        const categoryCell = document.createElement('td');
        categoryCell.textContent = dish.Category;
        tr.appendChild(categoryCell);

        // Add action buttons
        const actionsCell = document.createElement('td');
        const editButton = document.createElement('button');
        editButton.classList.add('btn', 'btn-primary', 'btn-sm');
        editButton.textContent = 'Edit';
        editButton.onclick = () => editDish(dish.id);

        const deleteButton = document.createElement('button');
        deleteButton.classList.add('btn', 'btn-danger', 'btn-sm');
        deleteButton.textContent = 'Delete';
        deleteButton.onclick = () => deleteDish(dish.id);

        actionsCell.appendChild(editButton);
        actionsCell.appendChild(deleteButton);
        tr.appendChild(actionsCell);

        tbody.appendChild(tr);
    });
    table.appendChild(tbody);

    // Append the table to the container
    container.innerHTML = ''; // Clear previous content
    container.appendChild(table);
}


function editDish(dishId) {
    // Fetch the specific dish data using Axios
    axios.get(`http://localhost:3001/Profiles/menus/${dishId}`)
        .then(response => {
            const dish = response.data;
            // Populate the form fields with the dish data
            document.getElementById('editDishId').value = dish.id;
            document.getElementById('editDishName').value = dish.Dish;
            document.getElementById('editDishDescription').value = dish.description;
            document.getElementById('editDishPrice').value = dish.Price;

            // Show the edit dish modal
            const editDishModal = new bootstrap.Modal(document.getElementById('editDishModal'));
            editDishModal.show();
        })
        .catch(error => {
            console.error('Error fetching dish data:', error);
            alert('Failed to fetch dish data.');
        });
}

function submitEditDishForm() {
    // Get the form data
    const formData = new FormData(document.getElementById('editDishForm'));
    
    // Extract dish ID
    const dishId = formData.get('id');
    
    // Create a payload object with the updated data
    const payload = {
        name: formData.get('name'),
        description: formData.get('description'),
        price: formData.get('price')
    };

    // Make an HTTP request to update the dish data on the server
    axios.put(`http://localhost:3001/Profiles/menus/${dishId}`, payload)
        .then(response => {
            // Handle success
            alert('Dish updated successfully!');
            // Optionally, refresh the dishes data in the table after editing
            fetchDishes();
        })
        .catch(error => {
            // Handle error
            alert('Failed to update dish.');
            console.error(error);
        });

    // Hide the modal
    const editDishModal = new bootstrap.Modal(document.getElementById('editDishModal'));
    editDishModal.hide();
}

function deleteDish(dishId) {
    // Make an HTTP DELETE request to remove the dish from the server
    axios.delete(`http://localhost:3001/Profiles/menus/${dishId}`)
        .then(() => {
            // Handle success
            alert('Dish deleted successfully!');
            // Refresh the list of dishes
            fetchDishes();
        })
        .catch(error => {
            // Handle error
            alert('Failed to delete dish.');
            console.error(error);
        });
}

// Set up event listener for the "Dishes" nav-link
document.getElementById('dishes-link').addEventListener('click', () => {
    fetchDishes();
});
