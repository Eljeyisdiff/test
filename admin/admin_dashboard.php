<?php 
require_once '../auth/session.php';
require_once '../config/connection.php';
include_once '../api/admin_api.php';
session_start();

checkAdminLogin();

$name = getAdminName($_SESSION['admin_id']);

$firstName = explode(' ', trim($name))[0];

$peopleInQueue = peopleInQueue();

$countCompTransact = countTotalCompletedTransactions();

$officesCount = getActiveAndTotalOffices();

$empCount = getTotalLoggedInEmployeeCount();

$trafficData = get_traffic_data($conn);

// Fetch transaction data
$today = new DateTime();
$start_date = (clone $today)->modify('-6 days'); // 7 days including today

$ticks = [];
for ($i = 0; $i < 7; $i++) {
    $ticks[] = (clone $start_date)->modify("+$i days")->format('Y, n-1, j');
}



// $trafficDataResult = $conn->query($transactionQuery);
// $trafficData = [];

// if ($trafficDataResult->num_rows > 0) {
//     while ($row = $trafficDataResult->fetch_assoc()) {
//         $trafficData[$row['date']][$row['ticket_status']] = (int)$row['count'];
//     }
// }


$transactionQuery = "
    SELECT 
        DATE(tickets.created_at) AS date,
        COUNT(tickets.ticket_id) AS count
    FROM 
        tickets
    WHERE 
        tickets.ticket_status = 'Completed' AND
        DATE(tickets.created_at) BETWEEN '" . $start_date->format('Y-m-d') . "' AND '" . $today->format('Y-m-d') . "'
    GROUP BY 
        DATE(tickets.created_at)
    ORDER BY 
        DATE(tickets.created_at);
";

$transactDataResult = $conn->query($transactionQuery);
$transactionData = [];

