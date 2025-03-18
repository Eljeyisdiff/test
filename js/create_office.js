document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.edit-office-btn').forEach(button => {
        button.addEventListener('click', function () {
            const officeId = this.getAttribute('data-id');
            const officeName = this.getAttribute('data-name');
            const officePrefix = this.getAttribute('data-prefix');
            const officeDescription = this.getAttribute('data-description');

            // Populate the modal fields
            document.getElementById('officeId').value = officeId;
            document.getElementById('officeName').value = officeName;
            document.getElementById('officePrefix').value = officePrefix;
            document.getElementById('officeDescription').value = officeDescription;

            // Fetch purposes for this office and populate the purpose list
            fetchPurposes(officeId);

            // Show the dialog
            document.getElementById('editOfficeDialog').showModal();
        });
    });
});

//delete office
document.querySelectorAll('.deletebtn').forEach(button => {
    button.addEventListener('click', function () {
        const officeId = this.value;

        // Make an AJAX call to delete the office
        fetch('../api/delete_office.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `office_id=${officeId}`
        })
        .then(response => response.json())
        .then(data => {
            const alertBox = document.getElementById('alertBox');
            let alertElement;

            if (data.success) {
                alertElement = `
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
            } else {
                alertElement = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
            }

            // Inject the alert into the DOM
            alertBox.innerHTML = alertElement;

            // Automatically hide the alert after 3 seconds (3000 milliseconds)
            setTimeout(() => {
                const alert = document.querySelector('.alert');
                if (alert) {
                    alert.classList.remove('show'); // Start fade-out
                    setTimeout(() => alert.remove(), 150); // Remove from DOM after fading
                }
            }, 800);

            // Optionally, remove the deleted office from the DOM
            if (data.success) {
                document.getElementById(`office-id-${officeId}`).remove();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
});


//#region Add Office Dialog
// Add New Office Modal trigger
document.getElementById('addOfficeBtn').addEventListener('click', function () {
    document.getElementById('addOfficeDialog').showModal();
});

function addPurposeField() {
    const container = document.getElementById('purposes-container');
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'purposes[]';
    input.placeholder = 'Another purpose';
    input.classList.add('form-control', 'mt-2'); // Add Bootstrap classes for styling
    container.appendChild(input);
}

document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById('addOfficeForm');

    form.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission

        const formData = new FormData(form); // Collect form data

        fetch('../api/add_office.php', { // Point to add_office.php
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            let alertElement;

            if (data.success) {
                alertElement = `
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                
                //close the dialog if the submission was successful
                document.getElementById('addOfficeDialog').close();
                 // Reload the view_offices page after a short delay (optional)
                 setTimeout(() => {
                    location.href = '../admin/admin_officesview.php'; 
                }, 2000);
            } else {
                alertElement = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
            }

            // Display the alert message in the warning-message div
            const warningMessageDiv = document.getElementById('alertBox');
            warningMessageDiv.innerHTML = alertElement;

            // Automatically dismiss the alert after 2 seconds
            setTimeout(() => {
                warningMessageDiv.innerHTML = '';
            }, 2000);
        })
        .catch(error => {
            console.error('Error:', error);
            const warningMessageDiv = document.getElementById('alertBox');
            warningMessageDiv.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    An error occurred. Please try again.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;

            // Automatically dismiss the alert after 2 seconds
            setTimeout(() => {
                warningMessageDiv.innerHTML = '';
            }, 2000);
        });
    });
});

//#endregion

//for search
document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("search-office");
    const officeEntries = document.querySelectorAll(".office-dt-element");
  
    // Event listener for the search input
    searchInput.addEventListener("input", function() {
      const searchTerm = searchInput.value.toLowerCase();
  
      // Loop through all office entries
      officeEntries.forEach((office) => {
        const officeName = office.querySelector(".office-name").textContent.toLowerCase();
  
        // Show or hide office based on whether it matches the search term
        if (officeName.includes(searchTerm)) {
          office.style.display = "block"; // Show office
        } else {
          office.style.display = "none"; // Hide office
        }
      });
    });
  });
  
//FOR EDITING OFFICE AND PURPOSES
// #region Edit Office Dialog

