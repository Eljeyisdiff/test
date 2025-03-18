//for the search bar
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('user-search');
    const userItems = document.querySelectorAll('.users-item');

    searchInput.addEventListener('input', function() {
        const searchTerm = searchInput.value.toLowerCase();

        userItems.forEach(function(office) {
            const userName = office.querySelector('.user-name').textContent.toLowerCase();
            const officeName = office.querySelector('.office-name').textContent.toLowerCase();

            if (officeName.includes(searchTerm) || userName.includes(searchTerm)) {
                office.style.display = '';
            } else {
                office.style.display = 'none';
            }
        });
    });
});


document.addEventListener('DOMContentLoaded', function() {
    // Filtering employees by office
    const officeFilterOptions = document.querySelectorAll('.office-filter-option');
    const userList = document.querySelector('.user-list');

    officeFilterOptions.forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            const officeId = this.getAttribute('data-office-id');

            if (!officeId) {
                // If officeId is empty or null, reload the page to show all employees
                window.location.reload();
                return;
            }

            fetch(`../api/get_emp_by_office.php?office_id=${officeId}`)
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => { throw new Error(text); });
                    }
                    return response.json();
                })
                .then(data => {
                    userList.innerHTML = ''; // Clear the user list

                    if (data.success) {
                        data.users.forEach(user => {
                            const { windowStat, statusColor } = getStatusDetails(user.window_status);
                            const userItem = `
                                <div class="user-list-element users-item" data-employee-id="${user.employee_id}">
                                    <div class="user-name">
                                        <p>${user.full_name}</p>
                                    </div>
                                    <div class="office-name">
                                        <p>${user.office_name}</p>
                                    </div>
                                    <div class="assigned-window">
                                        <p>${user.window_number}</p>
                                    </div>
                                    <div class="window-status" style="color: ${statusColor}">
                                        <p>${windowStat}</p>
                                    </div>
                                    <div class="user-action-btns">
                                        <div class="edit-user-btn action-btn" id="edit-user-btn-${user.employee_id}" onclick="openEditUserDialog(${user.employee_id})">
                                            <h3>Edit</h3>
                                        </div>
                                        <div class="delete-user-btn action-btn" onclick="openDeleteUserDialog(${user.employee_id})">
                                            <h3>Delete</h3>
                                        </div>
                                    </div>
                                </div>
                            `;
                            userList.insertAdjacentHTML('beforeend', userItem);
                        });
                    } else {
                        userList.innerHTML = '<p>No users found for this office.</p>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching users:', error);
                    userList.innerHTML = '<p>Failed to load users.</p>';
                });
        });
    });

    // Add user functionality
    window.openAddUserDialog = function() {
        document.getElementById('dialog-add-user').showModal();
    }

    window.closeAddUserDialog = function() {
        document.getElementById('dialog-add-user').close();
    }

    const addUserForm = document.getElementById('add-user-information');
    const warningMessage = document.getElementById('warning-message');
    const togglePasswordButton = document.getElementById('toggle-password');
    const passwordField = document.getElementById('password-add');

    // Handle form submission for adding a user
    if (addUserForm) {
        addUserForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Log form data
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }
            
            fetch('../api/add_employee.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text()) // Change to text to log the raw response
            .then(text => {
                console.log('Raw response:', text); // Log the raw response
                const data = JSON.parse(text); // Parse the JSON response
                if (data.status === 'success') {
                    warningMessage.style.color = 'green';
                    warningMessage.textContent = 'Employee added successfully';
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    warningMessage.style.color = 'red';
                    warningMessage.textContent = `Error: ${data.message}`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                warningMessage.style.color = 'red';
                warningMessage.textContent = 'An error occurred while adding the employee';
            });
        });
    }

    // Edit user functionality
    window.openEditUserDialog = function(employeeId) {
        fetch(`../api/get_employee_details.php?employee_id=${employeeId}`)
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => { throw new Error(text); });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const employeeName = document.getElementById('employee-name-edit');
                    const assignedOffice = document.getElementById('assigned-office-edit');
                    const assignedWindow = document.getElementById('assigned-window-edit');
                    const email = document.getElementById('email-edit');
                    const password = document.getElementById('password-edit');
                    const employeeIdField = document.getElementById('employee_id'); // Ensure this field is set

                    if (employeeName) {
                        employeeName.value = data.employee.full_name;
                    }
                    if (assignedOffice) {
                        // Set the selected option for the assigned office
                        const options = assignedOffice.options;
                        if (options) {
                            for (let i = 0; i < options.length; i++) {
                                if (options[i].value.toLowerCase() === data.employee.office_name.toLowerCase()) {
                                    options[i].selected = true;
                                    break;
                                }
                            }
                        }
                    }
                    if (assignedWindow) {
                        // Set the selected option for the assigned window
                        const options = assignedWindow.options;
                        if (options) {
                            for (let i = 0; i < options.length; i++) {
                                if (options[i].value === data.employee.window_number.toString()) {
                                    options[i].selected = true;
                                    break;
                                }
                            }
                        }
                    }
                    if (email) {
                        email.value = data.employee.email;
                    }
                    if (password) {
                        password.value = data.employee.password;
                    }
                    if (employeeIdField) {
                        employeeIdField.value = data.employee.employee_id; // Set the employee_id
                    }

                    document.getElementById('dialog-edit-user').showModal();
                } else {
                    console.error('Failed to fetch employee details:', data.error);
                    const alertBox = document.getElementById('alert-box');
                    if (alertBox) {
                        alertBox.innerHTML = `
                            <div class="alert alert-danger" role="alert">
                                ${data.error || 'Failed to fetch employee details'}
                            </div>
                        `;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const alertBox = document.getElementById('alert-box');
                if (alertBox) {
                    alertBox.innerHTML = `
                        <div class="alert alert-danger" role="alert">
                            Failed to load employee details. Please try again later.
                        </div>
                    `;
                }
            });
    }

    window.closeEditUserDialog = function() {
        document.getElementById('dialog-edit-user').close();
    }

    const editUserForm = document.getElementById('user-information');

    // Handle form submission for editing a user
    if (editUserForm) {
        editUserForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Log form data
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }
            
            fetch('../api/update_employee.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text()) // Change to text to log the raw response
            .then(text => {
                console.log('Raw response:', text); // Log the raw response
                const data = JSON.parse(text); // Parse the JSON response
                const warningMessage = document.getElementById('warning-message');
                if (data.status === 'success') {
                    warningMessage.style.color = 'green';
                    warningMessage.textContent = 'Employee details updated successfully';
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    warningMessage.style.color = 'red';
                    warningMessage.textContent = `Error: ${data.message}`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const warningMessage = document.getElementById('warning-message');
                warningMessage.style.color = 'red';
                warningMessage.textContent = 'An error occurred while updating the employee';
            });
        });
    }

    // Delete user functionality
    let deleteEmployeeId = null;

    // Function to open the delete user dialog and set the employee ID
    window.openDeleteUserDialog = function(employeeId) {
        deleteEmployeeId = employeeId;
        document.getElementById('deleteEmployeeId').value = employeeId;
        document.getElementById('dialog-delete-user').showModal();
    }

    // Function to handle the delete confirmation
    function confirmDeleteUser(e) {
        e.preventDefault();
        const form = document.getElementById('deleteEmployeeForm');
        const formData = new FormData(form);

        fetch('../api/delete_employee.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                warningMessage.style.color = 'green';
                warningMessage.textContent = 'Employee deleted successfully';
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                warningMessage.style.color = 'red';
                warningMessage.textContent = `Error: ${data.message}`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            const warningMessage = document.getElementById('warning-message');
            warningMessage.style.color = 'red';
            warningMessage.textContent = 'An error occurred while deleting the employee';
        });
    }

    const dialogDelete = document.getElementById('dialog-delete-user');
    const confirmButton = document.getElementById('confirm-delete');
    const cancelButtonDelete = dialogDelete ? dialogDelete.querySelector('.close-button') : null;
    const goBackButtonDelete = dialogDelete ? dialogDelete.querySelector('.go-back') : null;

    // Ensure elements exist before adding event listeners
    if (confirmButton) {
        confirmButton.addEventListener('click', confirmDeleteUser);
    }
    if (cancelButtonDelete) {
        cancelButtonDelete.addEventListener('click', () => {
            dialogDelete.close();
        });
    }
    if (goBackButtonDelete) {
        goBackButtonDelete.addEventListener('click', () => {
            dialogDelete.close();
        });
    }

    // Make the add user dialog draggable
    const dialogAddUser = document.getElementById('dialog-add-user');
    const dialogTitleBar = document.getElementById('dialog-add-user-title-bar');

    let isDragging = false;
    let offsetX, offsetY;

    dialogTitleBar.addEventListener('mousedown', (e) => {
        isDragging = true;
        offsetX = e.clientX - dialogAddUser.getBoundingClientRect().left;
        offsetY = e.clientY - dialogAddUser.getBoundingClientRect().top;
        dialogAddUser.style.cursor = 'move';
    });

    document.addEventListener('mousemove', (e) => {
        if (isDragging) {
            dialogAddUser.style.left = `${e.clientX - offsetX}px`;
            dialogAddUser.style.top = `${e.clientY - offsetY}px`;
        }
    });

    document.addEventListener('mouseup', () => {
        isDragging = false;
        dialogAddUser.style.cursor = 'default';
    });

    // Helper function to get status details
    function getStatusDetails(status) {
        let windowStat = 'N/A';
        let statusColor = 'black';

        if (status === 'on_break') {
            windowStat = 'On Break';
            statusColor = '#f25325'; // orange
        } else if (status === 'closed') {
            windowStat = 'Closed';
            statusColor = '#990000'; // red
        } else if (status === 'open') {
            windowStat = 'Open';
            statusColor = 'green';
        } else if (!status) {
            windowStat = 'N/A';
        }

        return { windowStat, statusColor };
    }
});

