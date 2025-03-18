<?php
require_once '../auth/session.php';
require_once '../config/connection.php';
include_once '../api/queue_api.php';
session_start();
checkLogin();
checkUser();

if (!isset($_SESSION['queue_number'])) {
    //warning message that the user is not in a queue
    $_SESSION['queueMessage'] = "You are not in a queue.";
    header("Location: join_queue.php");
    exit();
}

//check if ticket is completed
if (isTicketCompleted($_SESSION['queue_number'])) {
    // Set JavaScript variable to indicate transaction completion
    echo '<script>var isTransactionCompleted = true;</script>';
} else {
    echo '<script>var isTransactionCompleted = false;</script>';
}

//check if ticket is cancelled
if (isTicketCancelled($_SESSION['queue_number'])) {
    echo '<script>var isTransactionCancelled = true;</script>';
} else {
    echo '<script>var isTransactionCancelled = false;</script>';
}

$userId = $_SESSION['user_id'];
$name = $_SESSION['name'];
$accountType = $_SESSION['role'];

$ticketId = $_GET['ticket_id'] ?? null;

//office ID
$officeId = $_SESSION['office_id'];
// Get the office name
$officeName = getOfficeName($officeId);

//store in session
$_SESSION['office_name'] = $officeName;
// Get the queue number
$queueNumber = $_SESSION['queue_number'];

$updatePosition = json_decode(updatePositionNumbers($queueNumber));

// Get purpose and position number using the queue number
$purpose = getPurposeOfVisit($queueNumber);
$position = getPositionNumber($queueNumber); // This now returns the ordinal position

$isCurrentTicket = isCurrentTicket($queueNumber);

//check if the ticket is currently serving
if ($isCurrentTicket) {
    $position = "Currently Serving";
    echo '<script>var isCurrentlyServing = true;</script>';
} else {
    echo '<script>var isCurrentlyServing = false;</script>';
}

// // Pass the PHP boolean to JavaScript
// echo "<script>
//     const shouldShowDialog = " . json_encode($isCurrentTicket) . ";
// </script>";

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
    <script src="../js/shared/mobile_menu.js" defer></script>
    <script src="../js/queue_notif.js" defer></script>
    <script src="../js/queueview.js" defer></script>
    <link rel="shortcut icon" type="image/x-icon" href="../favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400;1,700&display=swap"
        rel="stylesheet">
    <!-- google font icons -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=account_circle,notifications" />
    <title>NU Queuest: Current Queue</title>
    <link rel="stylesheet" href="../css/queueview_user.css">
    <link rel="stylesheet" href="../css/shared/header.css">
</head>