if ($transactDataResult->num_rows > 0) {
    while ($row = $transactDataResult->fetch_assoc()) {
        $transactionData[$row['date']] = (int)$row['count'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link id="nu-queuest-icon" rel="icon" href="../assets/nu_queuest.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <link rel="shortcut icon" type="image/x-icon" href="../favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <!-- google font icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=account_circle,group,show_chart" />
    <!-- chart responsiveness calls -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://www.google.com/jsapi"></script>
    <script src="../js/admin_dashboard.js"></script>
    <title>Admin Portal: Dashboard</title>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <h1>NU QUEUEST</h1>
            <p>Admin Portal</p>
        </div>
        <div class="header-right">
            <div class="header-buttons">
                <h2 class="current-page">Dashboard</h2>
                <a href="../admin/admin_officesview.php"><h2>Offices</h2></a>
                <a href="../admin/admin_usersview.php"><h2>Employees</h2></a>
            </div>
            <div class="user">
                <!-- name is hard-coded, should be linked to user database for name instead -->
                <p>Hi, <span class="account-name"><?php echo$firstName?></span></p>
                <!-- !!! - change into actual account img??? -->
                <i class="material-symbols-outlined account-pic">account_circle</i>
                <p class="logout"><a href="../auth/logout.php">Logout</a></p>
            </div>
        </div>
    </div>
    <div class="main">
        <div class="main-header">
            <h1>Monitor and manage all queue operations</h1>
        </div>
        
        <div class="main-div"></div>

        <div class="main-content">
            <div class="queue-count analytics-cont grid-element">
                <div class="analytics-cont-left">
                    <h2 class="analytics-title">People on queue</h2>
                    <p class="counter"><?php echo$peopleInQueue ?></p>
                </div>
                <div class="analytics-cont-right analytics-i">
                    <img src="../assets/group_icon_gfont.svg" alt="">
                </div>
            </div>
            <div class="transaction-count analytics-cont grid-element">
                <div class="analytics-cont-left">
                    <h2 class="analytics-title">Total Completed Transactions</h2>
                    <p class="counter"> <?php echo$countCompTransact ?></p>  <!--0 as initial value -->
                </div>
                <div class="analytics-cont-right analytics-i">
                    <img src="../assets/show_chart_icon_gfont.svg" alt="">
                </div>
            </div>
            <div class="active-offices grid-element">
                <h2 class="analytics-title">Active Offices</h2>
                <p class="counter"><?php echo $officesCount['active'] . ' / ' . $officesCount['total'];?></p>
                <p class="active-ofc-counter active-office-desc"><?php echo $officesCount['active'] ?> Office(s) handled transactions today.</p>
                <div class="analytics-div"></div>

                <a href="../admin/admin_officesview.php">
                <div class="manage-activity-btn">
                <p>Manage</p>
                </div>
                </a>

            </div>
            <div class="active-users grid-element">
                <h2 class="analytics-title">Total Logged in Employees</h2>
                <p class="counter"> <?php echo $empCount?></p>
                <div class="analytics-div"></div>

                <a href="../admin/admin_usersview.php">
                <div class="manage-activity-btn">
                    <p>Manage</p>
                </div>
                </a>

            </div>
            <div class="transaction-graph grid-element">
                <h2 class="analytics-title">Transaction details</h2>
                <!-- <div id="loading-message" style="text-align: center;">Loading...</div> -->
                <div class="transaction-analytics-graph" id="transactchart_div"></div>
            </div>
            <div class="traffic-graph grid-element">
                <h2 class="analytics-title">Office traffic</h2>
                    <!-- <div id="loading-traffic-message" style="text-align: center;">Loading...</div> -->
                <div class="traffic-analytics-graph" id="trafficchart_div"></div>
            </div>
        </div>
    </div>

    <!-- google charts for transactions and traffic -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
       google.load("visualization", "1", {packages:["corechart"]});
        google.setOnLoadCallback(drawTrafficChart);
        google.setOnLoadCallback(drawTransactChart);

        function drawTrafficChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Office');
            data.addColumn('number', 'Traffic');
            data.addColumn({type: 'string', role: 'style'});

            data.addRows(<?php echo json_encode($trafficData); ?>);

            var view = new google.visualization.DataView(data);
            var options = {
                height: 270,
                fontSize: 17,
                fontName: 'Atkinson Hyperlegible',
                lineWidth: 3,
                pointShape: 'square',
                pointSize: 10,
                vAxis: {
                    ticks: [0, 20, 40, 60, 80, 100],
                    textStyle: { color: 'black' }
                },
                hAxis: {
                    gridlines: { color: 'white' },
                    baselineColor: { color: 'white' },
                    textStyle: { color: 'black' },
                    format: 'MMM d',
                    viewWindow: {
                        min: new Date(<?php echo $start_date->format('Y, n-1, j'); ?>),
                        max: new Date(<?php echo $today->format('Y, n-1, j'); ?>)
                    },
                    ticks: [
                        <?php echo implode(', ', array_map(function($tick) { return "new Date($tick)"; }, $ticks)); ?>
                    ]
                },
                legend: { position: 'top' }, // Place the legend at the top
                chartArea: {
                    width: '85%', // Adjust the width to fit the legend
                    height: '70%' // Adjust the height to fit the legend
                }
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('trafficchart_div'));
            chart.draw(view, options);
        }

        function drawTransactChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('date', 'Date');
            data.addColumn('number', 'Completed Transactions');

            data.addRows([
                <?php
                foreach ($transactionData as $date => $count) {
                    echo '[new Date("' . $date . '"), ' . $count . '],';
                }
                ?>
            ]);

            var options = {
                height: 270,
                fontSize: 17,
                fontName: 'Atkinson Hyperlegible',
                lineWidth: 3,
                pointShape: 'square',
                pointSize: 10,
                vAxis: {
                    ticks: [0, 50, 100, 150, 200, 250],
                    textStyle: { color: 'black' }
                },
                hAxis: {
                    gridlines: { color: 'white' },
                    baselineColor: { color: 'white' },
                    textStyle: { color: 'black' },
                    format: 'MMM d',
                    viewWindow: {
                        min: new Date(<?php echo $start_date->format('Y, n-1, j'); ?>),
                        max: new Date(<?php echo $today->format('Y, n-1, j'); ?>)
                    },
                    ticks: [
                        <?php echo implode(', ', array_map(function($tick) { return "new Date($tick)"; }, $ticks)); ?>
                    ]
                },
                legend: { position: 'top' }, // Place the legend at the top
                chartArea: {
                    width: '85%', // Adjust the width to fit the legend
                    height: '70%' // Adjust the height to fit the legend
                }
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('transactchart_div'));
            chart.draw(data, options);
        }
      </script>
</body>
</html>
