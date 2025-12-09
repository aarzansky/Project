<?php
session_start();
include 'connect.php';

// Check if donor is logged in
if(!isset($_SESSION['donor_id']) || !isset($_SESSION['donor_email'])) {
    header('Location: login.php');
    exit();
}

$donor_id = $_SESSION['donor_id'];
$email = $_SESSION['donor_email'];

// Handle response
if(isset($_POST['respond'])) {
    $request_id = $_POST['request_id'];
    
    // Check if already responded
    $check_sql = "SELECT * FROM donor_responses WHERE request_id='$request_id' AND donor_id='$donor_id'";
    $check_result = mysqli_query($conn, $check_sql);
    
    if(mysqli_num_rows($check_result) == 0) {
        // Insert response
        $insert_sql = "INSERT INTO donor_responses (request_id, donor_id) VALUES ('$request_id', '$donor_id')";
        
        if(mysqli_query($conn, $insert_sql)) {
            $success = "Response submitted! The hospital will contact you soon.";
        } else {
            $error = "Error submitting response.";
        }
    } else {
        $info = "Already responded to this request.";
    }
}

// Get donor info
$donor_sql = "SELECT full_name, blood_type FROM donors WHERE donor_id='$donor_id'";
$donor_result = mysqli_query($conn, $donor_sql);
$donor = mysqli_fetch_assoc($donor_result);
$name = $donor['full_name'];
$blood_type = $donor['blood_type'];

// Get hospital ID from session or database
if(isset($_SESSION['hospital_id'])) {
    $hospital_id = $_SESSION['hospital_id'];
} else {
    // Get the only hospital in the system
    $hospital_sql = "SELECT hospital_id FROM hospitals LIMIT 1";
    $hospital_result = mysqli_query($conn, $hospital_sql);
    $hospital = mysqli_fetch_assoc($hospital_result);
    $hospital_id = $hospital['hospital_id'];
    $_SESSION['hospital_id'] = $hospital_id;
}

// Get matching blood requests from the hospital
$request_sql = "SELECT * FROM blood_requests 
               WHERE hospital_id='$hospital_id' 
               AND blood_type='$blood_type' 
               AND status='active' 
               ORDER BY created_at DESC";
$request_result = mysqli_query($conn, $request_sql);
$requests = mysqli_fetch_all($request_result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
     <nav>
        <div class="brand">BloodConnect Hospital</div>
        <div class="links">
            <a href="#" class="active">Dashboard</a>
        </div>
        <div class="uinfo">
            <span>Welcome, <?php echo htmlspecialchars($name); ?></span>
            <a href="logout.php"><button class="logout">Logout</button></a>
        </div>
    </nav>

    <div class="dashboard">
        <div class="header">
            <h1>Welcome, <?php echo htmlspecialchars($name); ?></h1>
            <p>Your Blood Type: <strong><?php echo $blood_type; ?></strong></p>
        </div>
        
        <?php if(isset($success)): ?>
            <div class="alert success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if(isset($info)): ?>
            <div class="alert" style="background: #fff3cd; color: #856404; border: 1px solid #ffeaa7;">
                <?php echo $info; ?>
            </div>
        <?php endif; ?>
        
        <h2>Blood Requests from BloodConnect Hospital</h2>
        
        <?php if(empty($requests)): ?>
            <div class="no-requests">
                <p>No matching blood requests at the moment.</p>
                <p>Check back later or contact the hospital if you need to update your information.</p>
            </div>
        <?php else: ?>
            <?php foreach($requests as $request): ?>
                <div class="card">
                    <h3><?php echo $request['blood_type']; ?> Blood Needed - <?php echo ucfirst($request['urgency']); ?> Priority</h3>
                    <p><strong>Units Needed:</strong> <?php echo $request['units']; ?></p>
                    <p><strong>Posted:</strong> <?php echo date('M j, Y g:i A', strtotime($request['created_at'])); ?></p>
                    <?php if(!empty($request['additional_notes'])): ?>
                        <p><strong>Notes:</strong> <?php echo htmlspecialchars($request['additional_notes']); ?></p>
                    <?php endif; ?>
                    
                    <?php
                    $check_response = "SELECT * FROM donor_responses WHERE request_id='{$request['request_id']}' AND donor_id='$donor_id'";
                    $response_result = mysqli_query($conn, $check_response);
                    $has_responded = mysqli_num_rows($response_result) > 0;
                    ?>
                    
                    <form method="POST">
                        <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                        
                        <?php if($has_responded): ?>
                            <button type="button" disabled class="btn" style="background-color: #6c757d;">
                                ✓ Already Responded
                            </button>
                        <?php else: ?>
                            <button type="submit" name="respond" class="btn" style="background-color: #c73030;">
                                I Can Donate
                            </button>
                        <?php endif; ?>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
</body>
</html>