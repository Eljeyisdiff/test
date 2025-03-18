<?php
session_start();
require_once '../config/connection.php';
require_once '../queue_api.php'; // Include the queue API functions

// Ensure the queue number is in the session
if (!isset($_SESSION['queue_number'])) {
    echo json_encode(['status' => 'error', 'message' => 'Queue number not found.']);
    exit();
}

// Get the ticket details using the queue API function
$queueNumber = $_SESSION['queue_number'];
$ticketDetails = getTicketDetails($queueNumber); // Call your function from queue_api

if ($ticketDetails) {
    echo json_encode(['status' => 'found', 'ticket_status' => $ticketDetails['status']]);
} else {
    echo json_encode(['status' => 'not_found', 'message' => 'No ticket found for this queue number.']);
}