<body>
    <header>
        <div class="header-left">
            <h1>NU QUEUEST</h1>
        </div>
        <div class="header-right">

            <div class="header-buttons">
                <a href="join_queue.php">Offices</a>
                <!-- should be linked to QUEUE SELECTION page -->
                <a class="toggled" href="queueview_user.php">Queue</a>
            </div>
            <div class="user">
                <!-- name is hard-coded, should be linked to user database for name instead -->
                <p>Hi, <span class="account-name"><?php echo $name ?></span></p>
                <!-- !!! - change into actual account img??? -->
                <i class="material-symbols-outlined account-pic">account_circle</i>
                <a href="../auth/logout.php" class="logout">Logout</a>
            </div>
        </div>
        <div class="menu-mobile"></div>
        <div class="menu-mobile-content">
            <div class="menu-header">
                <div class="profile-photo in-menu"></div>
                <h3>Hi, <span><?php echo $name; ?></span></h3>
                <div class="menu-mobile-close"></div>
            </div>
            <div class="menu-body">
                <ul>
                    <li><a href="join_queue.php" class="nav-button">Offices</a></li>
                    <div class="line-separator"></div>
                    <li><a href="queueview_user.php" class="nav-button toggled">Queues</a></li>
                    <div class="line-separator top-bar"></div>
                    <li><a href="../auth/logout.php" class="logout">Logout</a></li>
                </ul>
            </div>
        </div>
        <div class="menu-mobile-overlay"></div>
    </header>
    <div class="main">

        <div class="queue-ticket">
            <!-- scoop corners -->
            <div id="ne" class="scoop-corner"></div>
            <div id="nw" class="scoop-corner"></div>
            <div id="se" class="scoop-corner"></div>
            <div id="sw" class="scoop-corner"></div>
            <div class="queue-office">
                <h1><?php echo $officeName ?></h1>
            </div>
            <div class="queue-num">
                <p>Your queue number</p>
            </div>
            <div class="queue-code">
                <h1><?php echo $queueNumber ?></h1>
            </div>

            <div class="user-queue-details">
                <div class="user-name">
                    <p class="user-detail">Name</p>
                    <p class="user-in"><?php echo $name ?></p>
                </div>
                <div class="user-type">
                    <p class="user-detail">Type</p>
                    <p class="user-in"><?php echo $accountType ?></p>
                </div>
                <div class="user-in">
                    <p class="user-detail">Purpose</p>
                    <p class="purpose"><?php echo $purpose ?></p>
                </div>
            </div>

            <div class="ticket-div"></div>

            <div class="wait-position">
                <div class="queue-position">
                    <p class="q-pos"><?php echo $position ?></p>
                    <p class="text">Queue</p>
                    <p class="text">position</p>
                </div>
            </div>
            <div class="ticket-buttons">
                <!-- !!! - as yet, no javascript action -->
                <button class="cancel-ticket-button" name="control-button" type="button" value="">Cancel my
                    queue</button>
            </div>
        </div>

        <div class="queue-status-panel">
            <div class="queue-header">
                <div class="header-text">
                    <h1>Queue status</h1>
                </div>
                <!-- !!! - still has no javascript action -->
                <div class="notif-button" id="notif-button">
                    <i class="material-symbols-outlined notif-btn-icon">notifications</i>
                    <p>Get notified when I'm next</p>
                </div>
            </div>
            <div class="queue-main">
                <p style="color: black; font-weight: bold;"><?php $officeId ?></p>
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
    </div>
    <dialog id="dialog-cancel-queue">
        <div class="title-bar">
            <p class="title">Warning</p>
            <!-- <div class="close-button icon-close" onclick="closeDialog()"></div> -->
        </div>
        <div class="line-container">
            <div class="line-separator"></div>
        </div>
        <div class="dialog-body">
            <p class="text-confirmation">Are you sure you want to cancel your queue?</p>
            <p>You will lose your current spot in line, and this action cannot be undone</p>
        </div>
        <form action="../api/cancel_queue.php" method="POST">
            <div class="action-bar">
                <button class="button keep" name="control-button" type="button" value="" onclick="closeDialog()">Keep my
                    spot</button>
                <button class="button-right confirm" name="control-button" type="submit" value="">Confirm
                    cancellation</button>
            </div>
        </form>
    </dialog>
    <dialog class="dialog-transaction" id="dialog-transaction-ongoing">
        <div class="dialog-body">
            <p>Transaction Ongoing</p>
        </div>
    </dialog>
    <dialog class="dialog-transaction" id="dialog-transaction-completed">
        <div class="title-bar">
            <p class="title">Transaction Complete</p>
        </div>
        <div class="line-container">
            <div class="line-separator"></div>
        </div>
        <div class="dialog-body">
            <p class="text-confirmation">Your transaction has been completed</p>
        </div>
        <div class="action-bar">
            <button class="button-right go-back" name="control-button" type="button" value="">Go back to office
                selection</button>
        </div>
    </dialog>
    <dialog class="dialog-transaction" id="dialog-transaction-cancelled">
        <div class="title-bar">
            <p class="title">Ticket Cancelled</p>
        </div>
        <div class="line-container">
            <div class="line-separator"></div>
        </div>
        <div class="dialog-body">
            <p class="text-confirmation">Your ticket has been cancelled</p>
            <!-- <p>Reason: Insert Reason</p> -->
        </div>
        <div class="action-bar">
            <button class="button-right go-back" name="control-button" type="button" value="">Go back to office
                selection</button>
        </div>
        </form>
    </dialog>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            //show modal for transaction ongoing
            if (isCurrentlyServing) {
                document.getElementById("dialog-transaction-ongoing").showModal();
            }
            //show modal for transaction completed
            if (isTransactionCompleted) {
                document.getElementById("dialog-transaction-completed").showModal();
            }
            //transaction cancelled
            if (isTransactionCancelled) {
                document.getElementById("dialog-transaction-cancelled").showModal();
            }

            // Redirect for any "Go back" button click
            document.querySelectorAll(".go-back").forEach(button => {
                button.addEventListener("click", function () {
                    window.location.href = '../api/clear_session.php';
                });
            });
        });


    </script>
</body>

</html>
