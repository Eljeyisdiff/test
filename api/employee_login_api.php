<?php 
session_start();

include_once '../api/login_api.php';
include_once '../auth/session.php';

//request method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //for employee login
    if (isset($_POST['email'])) {
        $email = $_POST['email'];
        // Password
        $password = $_POST['password'];

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['warningMessage'] = "Invalid email format.";
            $_SESSION['accountselect'] = 'employee';
            header("Location: ../auth/index.php");
            exit();
        }

        // Attempt to log in the employee
        $userId = loginEmployee($email, $password);

        // Check if user ID exists
        if (is_null($userId)) {
            $_SESSION['warningMessage'] = "Invalid email or password.";
            $_SESSION['accountselect'] = 'employee';
            header("Location: ../auth/index.php");
            exit();
        }

        // Check the account type of the user
        $accountType = getAccountType($email);

        // Ensure that the account type is 'employee'
        if ($accountType !== 'Employee') {
            $_SESSION['warningMessage'] = "This email is not registered as an employee.";
            header("Location: ../auth/index.php");
            exit();
        }

        // If account type is employee, check for employee ID
        $employeeId = checkEmployee($userId);

        // Check if employee ID exists
        if (empty($employeeId)) {
            $_SESSION['warningMessage'] = "Employee ID not found.";
            header("Location: ../auth/index.php");
            exit();
        }

        // Set session variables and redirect to employee dashboard
        $_SESSION['name'] = getEmployeeName($email);
        $_SESSION['user_id'] = $userId;
        $_SESSION['role'] = 'Employee';
        $_SESSION['employee_id'] = $employeeId;

        // Get the office ID of the employee
        $officeId = getOfficeIdOfEmployee($userId);

        $_SESSION['office_id'] = $officeId;
        insertLoggedIn($userId,$employeeId);
        header("Location: ../employee/employee_dashboard.php");
        exit();
    }

}