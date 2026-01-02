<?php
session_start();
include 'connect.php';

// Check if admin is logged in
if(!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php');
    exit();
}

$user_id = $_GET['id'] ?? 0;
$message = '';

// Get user data
$sql = "SELECT * FROM donors WHERE donor_id = '$user_id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

if(isset($_POST['updateuser'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $blood = $_POST['blood'];
    $status = $_POST['status'];
    
    $update_sql = "UPDATE donors SET 
                   full_name = '$name',
                   email = '$email',
                   phone_number = '$phone',
                   blood_type = '$blood',
                   verification_status = '$status'
                   WHERE donor_id = '$user_id'";
    
    if(mysqli_query($conn, $update_sql)) {
        $message = 'User updated successfully!';
        // Refresh user data
        $result = mysqli_query($conn, $sql);
        $user = mysqli_fetch_assoc($result);
    } else {
        $message = 'Error updating user: ' . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* ... existing styles ... */
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
        </div>
        <div class="uinfo">
            <span>Admin Panel</span>
            <a href="logout.php"><button class="logout">Logout</button></a>
        </div>
    </nav>
    
    <div class="dashboard">
        <div class="header">
            <h1>Edit Donor User</h1>
            <p>Update donor information and status</p>
        </div>
        
        <div class="editbox">
            <h2>Edit User #<?php echo $user_id; ?></h2>
            
            <?php if($message): ?>
                <div class="message"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="formrow">
                    <label>Full Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                </div>
                
                <div class="formrow">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                
                <div class="formrow">
                    <label>Phone Number</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
                </div>
                
                <div class="formrow">
                    <label>Blood Type</label>
                    <select name="blood" required>
                        <?php $blood_types = ['A+','A-','B+','B-','AB+','AB-','O+','O-']; ?>
                        <?php foreach($blood_types as $type): ?>
                            <option value="<?php echo $type; ?>" <?php if($user['blood_type'] == $type) echo 'selected'; ?>>
                                <?php echo $type; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="formrow">
                    <label>Status</label>
                    <select name="status" required>
                        <option value="pending" <?php if($user['verification_status'] == 'pending') echo 'selected'; ?>>Pending</option>
                        <option value="approved" <?php if($user['verification_status'] == 'approved') echo 'selected'; ?>>Approved</option>
                        <option value="rejected" <?php if($user['verification_status'] == 'rejected') echo 'selected'; ?>>Rejected</option>
                    </select>
                </div>
                
                <div class="buttonarea">
                    <button type="submit" name="updateuser" class="savebtn">Save Changes</button>
                    <a href="manage_users.php" class="cancelbtn">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>