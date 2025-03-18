<?php 
require_once '../auth/session.php';
require_once '../config/connection.php';
include_once '../api/queue_api.php';
session_start();
checkLogin();
checkUser();

$userId = $_SESSION['user_id'];



if (isset($_GET['office_id'])) {
    $officeId = (int)$_GET['office_id']; // Get office ID from URL


    // $queueNumber = generateQueueNumber($officeId);
    
    if ($queueNumber) {
        echo "Generated queue number: " . $queueNumber;
    } else {
        echo "Office not found or an error occurred.";
    }
} else {
    echo "Please provide an office ID.";
}

// Handle cancellation if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel_ticket_id'])) {
    $ticketId = intval($_POST['cancel_ticket_id']);
    
    // Call the cancelTicket function from  queue_api
    require_once 'api/queue_api.php'; 
    echo cancelTicket($ticketId); 
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
<link id="nu-queuest-icon" rel="icon" href="../assets/nu_queuest.ico" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Status</title>
</head>
<body>

<!-- ****** SAMPLE ONLY ***** -->

<h2>Your Queue Nunmber</h2>

<div>
    <?php if (empty($tickets)): ?>
        <p>You have no tickets in the queue.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($tickets as $ticket): ?>
                <li>
                    <strong>Service Details:</strong> <?php echo htmlspecialchars($ticket['service_details']); ?><br>
                    <strong>Status:</strong> <?php echo htmlspecialchars($ticket['status']); ?>
                    <form method="POST" action="" style="display:inline;">
                        <input type="hidden" name="cancel_ticket_id" value="<?php echo $ticket['ticket_id']; ?>">
                        <button type="submit">Cancel Ticket</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>


<!-- Warning Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalLabel">Confirm Cancellation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to cancel this ticket? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <form method="POST" action="" id="cancelForm">
                    <input type="hidden" name="cancel_ticket_id" id="ticketIdToCancel">
                    <button type="submit" class="btn btn-danger">Confirm Cancellation</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
