<?php
session_start();

// Unset specific session variables
unset($_SESSION['office_id']);
unset($_SESSION['office_name']);
unset($_SESSION['queue_number']);

// Redirect to join_queue.php
header("Location: ../user/join_queue.php");
exit;
?>