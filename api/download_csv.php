<?php
require_once '../config/connection.php';
include_once 'admin_api.php';

$officeId = isset($_GET['office_id']) ? $_GET['office_id'] : null;
$fromDate = isset($_GET['from_date']) ? $_GET['from_date'] : null;
$toDate = isset($_GET['to_date']) ? $_GET['to_date'] : null;

$getTickets = getCompletedTransactions($officeId, $fromDate, $toDate);

$officeName = getOfficeName($officeId);
$currentDate = date('Y-m-d');
$filename = $officeName;

if ($fromDate && $toDate) {
    $filename .= "_$fromDate_to_$toDate";
} elseif ($fromDate) {
    $filename .= "_from_$fromDate";
} elseif ($toDate) {
    $filename .= "_to_$toDate";
} else {
    $filename .= "_$currentDate";
}

$filename .= '.csv';

header('Content-Type: text/csv');
header("Content-Disposition: attachment;filename=$filename");

$output = fopen('php://output', 'w');

// Add headings
fputcsv($output, array('Completed Transactions'));
if ($fromDate && $toDate) {
    fputcsv($output, array("Date Range: $fromDate to $toDate"));
} elseif ($fromDate) {
    fputcsv($output, array("From Date: $fromDate"));
} elseif ($toDate) {
    fputcsv($output, array("To Date: $toDate"));
} else {
    fputcsv($output, array("Date: $currentDate"));
}
fputcsv($output, array()); // Blank line for separation

// Add column headers
fputcsv($output, array('Queue Number', 'Name', 'Account Type', 'Service', 'Date & Time Completed'));

// Add data rows
if ($getTickets->num_rows > 0) {
    while ($row = $getTickets->fetch_assoc()) {
        fputcsv($output, $row);
    }
}
fclose($output);
exit();
?>