<?php
session_start();
include 'connect.php';

if(!isset($_SESSION['admin'])) {
    header('Location:login.php');
    exit();
}

$email = $_SESSION['admin'];

if(isset($_POST['submit_request'])) {
    $blood_type = $_POST['blood_type'];
    $units = $_POST['units'];
    $urgency = $_POST['urgency'];
    $additional_notes = $_POST['additional_notes'];
    
    $sql = "INSERT INTO blood_requests (blood_type, units, urgency, additional_notes) 
            VALUES ('$blood_type', '$units', '$urgency', '$additional_notes')";
    
    if(mysqli_query($conn, $sql)) {
        $success_message = "Blood request posted successfully!";
    } else {
        $error_message = "Error posting request: " . mysqli_error($conn);
    }
}

$sql = "SELECT hospital_name FROM hospitals WHERE email='$email'";
$result = mysqli_query($conn, $sql);

if($result && mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    $hospital_name = $row['hospital_name'];
    
    $request_sql = "SELECT * FROM blood_requests ORDER BY created_at DESC";
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
    <title>Hospital Dashboard - Blood Donor Management</title>
        <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav>
        <div class="brand">BloodConnect Hospital</div>
        <div class="links">
            <a href="#" class="active">Dashboard</a>
            <a href="hospital-profile.php">Hospital Profile</a>
            <a href="request-history.php">Request History</a>
        </div>
        <div class="uinfo">
            <span><?php echo htmlspecialchars($hospital_name); ?></span>
            <a href="logout.php"><button class="logout">Logout</button></a>
        </div>
    </nav>

    <div class="dashboard">
        <div class="header">
            <h1>Hospital Dashboard</h1>
            <p>Manage blood requests and connect with donors</p>
        </div>

        <?php if(isset($success_message)): ?>
            <div class="alert success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if(isset($error_message)): ?>
            <div class="alert error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="card">
            <h2>Welcome, <?php echo htmlspecialchars($hospital_name); ?></h2>
            <p>Post blood requests to connect with matching donors.</p>
        </div>

        <div class="fcard">
            <h2>Create New Blood Request</h2>
            <form method="POST">
                <div class="frow">
                    <div class="fgroup">
                        <label for="blood_type">Blood Type Needed</label>
                        <select id="blood_type" name="blood_type" required>
                            <option value="">Select Blood Type</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                    </div>
                    <div class="fgroup">
                        <label for="units">Units Needed</label>
                        <input type="number" id="units" name="units" min="1" max="10" required>
                    </div>
                </div>
                <div class="frow">
                    <div class="fgroup">
                        <label for="urgency">Urgency Level</label>
                        <select id="urgency" name="urgency" required>
                            <option value="">Select Urgency</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                </div>
                <div class="fgroup">
                    <label for="additional_notes">Additional Notes (Optional)</label>
                    <textarea id="additional_notes" name="additional_notes" placeholder="Any specific requirements or instructions for donors..."></textarea>
                </div>
                <button type="submit" name="submit_request" class="btn">Post Blood Request</button>
            </form>
        </div>

        <div class="requests-table">
            <div class="table-header">
                <h2>Recent Blood Requests</h2>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Blood Type</th>
                        <th>Units</th>
                        <th>Urgency</th>
                        <th>Additional Notes</th>
                        <th>Date Posted</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($requests)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No blood requests yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($requests as $request): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($request['blood_type']); ?></td>
                                <td><?php echo htmlspecialchars($request['units']); ?></td>
                                <td>
                                    <span class="urgency <?php echo $request['urgency']; ?>">
                                        <?php echo ucfirst($request['urgency']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($request['additional_notes']); ?></td>
                                <td><?php echo date('M j, Y g:i A', strtotime($request['created_at'])); ?></td>
                                <td>
                                    <span class="status <?php echo $request['status']; ?>">
                                        <?php echo ucfirst($request['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>