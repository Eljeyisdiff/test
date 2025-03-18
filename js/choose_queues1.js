// Get all "Join queue" buttons
const joinQueueButtons = document.querySelectorAll('.join-queue-btn');
const dialog = document.getElementById('dialog-confirm-queue');
const officeIdInput = document.getElementById('office-id-input');
const joinQueueForm = document.getElementById('join-queue-form');

// Add click event listener to all buttons
joinQueueButtons.forEach(button => {
    button.addEventListener('click', async function() {
        // Get office ID from button
        const officeId = this.value;

        // Set office ID in hidden input field
        officeIdInput.value = officeId;

        // Show the modal dialog
        dialog.showModal();
    });
});

// Add event listener for form submission
joinQueueForm.addEventListener('submit', async function (event) {
    event.preventDefault(); // Prevent default form submission

    const formData = new FormData(joinQueueForm);
    const jsonData = {
        user_id: formData.get('user_id'),
        office_id: formData.get('office_id'),
        service_details: formData.get('other_reason') // Get the manually entered reason
    };

    try {
        const response = await fetch('../api/join_queue_api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(jsonData)
        });
        const result = await response.json();

        if (result.success) {
            // Redirect to the queue view page after successful join
            window.location.href = `queueview_user.php?ticket_id=${result.ticket_id}`;
        } else {
            alert(result.message || 'Failed to join the queue.');
        }
    } catch (error) {
        console.error('Error joining queue:', error);
    }
});

// Add event listener for close button in modal
const closeButton = document.querySelector('.close-button');
closeButton.addEventListener('click', function() {
    dialog.close();
});