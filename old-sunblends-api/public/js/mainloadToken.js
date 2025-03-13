let authState = {
    username: "", 
    id: 0, 
    status: false
  };

  let listofProfile = [];

  // Function to fetch authentication status
  function fetchAuthStatus() {
    fetch('http://localhost:3001/Profiles/auth', {
      headers: {
        accessToken: localStorage.getItem('accessToken')
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        authState = { ...authState, status: false };
      } else {
        authState = { username: data.username, id: data.id, status: true };
        
      }
    })
    .catch(error => {
      console.error('Error while fetching authentication status:', error);
      authState = { ...authState, status: false };
    });
  }

  // Function to fetch list of profiles
  function fetchProfiles() {
    fetch('http://localhost:3001/Profiles')
    .then(response => response.json())
    .then(data => {
      listofProfile = data;
    })
    .catch(error => {
      console.error('Error fetching profiles:', error);
    });
  }

  // Fetch authentication status and list of profiles on page load
  window.addEventListener('DOMContentLoaded', function() {
    fetchAuthStatus();
    fetchProfiles();
  });