// Handle edit form submission
document.getElementById('editOfficeForm').addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent the default form submission

    const formData = new FormData(this); // Create FormData object from the form

    // Send the form data using AJAX (fetch API)
    fetch('../api/edit_office.php', { // Ensure this points to the correct PHP file handling the update
        method: 'POST',
        body: formData
    })
    .then(response => response.json()) // Parse the JSON response
    .then(data => {
        // Get the warning message element inside the modal
        const warningMessageDiv = document.getElementById('alert-box');

        // Check the result of the update operation
        if (data.success) {
            // Display success message
            warningMessageDiv.innerHTML = 'Office updated successfully!';
            warningMessageDiv.style.color = 'green'; // Set color to green for success
        } else {
            // Display error message
            warningMessageDiv.innerHTML = 'Failed to update office: ' + data.message;
            warningMessageDiv.style.color = 'red'; // Set color to red for failure
        }

        // Keep the modal open and show the message
        document.getElementById('editOfficeDialog').showModal();
    })
    .catch(error => {
        console.error('Error:', error);
        // Handle any additional errors (optional)
    });
});

//load purposes
function fetchPurposes(officeId) {
    fetch(`../api/purposes_api.php?office_id=${officeId}`)
        .then(response => response.json())
        .then(data => {
            const purposeList = document.getElementById("purposeList");
            purposeList.innerHTML = ""; // Clear existing purposes

            if (data[officeId]) {
                data[officeId].forEach(purpose => {
                    const purposeItem = document.createElement("div");
                    purposeItem.className = "purpose-item d-flex justify-content-between align-items-center mt-2";
                    purposeItem.dataset.purposeId = purpose.id; // Store purpose ID

                    purposeItem.innerHTML = `
                        <span>${purpose}</span>
                        <div>
                            <button type="button" class="btn btn-sm btn-danger" onclick="removePurpose(this)">Remove</button>
                        </div>
                    `;

                    purposeList.appendChild(purposeItem);
                });
            }
        })
        .catch(error => console.error('Error fetching purposes:', error));
}

//edit purposes
// function editPurpose(button) {
//     const purposeItem = button.parentElement.parentElement;
//     const purposeId = purposeItem.dataset.purposeId;
//     const purposeName = purposeItem.querySelector("span");

//     const newPurposeName = prompt("Edit purpose:", purposeName.textContent);
//     if (newPurposeName !== null && newPurposeName.trim() !== "") {
//         purposeName.textContent = newPurposeName;

//         fetch('../api/edit_purpose.php', {
//             method: 'POST',
//             headers: { 'Content-Type': 'application/json' },
//             body: JSON.stringify({ purpose_id: purposeId, new_name: newPurposeName })
//         })
//         .then(response => response.json())
//         .then(data => {
//             if (data.success) {
//                 console.log('Purpose updated successfully');
//             } else {
//                 console.error('Error updating purpose:', data.error);
//             }
//         })
//         .catch(error => console.error('Error:', error));
//     }
// }

//remove purposes
function removePurpose(button) {
    const purposeItem = button.parentElement.parentElement;
    const purposeId = purposeItem.dataset.purposeId;

    // Remove purpose from UI
    purposeItem.remove();

    // Send delete request to server
    fetch('../api/remove_purpose.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ purpose_id: purposeId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Purpose removed successfully');
        } else {
            console.error('Error removing purpose:', data.error);
        }
    })
    .catch(error => console.error('Error:', error));
}

//add purposes
function addPurpose() {
    const newPurposeInput = document.getElementById("newPurpose");
    const officeId = document.getElementById("officeId").value;

    if (newPurposeInput.value.trim()) {
        const newPurposeName = newPurposeInput.value.trim();

        fetch('../api/add_purpose.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ office_id: officeId, purpose_name: newPurposeName })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const purposeItem = document.createElement("div");
                purposeItem.className = "purpose-item d-flex justify-content-between align-items-center mt-2";
                purposeItem.dataset.purposeId = data.purpose_id;

                purposeItem.innerHTML = `
                    <span>${newPurposeName}</span>
                    <div>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removePurpose(this)">Remove</button>
                    </div>
                `;

                document.getElementById("purposeList").appendChild(purposeItem);
                newPurposeInput.value = ""; // Clear input
            } else {
                console.error('Error adding purpose:', data.error);
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

//#endregion

//close modal
function closeEditOfficeDialog() {
    document.getElementById('editOfficeDialog').close(); // Close the dialog
    location.reload(); // Reload the page
}

// Hide the message after 3 seconds
document.addEventListener("DOMContentLoaded", function() {
    setTimeout(function() {
        var messageDiv = document.getElementById("office-message");
        if (messageDiv) {
            messageDiv.style.display = "none";
        }
    }, 3000); // 3000 milliseconds = 3 seconds
  });