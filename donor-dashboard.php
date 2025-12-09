<?php
session_start();
include 'connect.php';

if(!isset($_SESSION['donor_email'])) {
    header('Location:login.php');
    exit();
}

$email = $_SESSION['donor_email'];

$sql = "SELECT full_name, blood_type FROM donors WHERE email='$email'";
$result = mysqli_query($conn, $sql);

if($result && mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    $name = $row['full_name'];
    $blood_type = $row['blood_type'];
    
    $request_sql = "SELECT * FROM blood_requests 
                   WHERE blood_type = '$blood_type' 
                   AND status = 'active' 
                   ORDER BY 
                     CASE urgency 
                       WHEN 'urgent' THEN 1 
                       WHEN 'high' THEN 2 
                       WHEN 'medium' THEN 3 
                       WHEN 'low' THEN 4 
                     END, 
                     created_at DESC";
    $request_result = mysqli_query($conn, $request_sql);
    $requests = [];
    if($request_result) {
        $requests = mysqli_fetch_all($request_result, MYSQLI_ASSOC);
    }
} else {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Dashboard - Blood Donor Management</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav>
        <div class="brand">BloodConnect</div>
        <div class="links">
            <a href="donor-dashboard.php" class="active">Dashboard</a>
            <a href="profile.php">My Profile</a>
        </div>
        <a href="logout.php"><button class="logout">Logout</button></a>
    </nav>

    <div class="dashboard">
        <div class="mheader">
            <h1>Donor Dashboard</h1>
            <p>View blood requests and help save lives</p>
        </div>

        <div class="welcome">
            <div class="info">
                <h2>Welcome back, <?php echo htmlspecialchars($name); ?></h2>
                <p>Thank you for being a life-saver. Here are the current blood requests matching your blood type.</p>
            </div>
            <div class="badge"><?php echo htmlspecialchars($blood_type); ?></div>
        </div>

        <h2 class="stitle">Blood Requests (<?php echo count($requests); ?> found)</h2>
        
        <div class="rcontainer">
            <?php if(empty($requests)): ?>
                <div class="no-requests">
                    <p>No blood requests matching your blood type at the moment.</p>
                    <p>Check back later or contact the hospital directly.</p>
                </div>
            <?php else: ?>
                <?php foreach($requests as $request): ?>
                    <div class="rcard <?php echo $request['urgency']; ?>">
                        <div class="rheader">
                            <h3>
                                <?php 
                                $urgency_icons = [
                                    'urgent' => 'URGENT',
                                    'high' => 'High Priority', 
                                    'medium' => 'Medium Priority',
                                    'low' => 'Low Priority'
                                ];
                                echo $urgency_icons[$request['urgency']] . ': ' . $request['blood_type'] . ' Blood Needed';
                                ?>
                            </h3>
                        </div>
                        <div class="rbody">
                            <div class="rdetail">
                                <strong>Units Needed:</strong> <?php echo $request['units']; ?>
                            </div>
                            <div class="rdetail">
                                <strong>Posted:</strong> <?php echo date('M j, Y g:i A', strtotime($request['created_at'])); ?>
                            </div>
                            <?php if(!empty($request['additional_notes'])): ?>
                                <div class="rdetail">
                                    <strong>Notes:</strong> <?php echo htmlspecialchars($request['additional_notes']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="ractions">
                            <div class="contact-info">
                                <strong>Contact Hospital Directly</strong>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>