document.querySelector("#register-form").addEventListener("submit", function(event) {
    event.preventDefault(); // Prevent the default form submission
    validateForm(); // Call the validateForm function
});
  
  function validateForm() {
    const username = document.querySelector("#username1").value.trim();
    const password = document.querySelector("#password1").value.trim();
    const email = document.querySelector("#email1").value.trim();
  
    // Validation rules
    const errors = {};
    if (username === "") {
      errors.username = "Username is required";
    }
    if (password === "") {
      errors.password = "Password is required";
    }
    if (email === "") {
      errors.email = "Email is required";
    }
  
    // Display errors
    Object.keys(errors).forEach((fieldName) => {
      const errorMessage = errors[fieldName];
      document.getElementById(`${fieldName}Error`).textContent = errorMessage;
    });
  
    // If no errors, submit the form
    if (Object.keys(errors).length === 0) {
      submitForm({ username, password, email });
    }
  }
  
  function submitForm(data) {
    fetch("http://localhost:3001/Profiles", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify(data)
    })
    .then(response => {
      if (!response.ok) {
        throw new Error("Registration failed");
      }
      console.log("Registration successful");
      navigateHome();
    })
    .catch(error => {
      console.error("Error registering:", error);
    });
  }