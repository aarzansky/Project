<?php
session_start();
include 'connect.php';

if(!isset($_SESSION['admin'])) {
    header('Location:login.php');
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
</head>
<body>
    <h2>Admin - Pending Donor Approvals</h2>
    
    <a href="hospital-dashboard.php">← Back to Dashboard</a>
    <a href="logout.php" style="float:right;">Logout</a>
    
    <?php if(isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
    
    <?php if(empty($donors)): ?>
        <p>No pending donors to review.</p>
    <?php else: ?>
        <table border="1" cellpadding="10" cellspacing="0" width="100%">
            <tr style="background:#245E96;color:white;">
                <th>Name</th>
                <th>Blood Type</th>
                <th>Contact</th>
                <th>Address</th>
                <th>Last Donation</th>
                <th>Documents</th>
                <th>Actions</th>
            </tr>
            
            <?php foreach($donors as $donor): ?>
            <tr>
                <td><?php echo htmlspecialchars($donor['full_name']); ?></td>
                <td style="text-align:center;">
                    <span style="background:#c73030;color:white;padding:5px 10px;border-radius:10px;">
                        <?php echo htmlspecialchars($donor['blood_type']); ?>
                    </span>
                </td>
                <td>
                    <?php echo htmlspecialchars($donor['email']); ?><br>
                    <?php echo htmlspecialchars($donor['phone_number']); ?>
                </td>
                <td><?php echo htmlspecialchars($donor['address']); ?></td>
                <td><?php echo htmlspecialchars($donor['lastdonation']); ?></td>
                <td>
                    <?php if(!empty($donor['id_proof'])): ?>
                        <a href="<?php echo $donor['id_proof']; ?>" target="_blank">ID Proof</a><br>
                    <?php endif; ?>
                    <?php if(!empty($donor['medical_history'])): ?>
                        <a href="<?php echo $donor['medical_history']; ?>" target="_blank">Medical Report</a>
                    <?php endif; ?>
                </td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="donor_id" value="<?php echo $donor['donor_id']; ?>">
                        <button type="submit" name="approve" style="background:#28a745;color:white;border:none;padding:5px 10px;border-radius:3px;cursor:pointer;">
                            ✓ Approve
                        </button>
                    </form>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="donor_id" value="<?php echo $donor['donor_id']; ?>">
                        <button type="submit" name="reject" style="background:#dc3545;color:white;border:none;padding:5px 10px;border-radius:3px;cursor:pointer;">
                            ✗ Reject
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>