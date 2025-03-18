<?php
session_start();

include_once '../api/login_api.php';
include_once '../auth/session.php';

//check if logged in and redirect to their respective dashboard
checkRole();



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link id="nu-queuest-icon" rel="icon" href="../assets/nu_queuest.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/loginstyles.css">
    <link rel="shortcut icon" type="image/x-icon" href="../favicon.ico">
    <!-- fonts from google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400;1,700&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Armata&display=swap" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">
    <!-- icons from google -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=account_circle,mail,password" />
    <script type="text/javascript" src="scripts.js"></script>
    <title>NU Queuest: Login</title>
</head>

<body>
    <div class="main">
        <div class="header">
            <div class="nu-logo-login">
                <img src="../assets/NU-L_logo.png" alt="NU Laguna Logo">
            </div>
            <div class="nu-queuest-logo">
                <img src="../assets/nuqueuest-vector.svg" alt="NU QUEUEST">
            </div>
            <div class="login-header-sub">
                <h2>Skip the lines</h2>
                <p>Your fast pass to campus services</p>
            </div>
        </div>

        <div class="loginbody">
            <div class="welcome">
                <h1>Welcome to NU Laguna</h1>
                <p>Select how you'd like to proceed</p>
            </div>
            <!-- Warning message placeholder -->
            <div id="warning-message" style="color: red; font-weight: bold;">
                <?php
                if (!empty($_SESSION['warningMessage'])) {
                    echo $_SESSION['warningMessage'];
                    unset($_SESSION['warningMessage']); // Clear the warning message
                }
                ?>
            </div>
            <!-- note: autoselect student on load -->
            <select id="accountselect" onchange="changeForm()">
                <option value="student" selected>Student</option>
                <option value="employee">Employee</option>
                <option value="guest">Guest</option>
            </select>

            <div class="login-container">
                <div class="logins">
                    <!-- student -->
                    <div id="student" class="inv">
                        <form method="post" action="../api/user_login_api.php" onsubmit="setStudentCookie()">
                        <div class="email-entry">
                                <i class="material-symbols-outlined">account_circle</i>
                                <input type="text" name="studentname" placeholder="Full name" required>
                            </div>
                            <div class="email-entry">
                                <i class="material-symbols-outlined">mail</i>
                                <input type="text" name="studentemail" placeholder="email@students.nu-laguna.edu.ph"
                                    required>
                            </div>
                            <!-- <div class="password-entry"> -->
                                <!-- <i class="material-symbols-outlined">password</i> -->
                                <!-- <input type="password" name="password" placeholder="Password" required> -->
                            <!-- </div> -->
                            <div class="login-submit">
                                <input type="submit" class="loginbtn" name="loginbutton" value="Sign in">
                            </div>
                            <!-- <div class="otherLogin">
                                <p><input type="checkbox" name="rememberchk">Remember me</p>
                                <p><a href="">Forgot your password?</a> </p>
                            </div> -->
                        </form>
                        <div class="nuorms">
                            <p name="logindiv">OR</p>
                        </div>
                        <a class="MSbtn" href="">
                            <img src="https://www.microsoft.com/favicon.ico" alt="">
                            <p>Continue with Microsoft</p>
                        </a>
                    </div>
                    <!-- employee -->
                    <div id="employee" class="inv">
                        <form method="post" action="../api/employee_login_api.php" onsubmit="setEmployeeCookie()">
                            <div class="email-entry">
                                <i class="material-symbols-outlined">mail</i>
                                <input type="text" name="email" placeholder="email@nu-laguna.edu.ph" required>
                            </div>
                            <div class="password-entry">
                                <i class="material-symbols-outlined">password</i>
                                <input type="password" name="password" placeholder="Password" required>
                            </div>
                            <div class="login-submit">
                                <input type="submit" class="loginbtn" name="loginbutton" value="Sign in">
                            </div>
                            <!-- <div class="otherLogin">
                                <p><input type="checkbox" name="rememberchk">Remember me</p>
                                <p><a href="">Forgot your password?</a> </p>
                            </div> -->
                        </form>
                        <div class="nuorms">
                            <p name="logindiv">OR</p>
                        </div>
                        <a class="MSbtn" href="">
                            <img src="https://www.microsoft.com/favicon.ico" alt="">
                            <p>Continue with Microsoft</p>
                        </a>
                    </div>

                    <!-- guest -->
                    <div id="guest" class="inv">
                        <form method="post" action="../api/user_login_api.php" onsubmit="setGuestCookie()">
                            <div class="email-entry">
                                <i class="material-symbols-outlined">account_circle</i>
                                <input type="text" name="fullname" placeholder="Full name" required>
                            </div>
                            <div class="password-entry">
                                <i class="material-symbols-outlined">mail</i>
                                <input type="text" name="guestemail" placeholder="Email address" required>
                            </div>
                            <div class="login-submit">
                                <input type="submit" class="loginbtn" name="loginbutton" value="Continue as guest">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- javascript -->
            <script src="../js/login.js"></script>
            <div class="tos">
                <p>By clicking continue, you agree to our<br></r><a href="">Terms of Service</a> and <a href="">Privacy
                        Policy</a>.</p>
            </div>
        </div>
    </div>
</body>

</html>
