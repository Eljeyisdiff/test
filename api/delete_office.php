<?php
require_once '../auth/session.php';
require_once '../config/connection.php';
include_once '../api/admin_api.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Validate input
        if (!isset($_POST['office_id'])) {
            throw new Exception('Missing office ID');
        }

        $office_id = $_POST['office_id'];

        // Start transaction
        $conn->begin_transaction();

        try {
            // Delete office
            $query = "DELETE FROM offices WHERE office_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $office_id);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to delete office');
            }

            // If we got here, commit the transaction
            $conn->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Office deleted successfully'
            ]);

        } catch (Exception $e) {
            $conn->rollback();
            throw $e;
        }
        
    } catch (Exception $e) {
        error_log('Error in delete_office.php: ' . $e->getMessage());
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request method'
    ]);
}