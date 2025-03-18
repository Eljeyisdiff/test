<?php 
require_once '../auth/session.php';
require_once '../config/connection.php';
include_once '../api/admin_api.php';
session_start();
checkAdminLogin();  // Ensure the user is admin

$name = getAdminName($_SESSION['admin_id']);

$firstName = explode(' ', trim($name))[0];

$result = getOffices();
// message if no offices available
if ($result->num_rows === 0) {
    $_SESSION['warningMessage'] = "No offices available at the moment.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link id="nu-queuest-icon" rel="icon" href="../assets/nu_queuest.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/admin_officesview.css">
    <link rel="shortcut icon" type="image/x-icon" href="../favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <!-- google font icons -->
<!--    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=account_circle,add,search,trending_down,trending_up" />-->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
   <script src="../js/admin_officesview.js"></script>

    <title>Admin Portal: Offices</title>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <h1>NU QUEUEST</h1>
            <p>Admin Portal</p>
        </div>
        <div class="header-right">
            <div class="header-buttons">
                <a href="../admin/admin_dashboard.php"><h2>Dashboard</h2></a>
                <h2 class="current-page">Offices</h2>
                <a href="../admin/admin_usersview.php"><h2>Employees</h2></a>
            </div>
            <div class="user">
                <p>Hi, <span class="account-name"><?php echo $firstName; ?></span></p>
                <i class="material-symbols-outlined account-pic">account_circle</i>
                <p class="logout"><a href="../auth/logout.php">Logout</a></p>
            </div>
        </div>
    </div>

    <div class="main">
    <div class="main-header">
        <h1>Oversee all office activities</h1>
    </div>
    <div class="main-div"></div>
    <div class="offices-container">
            <div class="offices-top-cont">
                <div class="office-search">
                    <div class="search-in">
                        <i class="material-symbols-outlined search">search</i>
                        <input type="search" id="officeSearchInput" name="office-search" placeholder="Search offices">
                    </div>
                </div>
                <div class="add-office" onclick="document.getElementById('addOfficeDialog').showModal();">
                    <i class="material-symbols-outlined add">add</i><p>Add office</p>
                </div>
            </div>
            <!-- Display offices -->
            <?php while ($row = $result->fetch_assoc()) { ?>
                <div class="office-dt-container office-item">
                    <div class="office-dt-element" id="office-id-<?php echo $row['office_id']; ?>">
                        <div class="office-dt-left">
                            <div class="office-details">
                                <h1 class="office-name"><?php echo $row['office_name']; ?></h1>
                                <p class="office-desc"><?php echo $row['office_description']; ?></p>
                            </div>
                        </div>
                        <div class="office-dt-right">
                            <div class="office-analytics">
                                <!-- Display the total number of people in queue across all offices -->
                                <div class="office-queue-num">
                                    <?php $totalQueueCount = getAllOfficeQueueCount(); ?>
                                    <h2 class="office-queue-count"><?php echo $totalQueueCount ?></h2>
                                    <p>No. of People on all queues</p>
                                </div>
                                <div class="office-transactions">
                                    <?php $completedTransactions = getOfficesCompletedTransactCount($row['office_id']); ?>
                                    <h2 class="office-transactions-count"><?php echo $completedTransactions; ?></h2>
                                    <p>Completed Transactions</p>
                                </div>
                            </div>
                            <div class="office-action-btns">
                                <a id="viewLink" href="view_office_details.php?office_id=<?php echo $row['office_id']; ?>">
                                    <h3>View</h3>
                                </a>
                                <div class="edit-office-btn office-btn" onclick="openEditOfficeDialog(<?php echo $row['office_id']; ?>)">
                                    <h3>Edit</h3>
                                </div>
                                <div class="delete-office-btn office-btn" onclick="openDeleteOfficeDialog(<?php echo $row['office_id']; ?>)">
                                    <h3>Delete</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
    </div>
</div>

    <dialog id="addOfficeDialog">
        <div class="title-bar">
            <h5 class="dialog-title">Add New Office</h5>
            <div class="close-button icon-close" onclick="document.getElementById('addOfficeDialog').close();"></div>
        </div>
        <div class="line-separator"></div>
        <div class="modal-body">
            <form id="addOfficeForm" method="POST">
                <div id="warning-message" style="color: green; font-weight: bold;"></div>
                <div class="mb-3">
                    <label for="newOfficeName" class="form-label">Office Name</label>
                    <input type="text" class="form-control" id="newOfficeName" name="office_name" required>
                </div>
                <div class="mb-3">
                    <label for="newOfficePrefix" class="form-label">Office Prefix</label>
                    <input type="text" class="form-control" id="newOfficePrefix" name="office_prefix" required>
                </div>
                <div class="mb-3">
                    <label for="newOfficeDescription" class="form-label">Office Description</label>
                    <textarea class="form-control" id="newOfficeDescription" name="office_description" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Purposes</label>
                    <div id="purposes-container">
                        <input type="text" class="form-control mb-2" name="purposes[]" placeholder="Enter purpose" required>
                    </div>
                    <button type="button" class="btn btn-secondary mt-2 add-purpose-btn">Add Purpose Field</button>
                </div>

            </form>
        </div>
        <div class="action-bar">
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('addOfficeDialog').close();">Cancel</button>
            <button type="submit" class="btn btn-primary" form="addOfficeForm">Save Office</button>
        </div>
    </dialog>


    <dialog id="editOfficeDialog">
    <div class="title-bar">
        <h5 class="dialog-title">Edit Office</h5>
        <div class="close-button icon-close" onclick="closeEditOfficeDialog();"></div>
    </div>
    <div class="line-separator"></div>
    <div class="modal-body">
        <form id="editOfficeForm" method="POST">
            <div id="alert-box" style="color: green; font-weight: bold;"></div>
            <input type="hidden" id="officeId" name="office_id">
            <div class="mb-3">
                <label for="officeName" class="form-label">Office Name</label>
                <input type="text" class="form-control" id="officeName" name="office_name" required>
            </div>
            <div class="mb-3">
                <label for="officePrefix" class="form-label">Office Prefix</label>
                <input type="text" class="form-control" id="officePrefix" name="office_prefix" required>
            </div>
            <div class="mb-3">
                <label for="officeDescription" class="form-label">Office Description</label>
                <textarea class="form-control" id="officeDescription" name="office_description" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Purposes</label>
                <div id="editPurposesContainer"></div>
                <button type="button" class="btn btn-secondary mt-2 add-purpose-btn">Add Purpose Field</button>
            </div>
        </form>
    </div>
    <div class="action-bar">
        <button type="submit" class="btn btn-secondary" onclick="closeEditOfficeDialog();">Cancel</button>
        <button type="submit" class="btn btn-primary" form="editOfficeForm">Save Changes</button>
    </div>
</dialog>

<dialog id="deleteOfficeDialog">
    <div class="title-bar">
        <h5 class="dialog-title">Delete Office</h5>
        <!-- <div class="close-button icon-close" onclick="closeDeleteOfficeDialog();"></div> -->
    </div>
    <div class="line-separator"></div>
    <div class="modal-body">
        <p>Are you sure you want to delete this office?</p>
        <form id="deleteOfficeForm" method="POST">
            <input type="hidden" id="deleteOfficeId" name="office_id">
        </form>
    </div>
    <div class="action-bar">
        <button type="button" class="btn btn-secondary" onclick="closeDeleteOfficeDialog();">Cancel</button>
        <button type="submit" class="btn btn-primary" form="deleteOfficeForm">Delete</button>
    </div>

</dialog>
</body>
</html>
