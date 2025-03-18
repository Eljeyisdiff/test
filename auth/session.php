<?php
require_once '../config/connection.php';

//FOR USER
// Check if a user is logged in
function checkLogin()
{
  // If there is no user session, redirect to the login page
  if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/index.php");
    exit();
  }
}
//if role is set not user, redirect to their respective dashboard
function checkUser()
{
  // Check if user is logged in by checking if 'user_id' is set in session
  if (isset($_SESSION['user_id'])) {
    // Check user role and redirect based on role
    if($_SESSION['role'] == 'employee') {
      header("Location: ../employee/employee_dashboard.php"); // Redirect to employee dashboard
      exit();
    } elseif ($_SESSION['role'] == 'admin') {
      header("Location: ../admin/view_offices.php"); // Redirect to admin dashboard
      exit();
    }
  }
}

//check if employee is logged in
function checkIfEmployee()
{
  // Check if user is logged in by checking if 'user_id' is set in session
  if (isset($_SESSION['user_id'])) {
    // Check user role and redirect based on role
    if ($_SESSION['role'] == 'Guest' || $_SESSION['role'] == 'Student') {
      header("Location: ../user/join_queue.php"); // Redirect to student queue page
      exit();
    } elseif ($_SESSION['role'] == 'admin') {
      header("Location: ../admin/view_offices.php"); // Redirect to admin dashboard
      exit();
    }
  }
}


//FOR ADMIN
/// Check if admin_id is set in the session
function checkAdminLogin()
{
  if (!isset($_SESSION['admin_id'])) {
    // Redirect to login_Admin.php if admin_id is not defined
    header("Location: ../auth/loginadmin.php");
    exit();
}

}

//for logins
//check role and redirect to their respective dashboard
function checkRole()
{
  // Check if user is logged in by checking if 'user_id' is set in session
  if (isset($_SESSION['user_id'])) {
    // Check user role and redirect based on role
    if ($_SESSION['role'] == 'Employee') {
      header("Location: ../employee/employee_dashboard.php"); // Redirect to admin dashboard
      exit();
    } elseif ($_SESSION['role'] == 'Guest' || $_SESSION['role'] == 'Student') {
      header("Location: ../user/join_queue.php"); // Redirect to student queue page
      exit();
    }elseif ($_SESSION['role'] == 'admin') {
      header("Location: ../admin/view_offices.php"); // Redirect to student queue page
      exit();
    } 
  }
}

// Function to destroy the session (logout)
function logout()
{
  session_start();
  //change boolean logged_in to false in employee table if $_SESSION['role'] == 'employee'
  if ($_SESSION['role'] == 'Employee') {
    global $conn;
    $stmt = $conn->prepare("UPDATE employees SET logged_in = 0 WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
  }
  session_unset();
  session_destroy();
}
function tableExists($table_name)
{
  global $conn;

  // Escape the table name to prevent SQL injection
  $table_name = $conn->real_escape_string($table_name);

  // Directly use the escaped table name in the query
  $query = "SHOW TABLES LIKE '$table_name'";
  $result = $conn->query($query);

  return $result->num_rows > 0;
}
?>