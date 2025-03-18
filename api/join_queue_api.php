<?php
// join_queue_api.php
require_once '../config/connection.php';
require_once 'queue_api.php';
session_start();

header('Content-Type: application/json');


try {
    $input = json_decode(file_get_contents('php://input'), true);
    $userId = $input['user_id'];
    $officeId = $input['office_id'];
    $serviceDetails = $input['service_details'] === 'other' ? $input['other_purpose'] : $input['service_details'];

    // Generate the ticket
    $ticketResult = generateTicket($userId, $serviceDetails, $officeId);
    
    if ($ticketResult['success']) {
        // Insert into temp_queue using the generated queue number and ticket ID
        $tempQueueResult = insertIntoTempQueue($officeId, $ticketResult['queue_number'], $ticketResult['ticket_id']);
        
        //store queue number in session
        $_SESSION['queue_number'] = $ticketResult['queue_number'];
        $_SESSION['office_id'] = $officeId; //store office id in session


        if ($tempQueueResult['success']) {
            echo json_encode(['success' => true, 'queue_number' => $ticketResult['queue_number'], 'ticket_id' => $ticketResult['ticket_id']]);
        } else {
            echo json_encode(['success' => false, 'message' => $tempQueueResult['error']]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => $ticketResult['error']]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>