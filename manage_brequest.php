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
    
    // Count accepted responses for this request
    $count_sql = "SELECT COUNT(*) as accepted_count FROM donor_responses 
                 WHERE request_id = '$request_id' AND response_status = 'accepted'";
    $count_result = mysqli_query($conn, $count_sql);
    $count_data = mysqli_fetch_assoc($count_result);
    $accepted_count = $count_data['accepted_count'];
    
    // Get total units needed
    $units_sql = "SELECT units FROM blood_requests WHERE request_id = '$request_id'";
    $units_result = mysqli_query($conn, $units_sql);
    $units_data = mysqli_fetch_assoc($units_result);
    $units_needed = $units_data['units'];
    
    // AUTO-FULFILLMENT: If accepted responses meet needed units, update status
    if($accepted_count >= $units_needed) {
        $update_sql = "UPDATE blood_requests SET status = 'fulfilled' WHERE request_id = '$request_id'";
        mysqli_query($conn, $update_sql);
        
        // Also mark all accepted responses as completed
        $complete_sql = "UPDATE donor_responses SET response_status = 'completed' 
                        WHERE request_id = '$request_id' AND response_status = 'accepted'";
        mysqli_query($conn, $complete_sql);
    }
    
    $success = "Response accepted!";
    
    // Refresh page
    header("Location: manage_requests.php");
    exit();
}

// Mark donor response as completed (for manual completion if needed)
if(isset($_POST['complete_donation'])) {
    $response_id = $_POST['response_id'];
    
    // Update response to completed
    $sql = "UPDATE donor_responses SET response_status = 'completed' WHERE response_id = '$response_id'";
    mysqli_query($conn, $sql);
    
    $success = "Donation marked as completed!";
    
    // Refresh page
    header("Location: manage_requests.php");
    exit();
}

