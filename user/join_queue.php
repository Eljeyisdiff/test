<?php
require_once '../auth/session.php';
require_once '../config/connection.php';
include_once '../api/queue_api.php';

session_start();

// Ensure the user is logged in
checkLogin();

// Ensure the user is student
checkUser();

//check if user_id is in temp_queue after logging in
$userId = $_SESSION['user_id'];
$queueNumber = getQueueNumber($userId);

//store queue number in session if in queue
if ($queueNumber) {
  $_SESSION['queue_number'] = $queueNumber;
  //office id from temp_queue (when the user logs in after logging out and still in queue)
  $officeId = getOfficeIdFromTempQueue($userId);
  $_SESSION['office_id'] = $officeId;
}

// get the office name from session if set
//check if office name is set
if (!isset($_SESSION['office_name'])) {
  $_SESSION['office_name'] = "";
}
$office_name = $_SESSION['office_name'];

$name = $_SESSION['name'];

$result = getOffices();
if ($result->num_rows === 0) {
  $_SESSION['warningMessage'] = "No offices available at the moment.";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Choose Office - NU QUEUEST</title>
  <link rel="shortcut icon" type="image/x-icon" href="../favicon.ico">
  <script src="../js/choose_queues.js" defer></script>
  <script src="../js/shared/mobile_menu.js" defer></script>
  <link id="nu-queuest-icon" rel="icon" href="../assets/nu_queuest.ico" type="image/x-icon">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400;1,700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=account_circle" />
  <link rel="stylesheet" href="../css/choose_queues.css">
  <link rel="stylesheet" href="../css/shared/header.css">
</head>

<body>
  <header>
    <div class="header-left">
      <h1>NU QUEUEST</h1>
    </div>
    <div class="header-right">
      <div class="header-buttons">
        <a class="toggled" href="join_queue.php">Offices</a>
        <!-- should be linked to QUEUE SELECTION page -->
        <a href="queueview_user.php">Queue</a>
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
          <li><a href="join_queue.php" class="nav-button toggled">Offices</a></li>
          <div class="line-separator"></div>
          <li><a href="queueview_user4.php" class="nav-button">Queues</a></li>
          <div class="line-separator top-bar"></div>
          <li><a href="../auth/logout.php" class="logout">Logout</a></li>
        </ul>
      </div>
    </div>
    <div class="menu-mobile-overlay"></div>
  </header>
  <main>
    <div class="top-bar">
      <p class="title">Please select an office to start queueing</p>
      <div class="search-bar">
        <div class="icon-search"></div>
        <input type="text" id="search_office" placeholder="Search offices" />
      </div>
    </div>
    <div class="line-separator top-bar"></div>
    <div id="queue-message" style="color: red; font-weight: bold;">
      <?php
      if (!empty($_SESSION['queueMessage'])) {
        echo $_SESSION['queueMessage'];
        unset($_SESSION['queueMessage']); // Clear the warning message
      }
      ?>
    </div>
    <div id="warning-message" style="color: #35408e; font-weight: bold;">
      <?php
      if (!empty($_SESSION['warningMessage'])) {
        echo $_SESSION['warningMessage'];
        unset($_SESSION['warningMessage']); // Clear the warning message
      }
      ?>
    </div>

    <div class='office-grids' id="office-grids">
      <?php while ($row = $result->fetch_assoc()) { ?>
        <div class="office-entry" id="office-id-<?php echo $row['office_id']; ?>">
          <div class="title-grid">
            <p class="title"><?php echo $row['office_name']; ?></p>
          </div>
          <div class="line-separator"></div>
          <p><?php echo $row['office_description']; ?></p>
          <div class="queue-statistics">
            <div class="currently-in-line">
              <?php
              //count of the number of people in the queue in the office             
              $queueCount = getQueueCount($row['office_id']);
              ?>
              <p class="current-stat"><?php echo $queueCount['count'] ?? 0; ?></p>
              <p>Currently in line</p>
            </div>
            <!-- check if the office is closed -->
            <!--            <p><?php echo ($row['status'] === 'closed') ? 'Office Closed' : 'Open'; ?></p>-->
          </div>
          <button name="control-button" class="join-queue-btn" type="button" value="<?php echo $row['office_id']; ?>"
            <?php echo ($row['status'] === 'closed') ? 'disabled' : ''; ?>>
            <?php echo ($row['status'] === 'closed') ? 'Office closed' : 'Join queue'; ?></button>
        </div>
      <?php } ?>
    </div>
  </main>
  <!-- join queue -->
  <dialog id="dialog-confirm-queue" class="modal">
    <div class="title-bar">
      <div>
        <p class="title">You selected the</p>
        <p class="title office" id="dialog-title">$CONTENT</p>
      </div>
      <div class="close-button icon-close"></div>
    </div>
    <div class="line-separator"></div>
    <div class="dialog-body">
      <p class="text-reason">What is your reason for visiting today?<span>*</span></p>
      <form id="select-reason" class="select-reason" method="post">
        <div class="selection">
          <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>" />
        </div>
      </form>
    </div>
    <div class="action-bar">
      <button class="close-button go-back" name="control-button" type="button" value="">Go Back</button>
      <button class="confirm-join" name="control-button" type="submit" form="select-reason" value="Submit">Confirm and
        join queue</button>
    </div>
  </dialog>

  <!-- user already queued -->
  <dialog id="dialog-already-queued" class="modal">
    <div class="title-bar">
      <p class="title">You are currently in a queue.</p>
      <div class="close-button icon-close"></div>
    </div>
    <div class="line-container">
      <div class="line-separator"></div>
    </div>
    <p class="dialog-body">
      <span>You are currently in the queue for the <span class="office-name"><?php echo $office_name; ?></span></span>.
      Please complete or cancel your current queue before joining another.
    </p>
    <div class="action-bar">
      <a href="queueview_user.php"><button class="confirm-button goto-queue" name="control-button" type="button"
          value="">Go to my queue</button></a>
      <button class="close-button okay" name="control-button" type="button" value="">Okay</button>
    </div>
  </dialog>
  <footer>

  </footer>
</body>

</html>
