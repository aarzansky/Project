<?php
session_start();
include 'connect.php';

// Check if admin is logged in
if(!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php');
    exit();
}

$hospital_id = $_SESSION['hospital_id'];
$success = '';
$error = '';

// Mark donor response as accepted (counts as 1 unit)
if(isset($_POST['accept_response'])) {
    $response_id = $_POST['response_id'];
    $request_id = $_POST['request_id'];
    
    // Update response to accepted
    $sql = "UPDATE donor_responses SET response_status = 'accepted' WHERE response_id = '$response_id'";
    mysqli_query($conn, $sql);
    
    // Check for auto-fulfillment
    checkAutoFulfillment($conn, $request_id);
    
    $success = "Response accepted!";
    header("Location: manage_requests.php");
    exit();
}

// Manual status update
if(isset($_POST['update_status'])) {
    $request_id = $_POST['request_id'];
    $status = $_POST['status'];
    
    $sql = "UPDATE blood_requests SET status = '$status' WHERE request_id = '$request_id'";
    if(mysqli_query($conn, $sql)) {
        $success = "Request status updated!";
        
        // If request is cancelled, reject all pending/accepted responses
        if($status == 'cancelled') {
            $update_sql = "UPDATE donor_responses 
                          SET response_status = 'rejected' 
                          WHERE request_id = '$request_id' 
                          AND response_status IN ('pending', 'accepted')";
            mysqli_query($conn, $update_sql);
        }
        
        // If manually marked as fulfilled, complete all accepted responses
        if($status == 'fulfilled') {
            $complete_sql = "UPDATE donor_responses 
                            SET response_status = 'completed' 
                            WHERE request_id = '$request_id' 
                            AND response_status = 'accepted'";
            mysqli_query($conn, $complete_sql);
        }
    } else {
        $error = "Error updating status";
    }
}

// Function to check and auto-fulfill requests
function checkAutoFulfillment($conn, $request_id) {
    // Count accepted responses
    $count_sql = "SELECT COUNT(*) as accepted_count FROM donor_responses 
                 WHERE request_id = '$request_id' AND response_status = 'accepted'";
    $count_result = mysqli_query($conn, $count_sql);
    $count_data = mysqli_fetch_assoc($count_result);
    $accepted_count = $count_data['accepted_count'];
    
    // Get total units needed
    $units_sql = "SELECT units, status FROM blood_requests WHERE request_id = '$request_id'";
    $units_result = mysqli_query($conn, $units_sql);
    $units_data = mysqli_fetch_assoc($units_result);
    $units_needed = $units_data['units'];
    
    // Auto-fulfill if reached units and still active
    if($accepted_count >= $units_needed && $units_data['status'] == 'active') {
        $update_sql = "UPDATE blood_requests SET status = 'fulfilled' WHERE request_id = '$request_id'";
        mysqli_query($conn, $update_sql);
        
        // Mark all accepted as completed
        $complete_sql = "UPDATE donor_responses SET response_status = 'completed' 
                        WHERE request_id = '$request_id' AND response_status = 'accepted'";
        mysqli_query($conn, $complete_sql);
    }
}

// Get all blood requests
$sql = "SELECT * FROM blood_requests WHERE hospital_id = '$hospital_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
$requests = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Blood Requests</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav>
        <div class="brand">BloodConnect Hospital</div>
        <div class="links">
            <a href="hospital-dashboard.php">Dashboard</a>
            <a href="admin-approve.php">Pending Users</a>
            <a href="donor-response.php">Responses</a>
            <a href="manage_requests.php" class="active">Manage Requests</a>
            <a href="manage_users.php">Manage Users</a>
        </div>
        <div class="uinfo">
            <span>Admin Panel</span>
            <a href="logout.php"><button class="logout">Logout</button></a>
        </div>
    </nav>
    
    <div class="dashboard">
        <div class="header">
            <h1>Manage Blood Requests</h1>
            <p>Update status and track fulfillment</p>
        </div>
        
        <?php if($success): ?>
            <div class="alert success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if(empty($requests)): ?>
            <div class="no-requests">
                <p>No blood requests found. <a href="hospital-dashboard.php">Create one</a></p>
            </div>
        <?php else: ?>
            <table class="container">
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Blood Type</th>
                        <th>Units</th>
                        <th>Urgency</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Update Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($requests as $request): 
                        // Get accepted donations count
                        $accepted_sql = "SELECT COUNT(*) as accepted FROM donor_responses 
                                         WHERE request_id = '{$request['request_id']}' 
                                         AND response_status = 'accepted'";
                        $accepted_result = mysqli_query($conn, $accepted_sql);
                        $accepted_data = mysqli_fetch_assoc($accepted_result);
                        $accepted_count = $accepted_data['accepted'];
                        
                        // Auto-check fulfillment on page load
                        if($request['status'] == 'active') {
                            checkAutoFulfillment($conn, $request['request_id']);
                            // Refresh request data if status changed
                            if($accepted_count >= $request['units']) {
                                $refresh_sql = "SELECT status FROM blood_requests WHERE request_id = '{$request['request_id']}'";
                                $refresh_result = mysqli_query($conn, $refresh_sql);
                                $refreshed = mysqli_fetch_assoc($refresh_result);
                                $request['status'] = $refreshed['status'];
                            }
                        }
                    ?>
                    <tr>
                        <td>#<?php echo $request['request_id']; ?></td>
                        <td>
                            <span class="bloodtype"><?php echo $request['blood_type']; ?></span>
                        </td>
                        <td>
                            <?php echo $accepted_count; ?>/<?php echo $request['units']; ?>
                            <?php if($accepted_count >= $request['units'] && $request['status'] == 'fulfilled'): ?>
                                <br><small style="color: #28a745;">✓ Fulfilled</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="urgency <?php echo $request['urgency']; ?>">
                                <?php echo ucfirst($request['urgency']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="status <?php echo $request['status']; ?>">
                                <?php echo ucfirst($request['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M j', strtotime($request['created_at'])); ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                                <select name="status" onchange="this.form.submit()" style="padding: 5px;">
                                    <option value="active" <?php echo $request['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="fulfilled" <?php echo $request['status'] == 'fulfilled' ? 'selected' : ''; ?>>Fulfilled</option>
                                    <option value="cancelled" <?php echo $request['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <input type="hidden" name="update_status" value="1">
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>