// Manual status update (for cancelling or other reasons)
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
    } else {
        $error = "Error updating status";
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
    <style>
        .request-details {
            background: #f8f9fa;
            padding: 15px;
            margin-top: 10px;
            border-left: 4px solid #c73030;
        }
        .donor-response {
            background: white;
            padding: 10px;
            margin: 5px 0;
            border-radius: 4px;
            border-left: 3px solid #ddd;
        }
        .response-completed {
            border-left-color: #28a745;
            background: #f8fff8;
        }
        .response-accepted {
            border-left-color: #ffc107;
            background: #fffdf5;
        }
        .response-pending {
            border-left-color: #6c757d;
            background: #f8f9fa;
        }
        .fulfilled-badge {
            background: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: bold;
        }
    </style>
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
            <p>Accept donor responses and track fulfillment</p>
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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($requests as $request): 
                        // Get accepted donations count (each accepted response = 1 unit)
                        $accepted_sql = "SELECT COUNT(*) as accepted FROM donor_responses 
                                         WHERE request_id = '{$request['request_id']}' 
                                         AND response_status = 'accepted'";
                        $accepted_result = mysqli_query($conn, $accepted_sql);
                        $accepted_data = mysqli_fetch_assoc($accepted_result);
                        $accepted_count = $accepted_data['accepted'];
                        
                        // Check if request is auto-fulfilled
                        $is_fulfilled = ($accepted_count >= $request['units']) && $request['status'] == 'active';
                        if($is_fulfilled) {
                            // Auto-update status if not already updated
                            $update_sql = "UPDATE blood_requests SET status = 'fulfilled' WHERE request_id = '{$request['request_id']}'";
                            mysqli_query($conn, $update_sql);
                            $request['status'] = 'fulfilled';
                        }
                    ?>
                    <tr>
                        <td><strong>#<?php echo $request['request_id']; ?></strong></td>
                        <td><span class="bloodtype"><?php echo $request['blood_type']; ?></span></td>
                        <td>
                            <?php echo $accepted_count; ?>/<?php echo $request['units']; ?>
                            <?php if($accepted_count >= $request['units'] && $request['status'] == 'fulfilled'): ?>
                                <span class="fulfilled-badge">✓ Fulfilled</span>
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
                        <td>
                            <?php if($request['status'] == 'active'): ?>
                                <a href="manage_requests.php?view=<?php echo $request['request_id']; ?>#request-<?php echo $request['request_id']; ?>" 
                                   style="color: #c73030; text-decoration: none;">
                                    Manage Donors
                                </a>
                            <?php else: ?>
                                <a href="manage_requests.php?view=<?php echo $request['request_id']; ?>#request-<?php echo $request['request_id']; ?>" 
                                   style="color: #666; text-decoration: none;">
                                    View Details
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    
                    <?php 
                    // Show details if this request is being viewed
                    if(isset($_GET['view']) && $_GET['view'] == $request['request_id']): 
                        // Get all responses for this request
                        $responses_sql = "SELECT * FROM donor_responses 
                                         WHERE request_id = '{$request['request_id']}' 
                                         ORDER BY response_date DESC";
                        $responses_result = mysqli_query($conn, $responses_sql);
                        $responses = mysqli_fetch_all($responses_result, MYSQLI_ASSOC);
                    ?>
                    <tr id="request-<?php echo $request['request_id']; ?>">
                        <td colspan="6" style="padding: 0;">
                            <div class="request-details">
                                <h4>Request #<?php echo $request['request_id']; ?> Details:</h4>
                                <p><strong>Created:</strong> <?php echo date('M j, Y g:i A', strtotime($request['created_at'])); ?></p>
                                <?php if(!empty($request['additional_notes'])): ?>
                                    <p><strong>Notes:</strong> <?php echo htmlspecialchars($request['additional_notes']); ?></p>
                                <?php endif; ?>
                                
                                <div style="display: flex; justify-content: space-between; align-items: center; margin: 15px 0;">
                                    <h4 style="margin: 0;">Donor Responses (<?php echo count($responses); ?>):</h4>
                                    <?php if($request['status'] == 'active'): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                                            <select name="status" onchange="this.form.submit()" style="padding: 5px;">
                                                <option value="">Cancel Request</option>
                                                <option value="cancelled">Cancel This Request</option>
                                            </select>
                                            <input type="hidden" name="update_status" value="1">
                                        </form>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if(empty($responses)): ?>
                                    <p>No responses yet.</p>
                                <?php else: ?>
                                    <?php foreach($responses as $response): 
                                        // Get donor info
                                        $donor_sql = "SELECT full_name, phone_number, email FROM donors WHERE donor_id = '{$response['donor_id']}'";
                                        $donor_result = mysqli_query($conn, $donor_sql);
                                        $donor = mysqli_fetch_assoc($donor_result);
                                    ?>
                                    <div class="donor-response response-<?php echo $response['response_status']; ?>">
                                        <div style="display: flex; justify-content: space-between; align-items: start;">
                                            <div>
                                                <strong><?php echo htmlspecialchars($donor['full_name'] ?? 'Unknown'); ?></strong>
                                                <br>
                                                <small>Phone: <?php echo htmlspecialchars($donor['phone_number'] ?? 'N/A'); ?></small>
                                                <br>
                                                <small>Email: <?php echo htmlspecialchars($donor['email'] ?? 'N/A'); ?></small>
                                            </div>
                                            <div style="text-align: right;">
                                                <span class="status <?php echo $response['response_status']; ?>">
                                                    <?php echo ucfirst($response['response_status']); ?>
                                                </span>
                                                <br>
                                                <small><?php echo date('M j, Y g:i A', strtotime($response['response_date'])); ?></small>
                                            </div>
                                        </div>
                                        
                                        <!-- Action buttons -->
                                        <?php if($response['response_status'] == 'pending' && $request['status'] == 'active'): ?>
                                            <form method="POST" style="margin-top: 10px;">
                                                <input type="hidden" name="response_id" value="<?php echo $response['response_id']; ?>">
                                                <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                                                <button type="submit" name="accept_response" class="approve" style="padding: 5px 15px;">
                                                    ✓ Accept Donor (Counts as 1 unit)
                                                </button>
                                            </form>
                                        <?php elseif($response['response_status'] == 'accepted' && $request['status'] == 'active'): ?>
                                            <div style="margin-top: 10px;">
                                                <span style="color: #28a745; font-weight: bold;">✓ Accepted (1 unit secured)</span>
                                                <form method="POST" style="display:inline; margin-left: 10px;">
                                                    <input type="hidden" name="response_id" value="<?php echo $response['response_id']; ?>">
                                                    <button type="submit" name="complete_donation" class="approve" style="padding: 3px 10px; font-size: 0.9rem;">
                                                        Mark Donated
                                                    </button>
                                                </form>
                                            </div>
                                        <?php elseif($response['response_status'] == 'completed'): ?>
                                            <p style="color: #28a745; margin-top: 5px; font-weight: bold;">
                                                ✓ Donation completed successfully
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                
                                <p style="margin-top: 15px; text-align: center;">
                                    <a href="manage_requests.php" style="color: #666; text-decoration: none;">
                                        ← Back to all requests
                                    </a>
                                </p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>