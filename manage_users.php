<?php
session_start();
include 'connect.php';

// Check if admin is logged in
if(!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php');
    exit();
}

$sql = "SELECT * FROM donors ORDER BY donor_id DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="styles.css">
    <style>
    </style>
</head>
<body>
    <nav>
        <div class="brand">BloodConnect Hospital</div>
        <div class="links">
            <a href="hospital-dashboard.php">Dashboard</a>
            <a href="admin-approve.php">Pending Users</a>
            <a href="donor-response.php">Responses</a>
            <a href="manage_users.php" class="active">Manage Users</a>
            <a href="manage_brequest.php">Manage Requests</a>
        </div>
        <div class="uinfo">
            <span>Admin Panel</span>
            <a href="logout.php"><button class="logout">Logout</button></a>
        </div>
    </nav>
    
    <div class="dashboard">
        <div class="header">
            <h1>Manage Donor Users</h1>
            <p>View and manage all registered donors</p>
        </div>
        
        <table class="userstable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Blood Type</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($result) == 0): ?>
                    <tr>
                        <td colspan="7" class="nousers">
                            No users found
                        </td>
                    </tr>
                <?php else: ?>
                    <?php while($user = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $user['donor_id']; ?></td>
                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['phone_number']); ?></td>
                        <td>
                            <span class="bloodtype"><?php echo htmlspecialchars($user['blood_type']); ?></span>
                        </td>
                        <td>
                            <?php 
                            $status = $user['verification_status'] ?? 'pending';
                            $status_class = $status == 'pending' ? 'waiting' : 
                                          ($status == 'approved' ? 'active' : 'blocked');
                            ?>
                            <span class="<?php echo $status_class; ?>">
                                <?php echo ucfirst($status); ?>
                            </span>
                        </td>
                        <td class="actionbox">
                            <div class="buttonrow">
                                <a href="edituser.php?id=<?php echo $user['donor_id']; ?>" class="editbutton">
                                    Edit
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>