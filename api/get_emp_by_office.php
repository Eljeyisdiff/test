<?php
require_once '../config/connection.php';
header('Content-Type: application/json');

$officeId = isset($_GET['office_id']) ? $_GET['office_id'] : null;

if ($officeId !== null && $officeId !== '') {
    // Prepare an SQL statement to fetch the employee details for a specific office
    $query = "
    SELECT 
        e.employee_id, 
        u.full_name, 
        u.email,
        u.password,
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
        e.office_id = ?
    ";

    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param('i', $officeId);
} else {
    // Fetch all employees if office_id is null or empty
    $query = "
    SELECT 
        e.employee_id, 
        u.full_name, 
        u.email,
        u.password,
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
    ";

    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement: ' . $conn->error]);
        exit;
    }
}

$stmt->execute();
$result = $stmt->get_result();
$users = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    echo json_encode(['success' => true, 'users' => $users]);
} else {
    echo json_encode(['success' => false, 'message' => 'No users found.']);
}

$stmt->close();
$conn->close();
?>