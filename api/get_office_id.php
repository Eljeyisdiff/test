<?php
require_once '../config/connection.php';
session_start();

header('Content-Type: application/json');

$response = [];
if (isset($_SESSION['office_id'])) {
    $response['office_id'] = $_SESSION['office_id'];
} else {
    $response['office_id'] = null; // or handle the case when the office ID is not set
}

echo json_encode($response);
?>