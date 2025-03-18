<?php
require_once '../auth/session.php';
require_once '../config/connection.php';
include_once '../api/queue_api.php';
session_start();

$officeId = $_SESSION['office_id'];
$officeName = getOfficeName($officeId);

// Fetch current queue data
$currentQueue = getQueue($officeId);
$queueData = json_decode($currentQueue, true);

// Fetch windows status for the given office
$windowsStatus = getWindows($officeId);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/public_queue.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <script type="text/javascript" src="scripts.js"></script>
    <title>NU Queuest: Public View</title>
</head>

<body>
    <div class="on-break-notif">
        <div class="break-container">
            <h1>BE RIGHT BACK</h1>
            <hr class="break-br">
            <p class="service">Service will resume at</p>
            <p class="service-resume">1:00 PM</p>
        </div>
    </div>

    <!-- for the sake of development, values will be explicitly stated in code; this base can be changed once backend has been solidified -->
    <div class="pubqueue-header">
        <h1><?php echo $officeName?></h1>
    </div>

    <div class="publicmain">
        <div class="officewindows-container">
            <?php if (!empty($windowsStatus)): ?>
                <?php foreach ($windowsStatus as $window): ?>
                    <?php $status = $window['status'];
                    $windowNumber = $window['window_number'];
                    $currentTicket = $window['current_ticket']; ?>
                    <div class="officewindows">
                        <!-- Window Status (open, on_break, closed) -->
                        <?php
                        // Include the appropriate content for the window status
                        if ($status === 'open') {
                            include '../windows/window_open.php';
                        } elseif ($status === 'on_break') {
                            include '../windows/window_on_break.php';
                        } elseif ($status === 'closed') {
                            include '../windows/window_closed.php';
                        }
                        ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No windows found for this office.</p>
            <?php endif; ?>
        </div>
        <div class="queue-div"></div>

        <div class="currentqueue">
            <div class="queue-container">
                <?php if (!empty($queueData)): ?>
                    <?php foreach (array_slice($queueData, 0, 5) as $index => $ticket): ?>
                        <div class="queue-element">
                            <div class="q-num">
                                <h3><?php echo $index + 1; ?></h3>
                            </div>
                            <div class="q-code">
                                <p><?php echo htmlspecialchars($ticket['queue_number']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: black; font-weight: bold;">No tickets in the queue.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>