<?php
require_once '../config/connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_SESSION['user_id'];

    // Check if the user is already in a queue for the given office
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM temp_queue WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        $response['inQueue'] = true;
    } else{
        $response['inQueue'] = false;
    }
}
else{
    //when tried to access through link
    header("Location: ../auth/index.php");
    exit();
}
header('Content-Type: application/json');
echo json_encode($response);
?>
