<?php
require_once '../auth/session.php';
require_once '../config/connection.php';
include_once '../api/queue_api.php';

session_start();

$queueNumber = $_SESSION['queue_number'];
//cancel if post request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // In your POST request handling
    $result = json_decode(updateStatus($queueNumber, "Cancelled"), true); // Decode the JSON string

    // Now you can access it as an array
    if ($result['success']) {
        $updatePosition = json_decode(updatePositionNumbers($queueNumber), true); // Decode the JSON string
        $delete = json_decode(deleteFromTempQueue($queueNumber), true); // Decode the JSON string
        //set office id to null
        $_SESSION['office_id'] = null;
        header("Location: ../user/join_queue.php");
        exit();
    }

}
else{
    //when tried to access through link
    header("Location: ../auth/index.php");
    exit();
}
