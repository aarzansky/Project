<?php
    include 'connect.php';
    if(isset($_POST['submit']))
    {
        $name = $_POST['full_name'];
        $email = $_POST['email'];
        $phone = $_POST['phone_number'];
        $type = $_POST['blood_type'];
        $address = $_POST['address'];
        $lastdonation = $_POST['lastdonation'];

        $today = date("Y-m-d");
        if ($lastdonation > $today) 
        {
        echo "<script>alert('Last donation date cannot be in the future!'); window.history.back();</script>";
        exit(0);
        }

        
        $id_file=$_FILES['id_proof'];
        $id_filename=$id_file['name'];
        $temp_path1=$id_file['tmp_name'];
        $new_id_filename = trim($name).'_'.$id_filename;
        $id_folder = 'id_proof/'.$new_id_filename;
        move_uploaded_file($temp_path1,$id_folder);

        $test_file=$_FILES['test_proof'];
        $test_filename=$test_file['name'];
        $temp=$test_file['tmp_name'];
        $new_test_filename = trim($name). '_' .$test_filename;
        $test_folder='test/'.$new_test_filename;
        move_uploaded_file($temp,$test_folder);

        $password=md5($_POST['password']);

        $sql="INSERT INTO donors(full_name,email,phone_number,blood_type,address,lastdonation,medical_history,id_proof,password)
        VALUES('$name','$email','$phone','$type','$address','$lastdonation','$test_folder','$id_folder','$password');";
        $result=mysqli_query($conn,$sql);
        if($result)
            {
                echo "<script>
                    alert('Your application is being verified');
                </script>";
            }   
        else{
            echo"Failed to insert into database".mysqli_error($conn);
        }

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register as Donor - Blood Donor Management System</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .error-message {
            color: red;
            font-size: 0.9rem;
            margin-top: 5px;
            display: block;
        }
        input:invalid, select:invalid {
            border-color: #ff4444 !important;
        }
        input:valid, select:valid {
            border-color: #00C851 !important;
        }
    </style>
</head>
<body>
    <nav>
        <div class="Links">
            <a href="index.html">Home</a>
            <a href="login.php">Login</a>
            <a href="register-donor.html" style="color: #c73030;">Become Donor</a>
        </div>
        <div class="Registration">
            <a href="login.php">Login</a>
            <a href="register-donor.html">Sign Up</a>
        </div>
    </nav>

    <div class="container">
        <h2>Become a Blood Donor</h2>
        <form id="donorRegisterForm" method="POST" enctype="multipart/form-data">
            <div class="group">
                <input type="text" placeholder="Full Name" name="full_name" required 
                       minlength="3" maxlength="50" pattern="[A-Za-z\s]{3,}"
                       title="Enter your full name (letters and spaces only, minimum 3 characters)">
                <small class="error-message">Must be 3-50 letters and spaces only</small>
            </div>
            
            <div class="group">
                <input type="email" placeholder="Email" name="email" required 
                       maxlength="100" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                       title="Enter a valid email address (e.g., user@example.com)">
                <small class="error-message">Enter a valid email address</small>
            </div>

            <div class="group">
                <input type="password" placeholder="Password" name="password" required 
                       minlength="6" maxlength="20" pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*#?&]{6,20}$"
                       title="Password must be 6-20 characters with at least one letter and one number">
                <small class="error-message">6-20 characters with letters and numbers</small>
            </div>
            
            <div class="group">
                <input type="tel" placeholder="Phone Number" name="phone_number" required 
                       pattern="[0-9]{10}" maxlength="10" minlength="10"
                       title="Enter 10-digit phone number (e.g., 9841123456)">
                <small class="error-message">10-digit number required</small>
            </div>
            
            <div class="group">
                <select required name="blood_type" title="Select your blood type">
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
                <small class="error-message">Please select your blood type</small>
            </div>
            
            <div class="group">
                <input type="text" placeholder="Address/Location" name="address" required 
                       minlength="5" maxlength="200"
                       title="Enter your complete address">
                <small class="error-message">Address must be 5-200 characters</small>
            </div>

            <div class="group">
                <label for="lastdonation">Last Donated</label>
                <input type="date" name="lastdonation" required 
                       max="<?php echo date('Y-m-d'); ?>"
                       title="Select date of your last blood donation (cannot be future date)">
                <small class="error-message">Cannot select future date</small>
            </div>
            
            <div class="upload">
                <label>Upload ID for Verification</label>
                <input type="file" accept=".jpg,.jpeg,.png,.pdf" name="id_proof" required 
                       title="Upload government ID (JPG, PNG, or PDF)">
                <small>Accepted: JPG, PNG, PDF (Max 5MB)</small>
            </div>

            <div class="upload">
                <label>Upload Latest Medical Test Report (In PDF Format)</label>
                <input type="file" accept=".pdf" name="test_proof" required 
                       title="Upload medical test report (PDF only)">
                <small>Only PDF files (Max 5MB)</small>
            </div>
            
            <button type="submit" name="submit">Register for Verification</button>
        </form>
        
        <p>Already registered? <a href="login.php">Login here</a></p>
    </div>

    <footer>
        <p>&copy; 2025 Blood Donor Management System. All rights reserved.</p>
    </footer>
</body>
</html>