<?php 
require_once 'session.php';
require_once '../config/connection.php';
session_start();



if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Student' || $_SESSION['role'] == 'Guest' || $_SESSION['role'] == 'Employee') {
        logout();
        header("Location: ../auth/index.php");
    } else {
        logout();
        header("Location: ../auth/loginadmin.php");
    }
} else {
    logout();
    header("Location: ../auth/loginadmin.php");
}
?>