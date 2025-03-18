<?php
include_once '../api/login_api.php';
include_once '../auth/session.php';

session_start();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["email"])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate email and password
    if (validateAdmin($email, $password)) {
        // Store full name in session
        $_SESSION['name'] = getAdminName($email);
        // Set role as admin
        $_SESSION['role'] = 'admin';
        // Get admin_id and store in session
        $_SESSION['admin_id'] = getAdminId($email);

        // Redirect to the admin dashboard
        header("Location: ../admin/admin_dashboard.php");
        exit();
    } else {
        $_SESSION['warningMessage'] = "Invalid email or password!";
        header("Location: ../auth/loginadmin.php");
        exit();
    }
} else {
    //when tried to access through link
    header("Location: ../auth/loginadmin.php");
    exit();
}