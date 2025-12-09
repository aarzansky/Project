<?php
session_start();
include 'connect.php';

// Check if admin is logged in
if(!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php');
    exit();
}

// Get hospital ID from session
if(!isset($_SESSION['hospital_id'])) {
    // Get hospital info from database
    $email = $_SESSION['admin_email'];
    $hospital_sql = "SELECT hospital_id FROM hospitals WHERE email='$email'";
    $hospital_result = mysqli_query($conn, $hospital_sql);
    if($hospital_result && mysqli_num_rows($hospital_result) > 0) {
        $hospital = mysqli_fetch_assoc($hospital_result);
        $_SESSION['hospital_id'] = $hospital['hospital_id'];
    } else {
        header('Location: logout.php');
        exit();
    }
}

$hospital_id = $_SESSION['hospital_id'];

// Update response status
if(isset($_POST['update_status'])) {
    $response_id = $_POST['response_id'];
    $new_status = $_POST['status'];
    
    $update_sql = "UPDATE donor_responses SET response_status = '$new_status' 
                   WHERE response_id = '$response_id'";
    
    if(mysqli_query($conn, $update_sql)) {
        $success_msg = "Status updated successfully!";
    } else {
        $error_msg = "Error updating status: " . mysqli_error($conn);
    }
}

// Get all donor responses
$responses_sql = "SELECT * FROM donor_responses ORDER BY response_date DESC";
$responses_result = mysqli_query($conn, $responses_sql);
$responses = [];
if($responses_result) {
    $responses = mysqli_fetch_all($responses_result, MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Responses - Hospital Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #c73030; color: white; }
        .status-pending { color: orange; font-weight: bold; }
        .status-accepted { color: green; font-weight: bold; }
        .status-rejected { color: red; font-weight: bold; }
        .status-completed { color: blue; font-weight: bold; }
        .success { color: green; background: #d4edda; padding: 10px; border-radius: 5px; }
        .error { color: red; background: #f8d7da; padding: 10px; border-radius: 5px; }
        select { padding: 5px; border-radius: 4px; }
    </style>
</head>
<body>
 <nav>
        <div class="brand">BloodConnect Hospital</div>
        <div class="links">
            <a href="hospital-dashboard.php">Dashboard</a>
            <a href="admin-approve.php">Pending Users</a>
            <a href="donor-response.php" class="active">Responses</a>
            <a href="manage_users.php">Manage Users</a>
        </div>
        <div class="uinfo">
            <span>Admin Panel</span>
            <a href="logout.php"><button class="logout">Logout</button></a>
        </div>
    </nav>    
    
    <div class="dashboard">
        <div class="header">
            <h1>Donor Responses</h1>
            <p>Manage donor responses to blood requests</p>
        </div>
        
        <!-- Messages -->
        <?php if(isset($success_msg)): ?>
            <div class="alert success"><?php echo $success_msg; ?></div>
        <?php endif; ?>
        
        <?php if(isset($error_msg)): ?>
            <div class="alert error"><?php echo $error_msg; ?></div>
        <?php endif; ?>
        
        <?php if(empty($responses)): ?>
            <div class="no-requests">
                <p>No responses yet.</p>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Response ID</th>
                        <th>Donor Info</th>
                        <th>Request Info</th>
                        <th>Contact Details</th>
                        <th>Response Date</th>
                        <th>Status</th>
                        <th>Update Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($responses as $response): ?>
                        <?php
                        // Get donor info - FIXED: Check if query succeeds
                        $donor_sql = "SELECT full_name, blood_type, phone_number, email 
                                     FROM donors WHERE donor_id='{$response['donor_id']}'";
                        $donor_result = mysqli_query($conn, $donor_sql);
                        
                        // Get request info - FIXED: Check if query succeeds
                        $request_sql = "SELECT blood_type, units, urgency, additional_notes, hospital_id 
                                       FROM blood_requests WHERE request_id='{$response['request_id']}'";
                        $request_result = mysqli_query($conn, $request_sql);
                        
                        // Only proceed if both queries return data
                        if($donor_result && $request_result && 
                           mysqli_num_rows($donor_result) > 0 && 
                           mysqli_num_rows($request_result) > 0) {
                            
                            $donor = mysqli_fetch_assoc($donor_result);
                            $request = mysqli_fetch_assoc($request_result);
                            
                            // Only show responses for this hospital's requests
                            if(isset($request['hospital_id']) && $request['hospital_id'] == $hospital_id):
                        ?>
                        
                        <tr>
                            <td><?php echo $response['response_id']; ?></td>
                            
                            <td>
                                <strong><?php echo htmlspecialchars($donor['full_name'] ?? 'N/A'); ?></strong><br>
                                Blood Type: <?php echo htmlspecialchars($donor['blood_type'] ?? 'N/A'); ?>
                            </td>
                            
                            <td>
                                <strong>Request #<?php echo $response['request_id']; ?></strong><br>
                                Needed: <?php echo htmlspecialchars($request['blood_type'] ?? 'N/A'); ?><br>
                                Units: <?php echo htmlspecialchars($request['units'] ?? 'N/A'); ?><br>
                                Urgency: <?php echo ucfirst($request['urgency'] ?? 'N/A'); ?>
                            </td>
                            
                            <td>
                                <strong>Phone:</strong> <?php echo htmlspecialchars($donor['phone_number'] ?? 'N/A'); ?><br>
                                <strong>Email:</strong> <?php echo htmlspecialchars($donor['email'] ?? 'N/A'); ?>
                            </td>
                            
                            <td><?php echo date('M j, Y g:i A', strtotime($response['response_date'])); ?></td>
                            
                            <td>
                                <span class="status-<?php echo $response['response_status']; ?>">
                                    <?php echo ucfirst($response['response_status']); ?>
                                </span>
                            </td>
                            
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="response_id" value="<?php echo $response['response_id']; ?>">
                                    <select name="status" onchange="this.form.submit()">
                                        <option value="pending" <?php echo $response['response_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="accepted" <?php echo $response['response_status'] == 'accepted' ? 'selected' : ''; ?>>Accepted</option>
                                        <option value="rejected" <?php echo $response['response_status'] == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                        <option value="completed" <?php echo $response['response_status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    </select>
                                    <input type="hidden" name="update_status" value="1">
                                </form>
                            </td>
                        </tr>
                        <?php 
                            endif;
                        }
                        ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>