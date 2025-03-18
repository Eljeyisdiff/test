function addPurpose() {
    const container = document.getElementById('purposes-container');
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'purposes[]';
    input.placeholder = 'Another purpose';
    container.appendChild(input);
}

// for edit click
document.querySelectorAll('.edit-office-btn').forEach(button => {
    button.addEventListener('click', function () {
        const officeId = this.getAttribute('data-id');
        const officeName = this.getAttribute('data-name');
        const officeDescription = this.getAttribute('data-description');
        const officePrefix = this.getAttribute('data-prefix'); // Add this if you're using it

        // Populate the modal fields
        document.getElementById('officeId').value = officeId;
        document.getElementById('officeName').value = officeName;
        document.getElementById('officeDescription').value = officeDescription;
        document.getElementById('officePrefix').value = officePrefix; // Populate prefix

        // Show the modal
        document.getElementById('editOfficeDialog').showModal(); // Use showModal() for dialog elements
    });
}); 


