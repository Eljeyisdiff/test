<?php 
require_once '../auth/session.php';
require_once '../config/connection.php';
include_once '../api/admin_api.php';

session_start();
checkAdminLogin();

$name = getAdminName($_SESSION['admin_id']);
$firstName = explode(' ', trim($name))[0];

$officeId = isset($_GET['office_id']) ? $_GET['office_id'] : null;
$officeName = getOfficeName($officeId);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View <?php echo $officeName?> Office </title>
  <link rel="shortcut icon" type="image/x-icon" href="../favicon.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
  <link rel="stylesheet" href="../css/view_office_details.css">
</head>
<body>
<div class="header">
    <div class="header-left">
        <h1>NU QUEUEST</h1>
        <p>Admin Portal</p>
    </div>
    <div class="header-right">
        <div class="header-buttons">
            <a href="admin_dashboard.php"><h2>Dashboard</h2></a>
            <h2 class="current-page">Offices</h2>
            <a href="admin_usersview.php"><h2>Users</h2></a>
        </div>
        <div class="user">
            <p>Hi, <span class="account-name"><?php echo $firstName; ?></span></p>
            <!-- !!! - change into actual account img??? -->
            <i class="material-symbols-outlined account-pic">account_circle</i>
            <p class="logout"><a href="../auth/logout.php">Logout</a></p>
        </div>
    </div>
</div>
<div class="main">
    <div class="office-name">
        <h1>Completed Transactions - <?php echo $officeName?></h1>
    </div>
    <div class="line-separator"></div>
    <div class="back-button">
        <button onclick="history.back()" class="btn btn-secondary">
            <i class="material-symbols-outlined arrow_back">arrow_back</i>
            Back
        </button>
    </div>
    <div class="completed-transactions">
            <div class="top-bar">
                <div class="date-container">
                    <div class="form-group">
                        <label for="from_date">From:</label>
                        <input type="date" id="from_date" name="from_date">
                    </div>
                    <div class="form-group">
                        <label for="to_date">To:</label>
                        <input type="date" id="to_date" name="to_date">
                    </div>
                </div>
                <button id="download-csv" class="btn btn-primary">
                    <i class="material-symbols-outlined download">download</i>
                    Download CSV
                </button>
            </div>
            <table id="transactions-table" class="display">
                <thead>
                    <tr>
                        <th>Queue Number</th>
                        <th>Name</th>
                        <th>Account Type</th>
                        <th>Service</th>
                        <th>Date & Time Completed</th>
                    </tr>
                </thead>
                <tbody id="transactions-table-body">
                    <!-- Data will be populated here by JavaScript -->
                </tbody>
            </table>
    </div>
</div>
<script>
     document.addEventListener('DOMContentLoaded', function() {
        function fetchTransactions() {
            var fromDate = document.getElementById('from_date').value;
            var toDate = document.getElementById('to_date').value;
            var officeId = '<?php echo $officeId; ?>';
    
            // Show the loading indicator
            var tableBody = document.getElementById('transactions-table-body');
            tableBody.innerHTML = `
                <tr id="loading-row">
                    <td colspan="5" style="text-align: center;">Loading...</td>
                </tr>
            `;
    
            fetch(`../api/get_completed_transactions.php?from_date=${fromDate}&to_date=${toDate}&office_id=${officeId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text(); // Expecting HTML response
                })
                .then(html => {
                    // Hide the loading indicator
                    var loadingRow = document.getElementById('loading-row');
                    if (loadingRow) {
                        loadingRow.remove();
                    }

    
                    // Populate the table with the fetched data
                    tableBody.innerHTML = html;
                    // Initialize DataTable
                    $('#transactions-table').DataTable();
                })
                .catch(error => {
                    console.error('Error fetching transactions:', error);
                    // Hide the loading indicator
                    var loadingRow = document.getElementById('loading-row');
                    if (loadingRow) {
                        loadingRow.remove();
                    }
                    // Optionally, show an error message
                    tableBody.innerHTML = '<tr><td colspan="5" style="text-align: center;">Failed to load data</td></tr>';
                });
        }
    
        // Fetch transactions on page load
        fetchTransactions();
    
        // Fetch transactions when the date range changes
        document.getElementById('from_date').addEventListener('change', fetchTransactions);
        document.getElementById('to_date').addEventListener('change', fetchTransactions);
    
        // Download CSV
        document.getElementById('download-csv').addEventListener('click', function() {
            var fromDate = document.getElementById('from_date').value;
            var toDate = document.getElementById('to_date').value;
            var officeId = '<?php echo $officeId; ?>';
            var url = `../api/download_csv.php?office_id=${officeId}&from_date=${fromDate}&to_date=${toDate}`;
            window.location.href = url;
        });
    });
</script>
</body>
</html>
