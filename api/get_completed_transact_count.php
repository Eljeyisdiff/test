<?php
require_once '../config/connection.php';
header('Content-Type: application/json');

try {
    $query = "SELECT COUNT(*) as total FROM transactions WHERE status = 'completed'";
    $result = $conn->query($query);

    if ($result) {
        $row = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'total' => $row['total']
        ]);
    } else {
        throw new Exception('Failed to fetch total completed transactions');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>