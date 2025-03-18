<?php
session_start();
require_once '../api/queue_api.php';
require_once '../auth/session.php';
require_once '../config/connection.php';

// Check if user is logged in
checkLogin();
checkUser();

$userId = $_SESSION['user_id'];
$name = $_SESSION['name'];

// Extract first name from name
$firstName = explode(' ', $name)[0];

$accountType = $_SESSION['role'];
$employeeId = $_SESSION['employee_id'];
//get office id
$officeId = getOfficeIdFromEmployee($employeeId);

$windowId = getWindowId($employeeId);

// Get the office name
$officeName = getOfficeName($officeId);

// Get the office status
$officeStatus = getOfficeStatus($officeId);

// Get current ticket
$currentTicket = getCurrentTicket($windowId ); // queue number
$_SESSION['current_ticket'] = $currentTicket;

if ($currentTicket) {
    // If there is a current ticket, get the details
    $ticketData = getTicketDetails($currentTicket);

    // Get account type of user of the current ticket
    $currentTicketType = getUserType($ticketData['user_id']);

    // Get the purpose of the current ticket
    $currentTicketPurpose = $ticketData['service_details'];

    // Current ticket full name
    $currentTicketName = getFullName($ticketData['user_id']);
} else {
    // No current ticket
    $currentTicketName = null;
    $currentTicketType = null;
    $currentTicketPurpose = null;
}

// Fetch current queue data
$currentQueue = getQueue($officeId);
$queueData = json_decode($currentQueue, true);


// Serve the next ticket only if there are people in the queue and the office is not on break
if (!empty($queueData)) {
    // Serve the next ticket
    $serve = serveNextInQueue($officeId);
    $nextTicket = json_decode($serve, true);
}

//window status
$window = getWindowStatus($employeeId);
$windowStatus = $window['window_status'];
$windowNumber = $window['window_number'];

