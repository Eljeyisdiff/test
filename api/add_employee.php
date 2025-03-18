<?php
require '../config/connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = isset($_POST['user_name']) ? trim($_POST['user_name']) : '';
    $office_name = isset($_POST['office_name']) ? trim($_POST['office_name']) : '';
    $window_number = isset($_POST['window_number']) ? trim($_POST['window_number']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $account_type = 'Employee'; 

    if ($full_name && $office_name && $window_number && $email && $password) {
        try {
            // Start a transaction
            $conn->begin_transaction();

            // Fetch the office_id based on the office_name
            $sql = "SELECT office_id FROM offices WHERE office_name = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $office_name);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $office = $result->fetch_assoc();
                $office_id = $office['office_id'];
            } else {
                throw new Exception('Office not found');
            }
            $stmt->close();

            // Check if the window number is already occupied in the office
            $sql = "SELECT * FROM office_windows WHERE office_id = ? AND window_number = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('is', $office_id, $window_number);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                throw new Exception('Window '. $window_number.' already occupied in '. $office_name .' Office. Please choose another window number.');
            }
            $stmt->close();

            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert into the users table
            $sql = "INSERT INTO users (full_name, email, password, account_type) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssss', $full_name, $email, $hashed_password, $account_type);
            if (!$stmt->execute()) {
                throw new Exception('Failed to insert into users table: ' . $stmt->error);
            }
            $user_id = $stmt->insert_id;
            $stmt->close();

            // Insert into the employees table
            $sql = "INSERT INTO employees (user_id, office_id) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ii', $user_id, $office_id);
            if (!$stmt->execute()) {
                throw new Exception('Failed to insert into employees table: ' . $stmt->error);
            }
            $employee_id = $stmt->insert_id;
            $stmt->close();

            // Insert into the office_windows table
            $sql = "INSERT INTO office_windows (office_id, employee_id, window_number, window_status) VALUES (?, ?, ?, 'on_break')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('iis', $office_id, $employee_id, $window_number);
            if (!$stmt->execute()) {
                throw new Exception('Failed to insert into office_windows table: ' . $stmt->error);
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
        echo json_encode(['status' => 'error', 'message' => 'Invalid input data']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>