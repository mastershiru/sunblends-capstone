document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('otp-verification-form');
    if (form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            const formData = new FormData(form); // Create FormData object from form

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    alert(data.message); // Display success message
                } else if (data.errors) {
                    alert(Object.values(data.errors).join('\n')); // Display validation errors
                }
            })
            .catch(error => console.error('Error:', error));
        });
    } else {
        console.error('Form element not found');
    }
});
