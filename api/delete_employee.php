<?php
require '../config/connection.php';

header('Content-Type: application/json');

// Ensure the request method is POST and the _method is DELETE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method']) && $_POST['_method'] === 'DELETE') {
    $employee_id = isset($_POST['employee_id']) ? intval($_POST['employee_id']) : 0;

    if ($employee_id) {
        try {
            // Start a transaction
            $conn->begin_transaction();

            // Fetch the user_id associated with the employee_id
            $sql = "SELECT user_id FROM employees WHERE employee_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $employee_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $employee = $result->fetch_assoc();
                $user_id = $employee['user_id'];
            } else {
                throw new Exception('Employee not found');
            }
            $stmt->close();

            // Delete from the employees table
            $sql = "DELETE FROM employees WHERE employee_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $employee_id);
            if (!$stmt->execute()) {
                throw new Exception('Failed to delete from employees table: ' . $stmt->error);
            }
            $stmt->close();

            // Delete from the users table
            $sql = "DELETE FROM users WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $user_id);
            if (!$stmt->execute()) {
                throw new Exception('Failed to delete from users table: ' . $stmt->error);
            }
            $stmt->close();

            // Commit the transaction
            $conn->commit();

            echo json_encode(['status' => 'success']);
        } catch (Exception $e) {
            // Rollback the transaction on error
            $conn->rollback();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }

        // Close the connection
        $conn->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid employee ID']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>