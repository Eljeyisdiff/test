//for search
document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("search-office");
  const officeEntries = document.querySelectorAll(".office-entry");

  // Event listener for the search input
  searchInput.addEventListener("input", function() {
    const searchTerm = searchInput.value.toLowerCase();

    // Loop through all office entries
    officeEntries.forEach((office) => {
      const officeName = office.querySelector(".title").textContent.toLowerCase();

      // Show or hide office based on whether it matches the search term
      if (officeName.includes(searchTerm)) {
        office.style.display = "block"; // Show office
      } else {
        office.style.display = "none"; // Hide office
      }
    });
  });
});

document.getElementById('addEmployeeBtn').addEventListener('click', function() {
  
  // Load office data
  loadOffices();
  // Open the dialog
  document.getElementById('addEmployee').showModal();

});

// Load offices function (make sure this is included as well)
function loadOffices() {
  const warningMessage = document.getElementById('warning-message');

  fetch('../api/fetch_offices.php')
      .then(response => response.json())
      .then(data => {
        console.log(data); // Check the data format here
        const officeSelect = document.getElementById('newEmployeeOffice');
        console.log(officeSelect); // Ensure this is not null
          officeSelect.innerHTML = ''; // Clear existing options

          if (data.error) {
              warningMessage.textContent = data.error; // Display error message
              warningMessage.style.color = 'red'; // Set text color to red
          } else {
              data.forEach(office => {
                  const option = document.createElement('option');
                  option.value = office.office_id;
                  option.textContent = office.office_name;
                  officeSelect.appendChild(option); // Add option to the select element
              });
          }
      })
      .catch(error => {
          console.error('Error fetching offices:', error);
      });
}
