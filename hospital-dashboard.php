<?php
session_start();
include 'connect.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

if(!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php');
    exit();
}

if(!isset($_SESSION['hospital_id']) || !isset($_SESSION['hospital_name'])) {
    $email = $_SESSION['admin_email'];
    $sql = "SELECT hospital_id, hospital_name FROM hospitals WHERE email='$email'";
    $result = mysqli_query($conn, $sql);
    
    if($result && mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['hospital_id'] = $row['hospital_id'];
        $_SESSION['hospital_name'] = $row['hospital_name'];
    } else {
        header('Location: logout.php');
        exit();
    }
}

$hospital_id = $_SESSION['hospital_id'];
$hospital_name = $_SESSION['hospital_name'];

if(isset($_POST['submit_request'])) {
    $blood_type = $_POST['blood_type'];
    $units = $_POST['units'];
    $urgency = $_POST['urgency'];
    $additional_notes = $_POST['additional_notes'];
    
    $sql = "INSERT INTO blood_requests (hospital_id, blood_type, units, urgency, additional_notes) 
            VALUES ('$hospital_id', '$blood_type', '$units', '$urgency', '$additional_notes')";
    
    if(mysqli_query($conn, $sql)) {
        $success_message = "Blood request posted successfully!";
        
        // ========== ADD EMAIL NOTIFICATION CODE HERE ==========
        require 'PHPMailer/src/Exception.php';
        require 'PHPMailer/src/PHPMailer.php';
        require 'PHPMailer/src/SMTP.php';
        
        // Get matching donors for this blood type
        $donor_sql = "SELECT email, full_name FROM donors WHERE blood_type = '$blood_type' AND verification_status = 'approved'";
        $donor_result = mysqli_query($conn, $donor_sql);
        
        if(mysqli_num_rows($donor_result) > 0) {
            $mail = new PHPMailer(true);
            
            try {
                // SMTP Settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'aarzanstudy@gmail.com';
                $mail->Password   = 'shhx njgh wrid xafv';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
                
                $mail->setFrom('aarzanstudy@gmail.com', 'Blood Donor Management System');
                
                while($donor = mysqli_fetch_assoc($donor_result)) {
                    $mail->addBCC($donor['email']);
                }
                
                $mail->isHTML(true);
                $mail->Subject = "Urgent: Blood Request for $blood_type";
                $mail->Body    = "
                    <h3>Blood Donation Request</h3>
                    <p><strong>Hospital:</strong> $hospital_name</p>
                    <p><strong>Blood Type Needed:</strong> $blood_type</p>
                    <p><strong>Units Required:</strong> $units</p>
                    <p><strong>Urgency Level:</strong> " . ucfirst($urgency) . "</p>
                    <p><strong>Additional Notes:</strong> " . htmlspecialchars($additional_notes) . "</p>
                    <p><strong>Date Posted:</strong> " . date('F j, Y g:i A') . "</p>
                    <br>
                    <p>If you're able to donate, please log in to your account to respond to this request.</p>
                    <p>Thank you for being a lifesaver!</p>
                ";
                
                $mail->AltBody = "Blood Request from $hospital_name - Blood Type: $blood_type, Units: $units, Urgency: $urgency";
                
                $mail->send();
                $success_message .= " Notification emails sent to matching donors.";
                
            } catch (Exception $e) {
                error_log("Email sending failed: " . $mail->ErrorInfo);
            }
        }
        
    } else {
        $error_message = "Error posting request: " . mysqli_error($conn);
    }
}

// Get requests for this hospital
$request_sql = "SELECT * FROM blood_requests WHERE hospital_id='$hospital_id' ORDER BY created_at DESC";
$request_result = mysqli_query($conn, $request_sql);
$requests = [];
if($request_result) {
    $requests = mysqli_fetch_all($request_result, MYSQLI_ASSOC);
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
            <a href="admin-approve.php">Pending Users</a>
            <a href="donor-response.php">Responses</a>
            <a href="manage_users.php">Manage Users</a>
            <a href="manage_brequest.php">Manage Requests</a>
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
                <div class="frow">
                    <label for="additional_notes">Additional Notes (Optional)</label>
                    <textarea id="additional_notes" name="additional_notes" placeholder="Any specific requirements or instructions for donors..."></textarea>
                </div>
                <button type="submit" name="submit_request" class="btn">Post Blood Request</button>
            </form>
        </div>

        <div class="brequest">
            <h2>Recent Blood Requests</h2>
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