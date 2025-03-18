document.getElementById('notif-button').addEventListener('click', function() {
    // Check if notifications are supported
    if ('Notification' in window) {
        // Request permission
        Notification.requestPermission().then(permission => {
            if (permission === 'granted') {
                alert("You will receive notifications when it's your turn.");
            } else {
                alert("Notification permission denied. Please enable them in your browser settings if you change your mind.");
            }
        });
    } else {
        alert("Your browser does not support notifications.");
    }
});

// Function to show notifications
function showNotification(title, options) {
    if ('Notification' in window && Notification.permission === 'granted') {
        const notification = new Notification(title, options);
        
        notification.onclick = function(event) {
            event.preventDefault(); 
            window.focus; // Adjust URL as needed
        };

        setTimeout(() => {
            notification.close();
        }, 5000); // Close after 5 seconds
    }
}

// Function to check the ticket status using the new API
function checkTicketStatus() {
    fetch('../api/get_ticket_status.php') // Call the new API file
        .then(response => response.json())
        .then(data => {
            if (data.status === 'found') {
                if (data.ticket_status === 'Currently_serving') {
                    showNotification("It's your turn!", {
                        body: 'Please proceed to the counter.',
                        // icon: 'path/to/icon.png'
                    });
                } else if (data.ticket_status === 'Cancelled') {
                    showNotification("Ticket Cancelled", {
                        body: 'Your ticket has been cancelled.',
                        // icon: 'path/to/icon.png'
                    });
                }
            } else {
                console.log(data.message); // For debugging
            }
        })
        .catch(error => console.error('Error:', error));
}

// Periodically check the ticket status
setInterval(checkTicketStatus, 5000); // Check every 5 seconds