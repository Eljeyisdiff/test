<?php
require_once '../auth/session.php';
require_once '../config/connection.php';
include_once '../api/queue_api.php';

session_start();

$office_id = $_SESSION['office_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    

    if (isset($_POST['currentTicket'])) {
        $queueNumber = $_POST['currentTicket'];
        // Cancel the current ticket
        $result = json_decode(updateStatus($queueNumber, "Cancelled"), true);

        if ($result['success']) {
            $remove = removeCurrentTicket();

            if ($remove) {
                //store success message in session
                $_SESSION['openMessage'] = "Ticket cancelled successfully. Serving next ticket.";
                $nextTicket = serveNextInQueue($office_id);
                header("Location: ../employee/employee_dashboard.php");
                exit();
            }
        }

    } elseif (isset($_POST['cancelTempQueueTicket'])) {
        $queueNumber = $_POST['cancelTempQueueTicket'];
        // Cancel the temp queue ticket
        $result = json_decode(updateStatus($queueNumber, "Cancelled"), true);

        if ($result['success']) {
            // Remove from temp_queue
            $stmt = $conn->prepare("DELETE FROM temp_queue WHERE queue_number = ?");
            $stmt->bind_param("s", $queueNumber);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $_SESSION['openMessage'] = "Ticket removed from queue.";
                header("Location: ../employee/employee_dashboard.php");
                exit();
            } else {
                echo "Failed to remove from temp_queue.";
            }
        } else {
            echo "Failed to update ticket status.";
        }
    }
} else {
    header("Location: ../auth/index.php");
    exit();
}