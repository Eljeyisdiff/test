// Function to add a new purpose field for Add Office form
function addNewPurposeField() {
    var container = document.getElementById('purposes-container');
    var div = document.createElement('div');
    div.className = 'purpose-field';
    div.innerHTML = `
        <input type="text" class="form-control mb-2" name="purposes[]" required>
        <button type="button" class="btn btn-danger remove-purpose-btn" onclick="removePurposeField(this)">Remove</button>
    `;
    container.appendChild(div);
}

// Function to remove a purpose field
function removePurposeField(button) {
    var field = button.parentElement;
    field.remove();
}

// Initialize event listeners when document loads
document.addEventListener('DOMContentLoaded', function() {
    // Add button for new office form
    const addNewPurposeBtn = document.querySelector('#addOfficeForm .add-purpose-btn');
    if (addNewPurposeBtn) {
        addNewPurposeBtn.onclick = addNewPurposeField;
    }

    // Handle form submission for adding a new office
    document.getElementById('addOfficeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('../api/add_office.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('warning-message').innerHTML = `
                    <div class="alert alert-success" role="alert">
                        ${data.message}
                    </div>
                `;
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                document.getElementById('warning-message').innerHTML = `
                    <div class="alert alert-danger" role="alert">
                        ${data.error || 'Error adding office'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('warning-message').innerHTML = `
                <div class="alert alert-danger" role="alert">
                    An error occurred while adding the office
                </div>
            `;
        });
    });
});

// Function to add a new purpose field for Edit Office form
function addEditPurposeField(value = '') {
    var container = document.getElementById('editPurposesContainer');
    var div = document.createElement('div');
    div.className = 'purpose-field';
    div.innerHTML = `
        <input type="text" class="form-control mb-2" name="purposes[]" value="${value}" required>
        <button type="button" class="btn btn-danger remove-purpose-btn" onclick="removePurposeField(this)"><i class="material-symbols-outlined">close</i></button>
    `;
    container.appendChild(div);
}

// Function to remove a purpose field
function removePurposeField(button) {
    var field = button.parentElement;
    field.remove();
}

// Function to open the edit office dialog and populate the form
function openEditOfficeDialog(officeId) {
    fetch(`../api/get_office_details.php?office_id=${officeId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                document.getElementById('officeId').value = data.office.office_id;
                document.getElementById('officeName').value = data.office.office_name;
                document.getElementById('officePrefix').value = data.office.prefix;
                document.getElementById('officeDescription').value = data.office.office_description;

                // Clear existing purposes
                var container = document.getElementById('editPurposesContainer');
                container.innerHTML = '';

                // Populate purposes
                data.purposes.forEach(purpose => addEditPurposeField(purpose));

                document.getElementById('editOfficeDialog').showModal();
            } else {
                console.error('Failed to fetch office details:', data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('alert-box').innerHTML = `
                <div class="alert alert-danger" role="alert">
                    Failed to load office details. Please try again later.
                </div>
            `;
        });
}
// Initialize event listeners when document loads
document.addEventListener('DOMContentLoaded', function() {
    // Add button for edit office form
    const editPurposeBtn = document.querySelector('#editOfficeForm .add-purpose-btn');
    if (editPurposeBtn) {
        editPurposeBtn.onclick = () => addEditPurposeField();
    }

    // Handle form submission for editing an office
    document.getElementById('editOfficeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('../api/edit_office.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('alert-box').innerHTML = `
                    <div class="alert alert-success" role="alert">
                        ${data.message}
                    </div>
                `;
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                document.getElementById('alert-box').innerHTML = `
                    <div class="alert alert-danger" role="alert">
                        ${data.error || 'Error updating office'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('alert-box').innerHTML = `
                <div class="alert alert-danger" role="alert">
                    An error occurred while updating the office
                </div>
            `;
        });
    });
});

// Function to close the edit office dialog
function closeEditOfficeDialog() {
    document.getElementById('editOfficeDialog').close();
}

document.addEventListener('DOMContentLoaded', function() {
    // Function to open the delete office dialog
    function openDeleteOfficeDialog(officeId) {
        document.getElementById('deleteOfficeId').value = officeId;
        document.getElementById('deleteOfficeDialog').showModal();
    }

    // Function to close the delete office dialog
    function closeDeleteOfficeDialog() {
        document.getElementById('deleteOfficeDialog').close();
    }

    // Handle form submission for deleting an office
    const deleteOfficeForm = document.getElementById('deleteOfficeForm');
    if (deleteOfficeForm) {
        deleteOfficeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('../api/delete_office.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('alert-box').innerHTML = `
                        <div class="alert alert-success" role="alert">
                            ${data.message}
                        </div>
                    `;
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    document.getElementById('alert-box').innerHTML = `
                        <div class="alert alert-danger" role="alert">
                            ${data.error || 'Error deleting office'}
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('alert-box').innerHTML = `
                    <div class="alert alert-danger" role="alert">
                        An error occurred while deleting the office
                    </div>
                `;
            });
        });
    }

    // Expose functions to global scope
    window.openDeleteOfficeDialog = openDeleteOfficeDialog;
    window.closeDeleteOfficeDialog = closeDeleteOfficeDialog;
});

//for the search bar
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('officeSearchInput');
    const officeItems = document.querySelectorAll('.office-item');

    searchInput.addEventListener('input', function() {
        const searchTerm = searchInput.value.toLowerCase();

        officeItems.forEach(function(office) {
            const officeName = office.querySelector('.office-name').textContent.toLowerCase();
            const officeDesc = office.querySelector('.office-desc').textContent.toLowerCase();

            if (officeName.includes(searchTerm) || officeDesc.includes(searchTerm)) {
                office.style.display = '';
            } else {
                office.style.display = 'none';
            }
        });
    });
});
