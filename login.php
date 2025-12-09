<?php
session_start();
include 'connect.php';

if(isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    // Admin login
    if($email == 'admin@gmail.com' && $password == md5('12345678')) {
        $_SESSION['admin'] = true;
        $_SESSION['admin_email'] = $email;
        
        // Get hospital info for session
        $hospital_sql = "SELECT hospital_id, hospital_name FROM hospitals WHERE email='$email'";
        $hospital_result = mysqli_query($conn, $hospital_sql);
        if($hospital_result && mysqli_num_rows($hospital_result) == 1) {
            $hospital = mysqli_fetch_assoc($hospital_result);
            $_SESSION['hospital_id'] = $hospital['hospital_id'];
            $_SESSION['hospital_name'] = $hospital['hospital_name'];
        }
        
        header("Location: hospital-dashboard.php");
        exit();
    }

    // Donor login
    $sql = "SELECT donor_id, email, password, verification_status FROM donors WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $sql);
    $num = mysqli_num_rows($result);
    
    if($num > 0) {
        $row = mysqli_fetch_assoc($result);
        
        // Check if donor is approved
        if($row['verification_status'] == 'approved') {
            $_SESSION['donor_id'] = $row['donor_id'];
            $_SESSION['donor_email'] = $row['email'];
            header("Location: donor-dashboard.php");
            exit();
        } else {
            echo "<script>
                alert('Your account is pending approval. Please wait for admin verification.');
                window.history.back();
            </script>";
            exit();
        }
    } else {
        echo "<script>
            alert('The email or password is invalid.');
            window.history.back();
        </script>";
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Blood Donor Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <nav>
            <div class="Links">
                <a href="index.html">Home</a>
                <a href="register-donor.php">Become Donor</a>
            </div>
            <div class="Registration">
                <a href="register-donor.php">Sign Up</a>
            </div>
        </nav>

        <div class="login">
            <div class="form">
                <h1>Login</h1>
                <form method="POST">
                    <div class="group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" class="submit" name="submit">Login</button>
                </form>
                <p class="registerlink">Don't have an account? <a href="register-donor.php">Register as Donor</a></p>
            </div>
        </div>
    </div>
</body>
</html>