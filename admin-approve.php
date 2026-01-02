<?php
session_start();
include 'connect.php';

// Check if admin is logged in
if(!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php');
    exit();
}

if(isset($_POST['approve'])) {
    $donor_id = $_POST['donor_id'];
    $sql = "UPDATE donors SET verification_status = 'approved' WHERE donor_id = '$donor_id'";
    if(mysqli_query($conn, $sql)) {
        $success = "Donor approved successfully!";
    }
}

if(isset($_POST['reject'])) {
    $donor_id = $_POST['donor_id'];
    $sql = "UPDATE donors SET verification_status = 'rejected' WHERE donor_id = '$donor_id'";
    if(mysqli_query($conn, $sql)) {
        $success = "Donor rejected!";
    }
}

$sql = "SELECT * FROM donors WHERE verification_status = 'pending' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
$donors = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Approval</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
     <nav>
        <div class="brand">BloodConnect Hospital</div>
        <div class="links">
            <a href="hospital-dashboard.php">Dashboard</a>
            <a href="admin-approve.php" class="active">Pending Users</a>
            <a href="donor-response.php">Responses</a>
            <a href="manage_users.php">Manage Users</a>
            <a href="manage_brequest.php">Manage Requests</a>
        </div>
        <div class="uinfo">
            <span>Admin Panel</span>
            <a href="logout.php"><button class="logout">Logout</button></a>
        </div>
    </nav>    
    
    <div class="dashboard">
        <div class="header">
            <h1>Pending Donor Approvals</h1>
            <p>Review and approve new donor registrations</p>
        </div>
        
        <?php if(isset($success)): ?>
            <div class="alert success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if(empty($donors)): ?>
            <div class="no-requests">
                <p>No pending donors to review.</p>
            </div>
        <?php else: ?>
            <table class="container">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Blood Type</th>
                        <th>Contact</th>
                        <th>Address</th>
                        <th>Last Donation</th>
                        <th>Documents</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($donors as $donor): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($donor['full_name']); ?></td>
                        <td style="text-align:center;">
                            <span class="bloodtype"><?php echo htmlspecialchars($donor['blood_type']); ?></span>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($donor['email']); ?><br>
                            <?php echo htmlspecialchars($donor['phone_number']); ?>
                        </td>
                        <td><?php echo htmlspecialchars($donor['address']); ?></td>
                        <td><?php echo htmlspecialchars($donor['lastdonation']); ?></td>
                        <td>
                            <?php if(!empty($donor['id_proof'])): ?>
                                <a href="<?php echo $donor['id_proof']; ?>" target="_blank">View ID Proof</a><br>
                            <?php endif; ?>
                            <?php if(!empty($donor['medical_history'])): ?>
                                <a href="<?php echo $donor['medical_history']; ?>" target="_blank">View Medical Report</a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="donor_id" value="<?php echo $donor['donor_id']; ?>">
                                <button type="submit" name="approve" class="approve">
                                    ✓ Approve
                                </button>
                            </form>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="donor_id" value="<?php echo $donor['donor_id']; ?>">
                                <button type="submit" name="reject" class="reject">
                                    ✗ Reject
                                </button>
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