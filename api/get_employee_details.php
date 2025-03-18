<?php
require_once '../config/connection.php';

try {
    // Ensure the request method is GET
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['employee_id'])) {
        $employee_id = $_GET['employee_id'];

        // Prepare an SQL statement to fetch the employee details
        $sql = "
            SELECT 
                e.employee_id, 
                u.full_name, 
                u.email,
                o.office_name, 
                ow.window_number,
                ow.window_status
            FROM 
                employees e
            LEFT JOIN 
                users u ON e.user_id = u.user_id
            LEFT JOIN 
                offices o ON e.office_id = o.office_id
            LEFT JOIN 
                office_windows ow ON e.employee_id = ow.employee_id
            WHERE 
                e.employee_id = ?
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $employee_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $employee = $result->fetch_assoc();
            // Add an empty password field with a placeholder
            $employee['password'] = ''; // This will be handled in the front-end
            echo json_encode(['success' => true, 'employee' => $employee]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Employee not found']);
        }

        // Close the statement and connection
        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid request']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>