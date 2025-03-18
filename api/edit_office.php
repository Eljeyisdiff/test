<?php
require_once '../auth/session.php';
require_once '../config/connection.php';
include_once '../api/admin_api.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Validate input
        if (!isset($_POST['office_id']) || !isset($_POST['office_name']) || !isset($_POST['office_prefix']) || !isset($_POST['office_description'])) {
            throw new Exception('Missing required fields');
        }

        $office_id = $_POST['office_id'];
        $office_name = $_POST['office_name'];
        $prefix = $_POST['office_prefix'];
        $office_description = $_POST['office_description'];
        $purposes = isset($_POST['purposes']) ? $_POST['purposes'] : array();

        // Start transaction
        $conn->begin_transaction();

        try {
            // Update office details
            $query = "UPDATE offices SET office_name = ?, prefix = ?, office_description = ? WHERE office_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('sssi', $office_name, $prefix, $office_description, $office_id);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to update office details');
            }

            // Always delete existing purposes
            $query = "DELETE FROM office_purpose WHERE office_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $office_id);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to delete existing purposes');
            }

            // Insert new purposes if any exist
            if (!empty($purposes) && $purposes[0] !== '') {
                $query = "INSERT INTO office_purpose (office_id, purpose) VALUES (?, ?)";
                $stmt = $conn->prepare($query);
                
                foreach ($purposes as $purpose) {
                    if (!empty(trim($purpose))) {
                        $stmt->bind_param('is', $office_id, $purpose);
                        if (!$stmt->execute()) {
                            throw new Exception('Failed to insert purpose');
                        }
                    }
                }
            }

            // If we got here, commit the transaction
            $conn->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Office updated successfully'
            ]);

        } catch (Exception $e) {
            $conn->rollback();
            throw $e;
        }
        
    } catch (Exception $e) {
        error_log('Error in edit_office.php: ' . $e->getMessage());
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