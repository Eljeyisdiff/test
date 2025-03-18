<?php
require_once '../config/connection.php';
include '../api/queue_api.php';

$offices = getOffices();


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link id="nu-queuest-icon" rel="icon" href="../assets/nu_queuest.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/loginstyles.css">
    <link rel="stylesheet" href="../css/officeselection_public.css">
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
    <script type="text/javascript" src="public_queue.js"></script>
    <title>NU Queuest: Public View</title>
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
                <h2>NU Queuest Public View</h2>
            </div>
        </div>

        <div class="loginbody">
            <div class="welcome">
                <h1>Welcome</h1>
                <p>Please Select Office to continue</p>
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
            <div class="login-container">
                <div class="office-selection">
                    <form method="post" action="../api/select_office_api.php">
                        <div class="office-label">
                            <label for="office">Select Office:</label>
                        </div>
                        <div class="office-select">
                            <select name="office" id="office" required>
                                <?php foreach ($offices as $office) { ?>
                                    <option value="<?php echo $office['office_id']; ?>"><?php echo $office['office_name']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="submit-btn">
                            <button class="button-right save-changes" name="submit" type="submit"
                                value="Submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>