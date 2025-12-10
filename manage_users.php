<?php
session_start();
include 'connect.php';

if(!isset($_SESSION['admin'])) {
    header('Location:login.php');
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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 20px;
        }
        
        .topbar {
            background: #245E96;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .topbar h1 {
            margin: 0;
        }
        
        .goback {
            float: right;
            background: #c73030;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
        }
        
        .userstable {
            width: 100%;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .userstable th {
            background: #245E96;
            color: white;
            padding: 12px;
            text-align: left;
        }
        
        .userstable td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .userstable tr:hover {
            background: #f5f5f5;
        }
        
        .actionbox {
            text-align: center;
            width: 200px;
        }
        
        .buttonrow {
            display: flex;
            gap: 8px;
            justify-content: center;
        }
        
        .editbutton {
            background: #28a745;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            display: inline-block;
        }
        
        .deletebutton {
            background: #dc3545;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            display: inline-block;
        }
        
        .editbutton:hover {
            background: #218838;
        }
        
        .deletebutton:hover {
            background: #c82333;
        }
        
        .bloodtype {
            background: #c73030;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: bold;
            display: inline-block;
        }
        
        .waiting {
            color: #ffc107;
            font-weight: bold;
        }
        
        .active {
            color: #28a745;
            font-weight: bold;
        }
        
        .blocked {
            color: #dc3545;
            font-weight: bold;
        }
        
        .nousers {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="topbar">
        <h1>User Management</h1>
        <a href="hospital-dashboard.php" class="goback">← Dashboard</a>
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
                        $status = $user['verification_status'] ?? 'waiting';
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
</body>
</html>