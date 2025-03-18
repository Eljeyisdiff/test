<?php
require_once '../config/connection.php';
header('Content-Type: application/json');

if (isset($_GET['office_id'])) {
    try {
        $office_id = $_GET['office_id'];
        
        // Fetch office details
        $query = "SELECT * FROM offices WHERE office_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $office_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $office = $result->fetch_assoc();

        // Fetch purposes
        $query = "SELECT purpose FROM office_purpose WHERE office_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $office_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $purposes = [];
        while ($row = $result->fetch_assoc()) {
            $purposes[] = $row['purpose'];
        }
        
        echo json_encode([
            'success' => true,
            'office' => $office,
            'purposes' => $purposes
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Office ID not provided'
    ]);
}