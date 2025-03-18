<?php
require_once '../config/connection.php';
include_once 'admin_api.php';

global $conn;

$data = json_decode(file_get_contents("php://input"), true);
if (isset($data['purpose_id'], $data['new_name'])) {
    $purpose_id = intval($data['purpose_id']);
    $new_name = $data['new_name'];

    $stmt = $conn->prepare("UPDATE office_purpose SET purpose = ? WHERE purpose_id = ?");
    $stmt->bind_param("si", $new_name, $purpose_id);

    $response = ['success' => $stmt->execute()];
    echo json_encode($response);
}
?>