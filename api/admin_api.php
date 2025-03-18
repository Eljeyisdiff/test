<?php 
require_once '../auth/session.php';
require_once '../config/connection.php';


// Function to insert a new office and its purposes
function addOffice($office_name, $prefix, $office_description, $purposes) {
    global $conn;

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Insert the office first
        $sql = "INSERT INTO offices (office_name, prefix, office_description) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $office_name, $prefix, $office_description);

        if ($stmt->execute()) {
            // Get the last inserted office ID
            $office_id = $conn->insert_id;

            // Prepare the purpose insert statement
            $purpose_sql = "INSERT INTO office_purpose (office_id, purpose) VALUES (?, ?)";
            $purpose_stmt = $conn->prepare($purpose_sql);

            // Add "Others" first if it's not already included
            if (!in_array("Others", $purposes)) {
                $purpose_stmt->bind_param("is", $office_id, $others_purpose);
                $others_purpose = "Others"; 
                $purpose_stmt->execute();
            }

            // Insert the remaining purposes
            foreach ($purposes as $purpose) {
                if ($purpose !== "Others") { 
                    $purpose_stmt->bind_param("is", $office_id, $purpose);
                    $purpose_stmt->execute();
                }
            }

            // Commit the transaction
            $conn->commit();
            return ['success' => true, 'message' => 'Office added successfully', 'office_id' => $office_id];
        } else {
            throw new Exception("Error adding office");
        }
    } catch (mysqli_sql_exception $e) {
        // Rollback on error
        $conn->rollback();

        // Handle specific duplicate entry error
        if ($e->getCode() === 1062) {
            if (strpos($e->getMessage(), 'office_name_UNIQUE') !== false) {
                return ['success' => false, 'message' => "Error: The office name '$office_name' already exists."];
            } elseif (strpos($e->getMessage(), 'prefix_UNIQUE') !== false) {
                return ['success' => false, 'message' => "Error: The prefix '$prefix' already exists."];
            }
        }
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

// Get office details by ID
function getOfficeById($officeId) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM offices WHERE office_id = ?");
    $stmt->bind_param("i", $officeId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc(); // Return the office details as an associative array
}

// Get office name by ID
function getOfficeName($officeId) {
    global $conn;
    $stmt = $conn->prepare("SELECT office_name FROM offices WHERE office_id = ?");
    $stmt->bind_param("i", $officeId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['office_name']; // Return the office name
}
// Get all offices

function getOffices() {
    global $conn;
    $sql = "SELECT office_id, prefix, office_name, office_description FROM offices";
    return $conn->query($sql);
}

// Get office name only
function getOfficeNamesOnly() {

    global $conn;

    $query = "SELECT office_id, office_name FROM offices";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    $offices = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $offices;

}

// Delete an office
function deleteOffice($officeId) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM offices WHERE office_id = ?");
    $stmt->bind_param("i", $officeId);
    return $stmt->execute(); // Return true on success, false on failure
}

//to update office details
function updateOffice($officeId, $officeName, $officePrefix, $officeDescription) {
    global $conn;
    $stmt = $conn->prepare("UPDATE offices SET office_name = ?, prefix = ?, office_description = ? WHERE office_id = ?");
    
    if ($stmt === false) {
        return ['success' => false, 'message' => 'Query preparation failed.'];
    }

    $stmt->bind_param("sssi", $officeName, $officePrefix, $officeDescription, $officeId);

    if ($stmt->execute()) {
        return ['success' => true];
    } else {
        return ['success' => false, 'message' => 'Update failed: ' . $stmt->error];
    }
}

//add employee 
function addEmployee($employee_name, $employee_email, $employee_password, $employee_office, $window_number) {
    global $conn;

    // Start a transaction
    $conn->begin_transaction();

    // Hash the password
    $employee_password = password_hash($employee_password, PASSWORD_DEFAULT);

    try {
        // Insert the user first
        $sql = "INSERT INTO users (full_name, email, password, account_type) VALUES (?, ?, ?, 'Employee')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $employee_name, $employee_email, $employee_password);

        if ($stmt->execute()) {
            // Get the last inserted user ID
            $user_id = $conn->insert_id;

            // Insert the employee
            $employee_sql = "INSERT INTO employees (user_id, office_id) VALUES (?, ?)";
            $employee_stmt = $conn->prepare($employee_sql);
            $employee_stmt->bind_param("ii", $user_id, $employee_office);

            if ($employee_stmt->execute()) {
                // Get the last inserted employee ID
                $employee_id = $conn->insert_id;
                // Insert into office_windows
                $window_sql = "INSERT INTO office_windows (employee_id, office_id, window_number) VALUES (?, ?, ?)";
                $window_stmt = $conn->prepare($window_sql);
                $window_stmt->bind_param("iis", $employee_id, $employee_office, $window_number);

                if ($window_stmt->execute()) {
                    // Commit the transaction
                    $conn->commit();
                    return ['success' => true, 'message' => 'Employee and window added successfully', 'user_id' => $user_id];
                } else {
                    throw new Exception("Error adding window data");
                }
            } else {
                throw new Exception("Error adding employee");
            }
        } else {
            throw new Exception("Error adding user");
        }
    } catch (mysqli_sql_exception $e) {
        // Rollback on error
        $conn->rollback();

        // Handle specific duplicate entry error
        if ($e->getCode() === 1062) {
            if (strpos($e->getMessage(), 'email_UNIQUE') !== false) {
                return ['success' => false, 'message' => "Error: The email '$employee_email' already exists."];
            }
        }
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}


//get employees
function getEmployees() {
    global $conn;
    $sql = "SELECT e.employee_id, u.full_name, u.email, o.office_name FROM employees e
            JOIN users u ON e.user_id = u.user_id
            JOIN offices o ON e.office_id = o.office_id";
    return $conn->query($sql);
}

function peopleInQueue() {
    global $conn;
    $sql = "SELECT COUNT(*) as people_in_queue FROM temp_queue";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['people_in_queue'];
}


function getAdminName($admin_id) {
    global $conn;
    $sql = "SELECT admin_name FROM admins WHERE admin_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['admin_name'];
}

// Function to get queue count for an office
function getOfficeQueueCount($office_id)
{
    global $conn;
    $sql = "SELECT COUNT(*) as count FROM temp_queue WHERE office_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $office_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['count'];
}

function getOfficesCompletedTransactCount($office_id)
{
    global $conn;
    $sql = "SELECT COUNT(*) as count FROM tickets WHERE office_id = ? AND ticket_status = 'Completed'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $office_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['count'];
}

// Function to get all tickets by office
function getAllTicketsWithUserDetailsByOffice($officeId) {
    global $conn;
    $sql = "SELECT t.*, u.full_name, u.account_type 
            FROM tickets t
            JOIN users u ON t.user_id = u.user_id
            WHERE t.office_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $officeId);
    $stmt->execute();
    return $stmt->get_result();
}

function getCompletedTransactions($officeId, $fromDate, $toDate) {
    global $conn;

    // Base query
    $query = "SELECT t.queue_number, u.full_name, u.account_type, t.service_details, t.created_at, t.sevice_ended_at
              FROM tickets t
              JOIN users u ON t.user_id = u.user_id
              WHERE t.office_id = ? AND t.ticket_status = 'completed'";

    // Add date filters if provided
    if ($fromDate) {
        $query .= " AND DATE(created_at) >= ?";
    }
    if ($toDate) {
        $query .= " AND DATE(created_at) <= ?";
    }

    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    // Bind parameters
    if ($fromDate && $toDate) {
        $stmt->bind_param("iss", $officeId, $fromDate, $toDate);
    } elseif ($fromDate) {
        $stmt->bind_param("is", $officeId, $fromDate);
    } elseif ($toDate) {
        $stmt->bind_param("is", $officeId, $toDate);
    } else {
        $stmt->bind_param("i", $officeId);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}

function getAllOfficeQueueCount() {
    // Assuming you have a database connection established
    global $conn;

    $query = "SELECT COUNT(*) as total_queue_count FROM temp_queue";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total_queue_count'];
    } else {
        // Handle query error
        return 0;
    }
}

function getOfficePurposes($office_id) {
    global $conn;
    $sql = "SELECT purpose FROM office_purpose WHERE office_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $office_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $purposes = [];
    while ($row = $result->fetch_assoc()) {
        $purposes[] = $row['purpose'];
    }
    return $purposes;
}


function countTotalCompletedTransactions() {
    global $conn; // Ensure you have access to the database connection

    $query = "SELECT COUNT(*) as total FROM tickets WHERE ticket_status = 'completed'";
    $result = $conn->query($query);

    if ($result) {
        $row = $result->fetch_assoc();
        return $row['total'];
    } else {
        return 0; // Return 0 if the query fails
    }
}


//get Active Total Offices
function getActiveAndTotalOffices() {
    global $conn; // Ensure you have access to the database connection

    // Get the total number of offices
    $queryTotal = "SELECT COUNT(*) as total FROM offices";
    $resultTotal = $conn->query($queryTotal);
    $totalOffices = $resultTotal ? $resultTotal->fetch_assoc()['total'] : 0;

    // Get the number of active offices (offices with recent service ended)
    $queryActive = "
        SELECT COUNT(DISTINCT office_id) as active 
        FROM tickets 
        WHERE sevice_ended_at >= NOW() - INTERVAL 1 DAY"; // Adjust the interval as needed
    $resultActive = $conn->query($queryActive);
    $activeOffices = $resultActive ? $resultActive->fetch_assoc()['active'] : 0;

    return [
        'active' => $activeOffices,
        'total' => $totalOffices
    ];
}

//get Total Employees

function getTotalLoggedInEmployeeCount() {
    global $conn; // Ensure you have access to the database connection

    // Get the total number of logged-in users with the "employee" role
    $queryTotalLoggedInEmployees = "SELECT COUNT(*) as total FROM employees WHERE logged_in = 1";
    $resultTotalLoggedInEmployees = $conn->query($queryTotalLoggedInEmployees);
    $totalLoggedInEmployees = $resultTotalLoggedInEmployees ? $resultTotalLoggedInEmployees->fetch_assoc()['total'] : 0;

    return $totalLoggedInEmployees;
}

//get the full names of users with the role "employee" and their associated office IDs

// function getEmployeeNamesAndOfficeIds() {
//     global $conn; 

//     $query = "SELECT u.full_name, e.office_id FROM users u JOIN employees e ON u.user_id = e.user_id";
//     $result = $conn->query($query);

//     if ($result) {
//         return $result->fetch_all(MYSQLI_ASSOC);
//     } else {
//         return [];
//     }

//         // $query = "
//         // SELECT 
//         //     u.user_id,
//         //     u.full_name,
//         //     e.office_id,
//         //     o.office_name
//         // FROM 
//         // JOIN 
//         //     employees e ON u.user_id = e.user_id
//         // JOIN 
//         //     offices o ON e.office_id = o.office_id
//         // WHERE 
//         //     u.role = 'employee';
//         // ";

//         // $result = $conn->query($query);

//         // if ($result) {
//         //     return $result->fetch_all(MYSQLI_ASSOC);
//         // } else {
//         //     return [];
//         // }

//                 // $result = $conn->query($query);

//                 // return $result;
//         }

function getEmployeeNamesAndOfficeIds() {
    global $conn; 
    $query = "SELECT employee_name, office_id, office_name FROM employees"; 
    return $conn->query($query);
}


function getEmployeeDetails() {
    global $conn; 
    $query = "
        SELECT 
            employees.employee_id,
            users.full_name, 
            offices.office_name, 
            office_windows.window_number,
            office_windows.window_status
        FROM 
            employees
        JOIN 
            users ON employees.user_id = users.user_id
        JOIN 
            offices ON employees.office_id = offices.office_id
        JOIN 
            office_windows ON employees.employee_id = office_windows.employee_id
        WHERE 
            users.account_type = 'employee';
    ";
    $result = $conn->query($query);
    $employees = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $employees[] = $row;
        }
    }

    return $employees;
}
// Fetch traffic data
function get_traffic_data($conn) {
    $trafficDataQuery = "
        SELECT 
            offices.office_name, 
            COUNT(tickets.ticket_id) AS traffic_count
        FROM 
            tickets
        JOIN 
            offices ON tickets.office_id = offices.office_id
        WHERE 
            tickets.ticket_status IN ('Serving', 'Waiting')
        GROUP BY 
            offices.office_name;
    ";

    $result = $conn->query($trafficDataQuery);
    $trafficData = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $trafficData[] = [$row['office_name'], (int)$row['traffic_count'], 'color: #76A7FA']; // Example color
        }
    }

    return $trafficData;
}

// Fetch traffic data
$trafficData = get_traffic_data($conn);

// Fetch transaction data
$transactionQuery = "
    SELECT 
        DATE(tickets.created_at) AS date,
        tickets.ticket_status,
        COUNT(tickets.ticket_id) AS count
    FROM 
        tickets
    GROUP BY 
        DATE(tickets.created_at), tickets.ticket_status
    ORDER BY 
        DATE(tickets.created_at);
";

$result = $conn->query($transactionQuery);
$transactionData = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $transactionData[$row['date']][$row['ticket_status']] = (int)$row['count'];
    }
}
?>