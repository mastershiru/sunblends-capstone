// Fetches account data from the server
function fetchAccounts() {
    axios.get('http://localhost:3001/Profiles/accounts') // Adjust the URL to your endpoint
        .then(response => {
            const data = response.data;
            renderAccountsTable(data);
        })
        .catch(error => {
            console.error('Error fetching accounts:', error);
        });
}

// Renders the accounts data in a Bootstrap table
function renderAccountsTable(data) {
    const container = document.getElementById('main-content');

    // Create a Bootstrap table
    const table = document.createElement('table');
    table.classList.add('table', 'table-striped');

    // Create table headers
    const thead = document.createElement('thead');
    const tr = document.createElement('tr');
    const headers = ['ID', 'Username', 'Email', 'Actions'];
    headers.forEach(header => {
        const th = document.createElement('th');
        th.textContent = header;
        tr.appendChild(th);
    });
    thead.appendChild(tr);
    table.appendChild(thead);

    // Create table body
    const tbody = document.createElement('tbody');
    data.forEach(account => {
        const tr = document.createElement('tr');

        // Add account data
        const idCell = document.createElement('td');
        idCell.textContent = account.id;
        tr.appendChild(idCell);

        const usernameCell = document.createElement('td');
        usernameCell.textContent = account.username;
        tr.appendChild(usernameCell);

        const emailCell = document.createElement('td');
        emailCell.textContent = account.email;
        tr.appendChild(emailCell);

        // Add action buttons
        const actionsCell = document.createElement('td');
        const editButton = document.createElement('button');
        editButton.classList.add('btn', 'btn-primary', 'btn-sm');
        editButton.textContent = 'Edit';
        editButton.onclick = () => editAccount(account.id);

        const deleteButton = document.createElement('button');
        deleteButton.classList.add('btn', 'btn-danger', 'btn-sm');
        deleteButton.textContent = 'Delete';
        deleteButton.onclick = () => deleteAccount(account.id);

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

// Function to open the edit account modal and populate it with the selected account data
function editAccount(accountId) {
    // Fetch the specific account data using Axios
    axios.get(`http://localhost:3001/Profiles/accounts/${accountId}`)
        .then(response => {
            const account = response.data;
            // Populate the form fields with the account data
            document.getElementById('editUserId').value = account.id;
            document.getElementById('editUsername').value = account.username;
            document.getElementById('editEmail').value = account.email;

            // Show the edit account modal
            const editAccountModal = new bootstrap.Modal(document.getElementById('editAccountModal'));
            editAccountModal.show();
        })
        .catch(error => {
            console.error('Error fetching account data:', error);
            alert('Failed to fetch account data.');
        });
}


// Function to submit the edited account form
function submitEditForm() {
    // Get the form data
    const formData = new FormData(document.getElementById('editAccountForm'));
    
    // Extract user ID
    const userId = formData.get('id');
    
    // Create a payload object with the updated data
    const payload = {
        username: formData.get('username'),
        email: formData.get('email')
    };

    // Make an HTTP request to update the account data on the server
    axios.put(`http://localhost:3001/Profiles/accounts:${userId}`, payload)
        .then(response => {
            // Handle success
            alert('Account updated successfully!');
            // Optionally, refresh the accounts data in the table after editing
            // fetchAccounts();
        })
        .catch(error => {
            // Handle error
            alert('Failed to update account.');
            console.error(error);
        });

    // Hide the modal
    const editAccountModal = new bootstrap.Modal(document.getElementById('editAccountModal'));
    editAccountModal.hide();
}

function deleteAccount(accountId) {
    // Implement account deletion logic
    console.log('Delete account:', accountId);
}

// Set up event listener for the "Account" nav-link
document.getElementById('account-link').addEventListener('click', () => {
    fetchAccounts();
});