<?php 
require_once '../auth/session.php';
require_once '../config/connection.php';
include_once '../api/admin_api.php';
session_start();
checkAdminLogin();  // Ensure the user is admin

$name = getAdminName($_SESSION['admin_id']);

$firstName = explode(' ', trim($name))[0];

$offices = getOfficeNamesOnly();



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link id="nu-queuest-icon" rel="icon" href="../assets/nu_queuest.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/admin_usersview.css">
    <link rel="shortcut icon" type="image/x-icon" href="../favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <!-- google font icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=account_circle,add,filter_alt,search,trending_down,trending_up" />
    <script src="../js/admin_usersview.js" defer></script>
    <title>Admin Portal: Employees</title>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <h1>NU QUEUEST</h1>
            <p>Admin Portal</p>
        </div>
        <div class="header-right">
            <div class="header-buttons">
                <a href="admin_officesview.php"><h2 >Dashboard</h2></a>
                <a href="../admin/admin_officesview.php"><h2>Offices</h2></a>
                <h2 class="current-page">Employees</h2>
            </div>
            <div class="user">
                <p>Hi, <span class="account-name"><?php echo$firstName?></span></p>
                <i class="material-symbols-outlined account-pic">account_circle</i>
                <p class="logout"><a href="../auth/logout.php">Logout</a></p>
            </div>
        </div>
    </div>

    <div class="main">
        <div class="main-header">
            <h1>Monitor and manage all users</h1>
        </div>
        
        <div class="main-div"></div>

        <div class="main-content-container">
        <div class="users-top-cont">
        <div class="user-search">
            <div class="search-in">
                <i class="material-symbols-outlined search">search</i>
                <input type="search" id="user-search" name="user-search" placeholder="Search Users">
            </div>
        </div>
                
                <div class="add-user" onclick="document.getElementById('dialog-add-user').showModal();">
                    <i class="material-symbols-outlined add" >add</i><p>Add Employees</p>
                </div>
                <div class="filter-by-office">
                <button class="office-flt-drp"><i class="material-symbols-outlined search">filter_alt</i>Filter by office</button>
                <div class="office-flt-options">
                    <a href="#" class="office-filter-option" data-office-id="">All Offices</a>
                    <?php $offices = getOfficeNamesOnly(); ?>
                    <?php foreach ($offices as $office) { ?>
                        <a href="#" class="office-filter-option" data-office-id="<?php echo $office['office_id']; ?>"><?php echo $office['office_name']; ?></a>
                    <?php } ?>
                </div>
            </div>
            </div>
            <div class="user-list-container">
                <div class="user-list-headers">
                    <div class="user-name-header">
                        <h2>Employee Name</h2>
                    </div>
                    <div class="office-name-header">
                        <h2>Office</h2>
                    </div>
                    <div class="assigned-window-header">
                        <h2>Assigned Window</h2>
                    </div>
                    <div class="window-status-header">
                        <h2>Window Status</h2>
                    </div>
                    <div class="action-header">
                        <h2>Actions</h2>
                    </div>
                </div>
                <?php 
                        $employees = getEmployeeDetails();
                        ?>
                <div class="user-list">
                    <?php foreach ($employees as $employee) { 

                         $statusColor = '';
                         if ($employee['window_status'] == 'on_break') {
                            $windowStat = "On Break";
                             $statusColor = '#f25325'; //orange
                         } elseif ($employee['window_status'] == 'closed') {
                            $windowStat = "Closed";
                             $statusColor = '#990000';
                         } elseif ($employee['window_status'] == 'open') {
                            $windowStat = "Open";
                             $statusColor = 'green';
                         } elseif (empty($employee['window_status'])) {
                            $windowStat = null;
                        }
                         
                        ?>
                        <div class="user-list-element users-item" data-employee-id="<?php echo $employee['employee_id']; ?>">
                            <div class="user-name">
                                <p><?php echo $employee['full_name']; ?></p>
                            </div>
                            <div class="office-name">
                                <p><?php echo $employee['office_name']; ?></p>
                            </div>
                            <div class="assigned-window">
                                <p><?php echo $employee['window_number']; ?></p>
                            </div>
                            <div class="window-status" >
                                <p style="color: <?php echo $statusColor; ?>;"><?php echo $windowStat; ?></p>
                            </div>
                            <div class="user-action-btns">
                     
                        <div class="edit-user-btn action-btn" id="edit-user-btn-<?php echo $employee['employee_id']; ?>" onclick="openEditUserDialog(<?php echo $employee['employee_id']; ?>)">
                        <h3>Edit</h3>
                        </div>
                        <div class="delete-user-btn action-btn" onclick="openDeleteUserDialog(<?php echo $employee['employee_id']; ?>)">
                <h3>Delete</h3>
            </div> 
                    </div>
                        </div>
                    <?php } ?>
            </div>
        </div>
    </div>

    <!-- dialog add user -->
    <dialog id="dialog-add-user" class="modal">
            <div class="title-bar"  id="dialog-add-user-title-bar">
                <p class="title office" id="dialog-title">Add User</p>
                <div class="close-button icon-close" onclick="closeAddUserDialog()"></div>
            </div>
            <div class="line-separator"></div>

            
            <form id="add-user-information">
                
            <div id="warning-message" style="color: green; font-weight: bold; text-align:center; margin: 10px"></div>
                <div class="dialog-body">
                    <div class="entry name">
                        <label for="employee-name" class="form-label">Employee Name</label>
                        <input type="text" id="employee-name" name="user_name" placeholder="Name" required>
                    </div>
                    <div class="entry">
                        <label for="assigned-office" class="form-label">Assigned Office</label>
                        <select name="office_name" id="assigned-office">
                            <?php foreach ($offices as $office) { ?>
                                <option data-office-id="<?php echo $office['office_id']; ?>" value="<?php echo $office['office_name']; ?>"><?php echo $office['office_name']; ?></option>
                            <?php } ?> 
                        </select>
                    </div>
                    <div class="entry">
                        <label for="assigned-window-add">Assigned Window</label>
                        <input type="text" name="window_number" id="assigned-window-add" required>
                    </div>
                    <div class="entry">
                        <label for="email-add">Email</label>
                        <input type="email" name="email" id="email-add" required>
                    </div>
                    <div class="entry">
                        <label for="password-add">Password</label>
                        <input type="password" name="password" id="password-add" required>
                    </div>
                </div>
            </form>
            <div class="action-bar">
                <button class="close-button go-back" name="cancel" type="button" value="" onclick="closeAddUserDialog()">Cancel</button>
                <button class="button-right add-button" name="add_user" type="submit" form="add-user-information" value="Submit">Add User</button>
            </div>
        </dialog>
   
    <!-- dialog edit user -->
    <dialog id="dialog-edit-user" class="modal">
    <div class="title-bar">
        <p class="title office" id="dialog-title">Edit Employee Details</p>
        <!-- <div class="close-button icon-close"></div> -->
    </div>
    <div class="line-separator"></div>
    <div class="alert alert-success" role="alert"></div>
    <form id="user-information" method="POST">
        <div class="dialog-body">
            <input type="hidden" name="employee_id" id="employee_id">
            <div class="entry name">
                <label for="employee-name-edit">Employee Name</label>
                <input type="text" id="employee-name-edit" name="full_name" required>
            </div>
                        <div class="entry">
                            <label for="assigned-office-edit" class="form-label">Assigned Office</label>
                            <select name="office_name" id="assigned-office-edit">
                                <?php foreach ($offices as $office) { ?>
                                <option data-office-id="<?php echo $office['office_id']; ?>" value="<?php echo $office['office_name']; ?>" <?php echo (isset($employee['office_name']) && $employee['office_name'] == $office['office_name']) ? 'selected' : ''; ?>><?php echo $office['office_name']; ?></option>
                                <?php } ?> 
                            </select>
                        </div>
                        <div class="entry">
                            <label for="assigned-window-edit">Assigned Window</label>
                            <input type="text" name="window_number" id="assigned-window-edit" value="<?php echo isset($employee['window_number']) ? $employee['window_number'] : ''; ?>" required>
                        </div>
                        <div class="entry">
                            <label for="email-edit">Email</label>
                            <input type="email" name="email" id="email-edit" required>
                        </div>
                        <div class="entry">
                            <label for="password-edit">Password</label>
                            <input type="password" name="password" id="password-edit" placeholder="Add new password" required>
                        </div>
                    </form>
            </div>
            <div class="action-bar">
                <button class="close-button go-back" name="cancel" type="button" value="" onclick="closeEditUserDialog()">Cancel</button>
                <button class="button-right save-changes" name="save_changes" type="submit" form="user-information" value="Submit">Save Changes</button>
            </div>
    </dialog>

    <dialog id="dialog-delete-user" class="modal">
        <div class="title-bar">
                    <div>
                        <p class="title office" id="dialog-title">Warning</p>
                    </div>
                    <div class="close-button icon-close"></div>
                </div>
                <div class="line-separator"></div>
                <div id="warning-message" style="color: green; font-weight: bold;"></div>
                <div class="dialog-body">
        <p><span>Confirm delete this user?</span> This action cannot be undone.</p>
    </div>
    <form id="deleteEmployeeForm" method="POST">
        <div class="action-bar">
            <input type="hidden" id="deleteEmployeeId" name="employee_id">
            <input type="hidden" name="_method" value="DELETE">
            <!-- <button class="close-button go-back" name="cancel" type="button" value="">Cancel</button> -->
            <button class="button-right delete-user" id="confirm-delete" name="delete_user" type="submit" value="Submit">Delete User</button>
        </div>
    </form>
</dialog>
</body>
</html>
