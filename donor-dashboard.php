<?php
session_start();
include 'connect.php';

if(!isset($_SESSION['donor_email'])) {
    header('Location:login.php');
    exit();
}

$email = $_SESSION['donor_email'];

// Handle response
if(isset($_POST['respond'])) {
    $request_id = $_POST['request_id'];
    
    // Get donor ID
    $donor_sql = "SELECT donor_id FROM donors WHERE email='$email'";
    $donor_result = mysqli_query($conn, $donor_sql);
    $donor_row = mysqli_fetch_assoc($donor_result);
    $donor_id = $donor_row['donor_id'];
    
    // Check if already responded
    $check_sql = "SELECT * FROM donor_responses 
                  WHERE request_id='$request_id' AND donor_id='$donor_id'";
    $check_result = mysqli_query($conn, $check_sql);
    
    if(mysqli_num_rows($check_result) == 0) {
        // Insert response
        $insert_sql = "INSERT INTO donor_responses (request_id, donor_id) 
                      VALUES ('$request_id', '$donor_id')";
        
        if(mysqli_query($conn, $insert_sql)) {
            $success = "Response submitted!";
        } else {
            $error = "Error submitting response.";
        }
    } else {
        $info = "Already responded to this request.";
    }
}

// Get donor info
$donor_sql = "SELECT donor_id, full_name, blood_type FROM donors WHERE email='$email'";
$donor_result = mysqli_query($conn, $donor_sql);
$donor = mysqli_fetch_assoc($donor_result);
$donor_id = $donor['donor_id'];
$name = $donor['full_name'];
$blood_type = $donor['blood_type'];

// Get matching blood requests
$request_sql = "SELECT * FROM blood_requests 
               WHERE blood_type='$blood_type' AND status='active' 
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
            <a href="logout.php"><button class="logout">Logout</button></a>
        </div>
    </nav>

    <h1>Welcome, <?php echo $name; ?></h1>
    <p>Your Blood Type: <?php echo $blood_type; ?></p>
    
    <?php if(isset($success)): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php endif; ?>
    
    <?php if(isset($info)): ?>
        <p style="color: blue;"><?php echo $info; ?></p>
    <?php endif; ?>
    
    <h2>Blood Requests</h2>
    
    <?php if(empty($requests)): ?>
        <p>No matching blood requests.</p>
    <?php else: ?>
        <?php foreach($requests as $request): ?>
            <div style="border: 1px solid #ccc; padding: 15px; margin: 10px;">
                <h3><?php echo $request['blood_type']; ?> Blood Needed</h3>
                <p>Units: <?php echo $request['units']; ?></p>
                <p>Urgency: <?php echo $request['urgency']; ?></p>
                <p>Posted: <?php echo $request['created_at']; ?></p>
                <p>Notes: <?php echo $request['additional_notes']; ?></p>
                
                <?php
                $check_response = "SELECT * FROM donor_responses 
                                  WHERE request_id='{$request['request_id']}' 
                                  AND donor_id='$donor_id'";
                $response_result = mysqli_query($conn, $check_response);
                $has_responded = mysqli_num_rows($response_result) > 0;
                ?>
                
                <form method="POST">
                    <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                    
                    <?php if($has_responded): ?>
                        <button disabled style="background-color: #ccc;">
                            ✓ Already Responded
                        </button>
                    <?php else: ?>
                        <button type="submit" name="respond" style="background-color: #c73030; color: white; padding: 10px;">
                            I Can Donate
                        </button>
                    <?php endif; ?>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
</body>
</html>