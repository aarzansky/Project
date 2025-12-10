<?php
session_start();
include 'connect.php';

if(!isset($_SESSION['admin'])) {
    header('Location:login.php');
    exit();
}

$user_id = $_GET['id'] ?? 0;
$message = '';

// Get user data
$sql = "SELECT * FROM donors WHERE donor_id = '$user_id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Update user
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
        $message = 'User updated';
        // Refresh user data
        $result = mysqli_query($conn, $sql);
        $user = mysqli_fetch_assoc($result);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 20px;
        }
        
        .editbox {
            max-width: 500px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin: 0 auto;
        }
        
        .editbox h2 {
            color: #245E96;
            margin-top: 0;
        }
        
        .formrow {
            margin-bottom: 15px;
        }
        
        .formrow label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        
        .formrow input,
        .formrow select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        .buttonarea {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
        
        .savebtn {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .cancelbtn {
            background: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
        }
        
        .message {
            padding: 10px;
            background: #d4edda;
            color: #155724;
            border-radius: 4px;
            margin-bottom: 15px;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
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
</body>
</html>