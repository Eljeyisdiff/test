<?php
require_once '../config/connection.php';

global $conn;

$data = json_decode(file_get_contents("php://input"), true);
if (isset($data['office_id'], $data['purpose_name'])) {
    $office_id = intval($data['office_id']);
    $purpose_name = $data['purpose_name'];

    $stmt = $conn->prepare("INSERT INTO office_purpose (office_id, purpose) VALUES (?, ?)");
    $stmt->bind_param("is", $office_id, $purpose_name);

    if ($stmt->execute()) {
        $response = ['success' => true, 'purpose_id' => $conn->insert_id];
    } else {
        $response = ['success' => false, 'error' => $stmt->error];
    }
    echo json_encode($response);
}
?>