//if office is closed
if ($officeStatus === 'closed') {
    //set queue message to office closed
    $_SESSION['queueMessage'] = 'Office is currently closed.';
    $windowStatus = 'on_break';
    updateWindowStatus($windowStatus, $employeeId);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/queueview_employee.css">
    <link rel="shortcut icon" type="image/x-icon" href="../favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400;1,700&display=swap"
        rel="stylesheet">
    <!-- google font icons -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=account_circle,coffee,person_remove,roller_shades,roller_shades_closed" />
    <script src="../js/employee_dashboard.js" defer></script>
    <script src="../js/shared/mobile_menu.js" defer></script>
    <title>NU Queuest: Current Queue</title>
</head>

<body>
    <div class="header">
        <div class="header-left">
            <h1>NU QUEUEST</h1>
        </div>
        <div class="header-right">
            <div class="user">
                <!-- name is hard-coded, should be linked to user database for name instead -->
                <p>Welcome back, <span class="account-name"><?php echo htmlspecialchars($firstName); ?></span></p>
                <!-- !!! - change into actual account img??? -->
                <i class="material-symbols-outlined account-pic">account_circle</i>
                <p class="logout"><a href="../auth/logout.php">Logout</a></p>

            </div>
        </div>
    </div>

    <div class="main">
        <div class="main-header">
            <div class="header-text">
                <h1><?php echo htmlspecialchars($officeName); ?></h1>
                <div id="queue-message" style="color: red; font-weight: bold;">
                    <?php
                    if (!empty($_SESSION['queueMessage'])) {
                        echo $_SESSION['queueMessage'];
                        unset($_SESSION['queueMessage']); // Clear the warning message
                    }
                    ?>
                </div>
                <div id="open-message" style="color: green; font-weight: bold;">
                    <?php
                    if (!empty($_SESSION['openMessage'])) {
                        echo $_SESSION['openMessage'];
                        unset($_SESSION['openMessage']); // Clear the warning message
                    }
                    ?>
                </div>
            </div>
            <div class="office-btn-container">
                <div class="during-office-hrs-btn-container">
                    <div class="toggle-break-queue">
                        <button class="break-btn" type="submit" value="" <?php echo ($windowStatus === 'on_break') ? 'disabled' : ''; ?> <?php echo ($officeStatus === 'closed') ? 'disabled' : ''; ?>>Break</button>
                        <button class="queue-btn" type="submit" value="" <?php echo ($windowStatus === 'open') ? 'disabled' : ''; ?> <?php echo ($officeStatus === 'closed') ? 'disabled' : ''; ?>>Queue</button>
                    </div>
                </div>
                <div class="during-office-hrs-btn-container">
                    <?php if ($officeStatus === 'closed'): ?>
                        <div class="open-office-btn ctrl-btn update-office">
                            <i class="material-symbols-outlined notif-btn-icon">roller_shades</i>
                            <p>Open Office</p>
                        </div>
                    <?php endif; ?>
                    <?php if ($officeStatus === 'open'): ?>
                        <div class="close-office-btn ctrl-btn update-office" id="open-office-btn">
                            <i class="material-symbols-outlined notif-btn-icon">roller_shades_closed</i>
                            <p>Close office</p>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>

        <div class="main-div"></div>

        <div class="main-body">
            <div class="queue-ticket">
                <!-- scoop corners -->
                <div id="ne" class="scoop-corner"></div>
                <div id="nw" class="scoop-corner"></div>
                <div id="se" class="scoop-corner"></div>
                <div id="sw" class="scoop-corner"></div>
                <div class="queue-office">
                    <h1>Currently serving</h1>
                </div>
                <div class="queue-num">
                    <p>Queue number</p>
                </div>
                <div class="queue-code">
                    <h1><?php echo htmlspecialchars($currentTicket ?: '-'); ?></h1>
                </div>

                <div class="user-queue-details">
                    <div class="user-name">
                        <p class="user-detail">Name</p>
                        <p class="user-in"><?php echo htmlspecialchars($currentTicketName ?: '-'); ?></p>
                    </div>
                    <div class="user-type">
                        <p class="user-detail">Type</p>
                        <p class="user-in"><?php echo htmlspecialchars($currentTicketType ?: '-'); ?></p>
                    </div>
                    <div class="user-purpose">
                        <p class="user-detail">Purpose</p>
                        <p class="user-in"><?php echo htmlspecialchars($currentTicketPurpose ?: '-'); ?></p>
                    </div>
                </div>
                <div class="ticket-buttons">
                    <!-- !!! - as yet, no javascript action -->
                    <button class="cancel-ticket-btn" name="control-button" type="button" <?php echo ($officeStatus === 'closed') ? 'disabled' : ''; ?> <?php echo $currentTicket ? '' : 'disabled'; ?>
                        <?php echo ($windowStatus === 'on_break') ? 'disabled' : ''; ?>>Cancel</button>
                    <button class="done-ticket-btn" name="control-button" type="submit" value="" <?php echo ($officeStatus === 'closed') ? 'disabled' : ''; ?> <?php echo $currentTicket ? '' : 'disabled'; ?>
                        <?php echo ($windowStatus === 'on_break') ? 'disabled' : ''; ?>>Done</button>

                </div>
            </div>

            <div class="queue-status-panel">
                <div class="queue-main">
                    <div class="officewindows-container">
                        <div class="officewindows">
                            <?php
                            // Include the appropriate content for the window status
                            if ($windowStatus === 'open') {
                                include '../windows/window_open.php';
                            } elseif ($windowStatus === 'on_break') {
                                include '../windows/window_on_break.php';
                            } elseif ($windowStatus === 'closed') {
                                include '../windows/window_closed.php';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="queue-div"></div>
                    <div class="queue-container">
                        <?php if (!empty($queueData)): ?>
                            <?php foreach ($queueData as $index => $ticket): ?>
                                <!-- get the account type of user -->
                                <?php $account_type = getUserType($ticket['user_id']); ?>
                                <?php $purpose = getTicketPurpose($ticket['ticket_id']); ?>
                                <div class="queue-element">
                                    <div class="q-num">
                                        <h3><?php echo $index + 1; ?></h3>
                                    </div>
                                    <div class="q-code">
                                        <p><?php echo htmlspecialchars($ticket['queue_number']); ?></p>
                                    </div>
                                    <div class="q-type">
                                        <p><?php echo htmlspecialchars($account_type); ?></p>
                                    </div>
                                    <div class="q-purpose">
                                        <p><?php echo htmlspecialchars($purpose); ?></p>
                                    </div>
                                    <!-- TODO: CREATE ANOTHER MODAL FOR CANCELLING QUEUE -->
                                    <div class="remove-queue" id="cancelQueueButton" name="cancelTempQueueTicket"
                                        value="<?php echo $ticket['queue_number']; ?>">
                                        <i class="material-symbols-outlined person-remove-i">person_remove</i>
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
    </div>

    <!-- dialog for canceling current ticket -->
    <dialog id="dialog-cancel-ticket">
        <div class="title-bar">
            <p class="title">Warning</p>
            <div class="close-button icon-close"></div>
        </div>
        <div class="line-container">
            <div class="line-separator"></div>
        </div>
        <div class="dialog-body">
            <p class="text-confirmation">Are you sure you want to cancel this queue?</p>
            <p>This action will permanently remove the user from the queue, and it cannot be undone.</p>
        </div>
        <form action="../api/cancel_currentTicket.php" method="post">
            <div class="action-bar">
                <input type="hidden" name="currentTicket" value="<?php echo htmlspecialchars($currentTicket); ?>">
                <button class="button-left cancel" name="control-button" type="button" value="">Keep
                    the spot</button>
                <button class="button-right confirm" name="cancelCurrentTicket" type="submit" value="">Confirm
                    cancellation</button>
            </div>
        </form>
    </dialog>

    <!-- dialog for canceling from queue -->
    <dialog id="dialog-cancel-queue">
        <div class="title-bar">
            <p class="title">Warning</p>
            <div class="close-button icon-close"></div>
        </div>
        <div class="line-container">
            <div class="line-separator"></div>
        </div>
        <div class="dialog-body">
            <p class="text-confirmation">Are you sure you want to cancel this queue?</p>
            <p>This action will permanently remove the user from the queue, and it cannot be undone.</p>
        </div>
        <form action="../api/cancel_currentTicket.php" method="post">
            <div class="action-bar">
                <input type="hidden" name="cancelTempQueueTicket" id="hiddenQueueNumber" value="">
                <button class="button-left cancel" name="control-button" type="button" value="">Keep
                    the spot</button>
                <button class="button-right confirm" name="cancelQueue" type="submit" value="">Confirm
                    cancellation</button>
            </div>
        </form>
    </dialog>

    <!-- NEED TOGGLE -->
    <dialog id="dialog-take-break" class="modal-employee-break">
        <div class="title-bar">
            <p class="title">You're about to take a break</p>
            <div class="close-button icon-close"></div>
        </div>
        <div class="line-separator"></div>

        <div class="dialog-body">
            <p class="text-confirmation">Are you sure you want to take a break?</p>
            <p>You can resume whenever you're ready. Please note that this will notify everyone in the queue.</p>
        </div>
        <form action="../api/take_break_api.php" method="POST" id="break-form">
            <div class="action-bar">
                <!-- input type hidden for window_status = on_break -->
                <input type="hidden" name="window_status" value="on_break">
                <button class="button-left cancel" name="control-button" type="button" value=""
                    form="break-form">Cancel</button>
                <button class="button-right confirm" name="control-button" type="submit" value="" form="break-form"
                    id="start-break-button">Start break</button>
            </div>
        </form>
    </dialog>

    <dialog id="dialog-resume-break" class="modal-employee-break">
        <div class="title-bar">
            <p class="title">Resume the queue?</p>
            <div class="close-button icon-close"></div>
        </div>
        <div class="line-separator"></div>
        <div class="dialog-body">
            <p class="text-confirmation">Welcome back!</p>
            <p>Would you like to resume the queue for your office now?</p>
        </div>
        <form action="../api/take_break_api.php" method="POST" id="resume-form">
            <div class="action-bar">
                <!-- input type hidden for window_status = open -->
                <input type="hidden" name="window_status" value="open">
                <button class="button-left cancel" name="control-button" type="button" value=""
                    form="resume-form">Cancel</button>
                <button class="button-right confirm" name="control-button" type="submit" value="" form="resume-form"
                    id="start-break-button">Resume</button>
            </div>
        </form>
    </dialog>

    <!-- dialog for close office -->
    <dialog id="dialog-close-office">
        <div class="title-bar">
            <p class="title">Warning</p>
            <div class="close-button icon-close"></div>
        </div>
        <div class="line-container">
            <div class="line-separator"></div>
        </div>
        <div class="dialog-body">
            <p class="text-confirmation">Are you sure you want to close this office?</p>
            <p>This action will stop the queueing, and it cannot be undone.</p>
        </div>
        <form method="POST" action="../api/update_office_status.php" id="openOffice">
            <div class="action-bar">
                <input type="hidden" name="officeId" value="<?php echo htmlspecialchars($officeId); ?>">
                <input type="hidden" name="status" value="closed">
                <button class="button-left cancel" name="control-button" type="button" value="">Cancel</button>
                <button class="button-right confirm" name="closeOffice" type="submit" value="">Confirm</button>
            </div>
        </form>
    </dialog>
    <!-- dialog for done -->
    <dialog id="dialog-queue-done">
        <div class="title-bar">
            <p class="title">Warning</p>
            <div class="close-button icon-close"></div>
        </div>
        <div class="line-container">
            <div class="line-separator"></div>
        </div>
        <div class="dialog-body">
            <p class="text-confirmation">Are you sure you want to mark the queue as done?</p>
            <p>This action will complete the transaction for the current queue.</p>
        </div>
        <form action="../api/serve_next.php" method="post">
            <div class="action-bar">
                <input type="hidden" name="currentTicket" value="<?php echo htmlspecialchars($currentTicket); ?>">
                <button class="button-left cancel" name="control-button" type="button" value="">Cancel</button>
                <button class="button-right confirm" name="closeOffice" type="submit" value="">Confirm</button>
            </div>
        </form>
    </dialog>
    <!-- open office -->
    <dialog id="dialog-open-office">
        <div class="title-bar">
            <p class="title">Open the office</p>
            <div class="close-button icon-close"></div>
        </div>
        <div class="line-container">
            <div class="line-separator"></div>
        </div>
        <div class="dialog-body">
            <p class="text-confirmation">Ready to start the day?</p>
            <p>Opening the office means your office will now accept queues</p>
        </div>
        <form method="POST" action="../api/update_office_status.php" id="openOffice">
            <div class="action-bar">
                <input type="hidden" name="officeId" value="<?php echo htmlspecialchars($officeId); ?>">
                <input type="hidden" name="status" value="open">
                <button class="button-left cancel" name="control-button" type="button" value="">Cancel</button>
                <button class="button-right confirm" name="openOffice" type="submit" value="">Confirm</button>
            </div>
        </form>
    </dialog>
</body>

</html>
