document.getElementById('registration-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent default form submission

    const form = event.target; // Get the form element
    const formData = new FormData(form); // Create FormData object from the form

    fetch('/register', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.redirect_url) {
            // Handle redirect if registration is successful
            window.location.href = data.redirect_url;
        } else if (data.errors) {
            // Show validation errors
            alert(Object.values(data.errors).join('\n'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});
