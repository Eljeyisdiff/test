<?php
require_once '../auth/session.php';
header('Content-Type: application/json'); // Set the content type to JSON

global $conn;

$query = "SELECT office_id, office_name FROM offices"; // Adjust the query based on your table structure
$result = $conn->query($query);

$offices = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $offices[] = $row; // Store each row in the array
    }
    echo json_encode($offices); // Return the data as a JSON array
} else {
    // Return an error message if the query fails
    echo json_encode(['error' => 'Failed to fetch offices']);
}

$conn->close();
?>