<?php
require_once '../config/connection.php';
include_once 'admin_api.php';

$officeId = isset($_GET['office_id']) ? $_GET['office_id'] : null;
$fromDate = isset($_GET['from_date']) ? $_GET['from_date'] : null;
$toDate = isset($_GET['to_date']) ? $_GET['to_date'] : null;

$getTickets = getCompletedTransactions($officeId, $fromDate, $toDate);

if ($getTickets->num_rows > 0) {
    while ($row = $getTickets->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['queue_number'] . "</td>";
        echo "<td>" . $row['full_name'] . "</td>";
        echo "<td>" . $row['account_type'] . "</td>";
        echo "<td>" . $row['service_details'] . "</td>";
        echo "<td>" . $row['sevice_ended_at'] . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>No completed transactions available</td></tr>";
}
?>