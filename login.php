<?php
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
include 'connect.php';



if(isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    if($email == 'admin@gmail.com' && $password == md5('12345678')) {
        $_SESSION['admin'] = $email;
        header("Location: hospital-dashboard.php");
        exit();
    }

    $sql = "SELECT email, password, verification_status FROM donors WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $sql);
    $num = mysqli_num_rows($result);
    
    if($num > 0) {
        $row = mysqli_fetch_assoc($result);
        $status = $row['verification_status'];

        if($status == 'pending') {
            echo "<script>
                alert('Your account is pending verification. Please wait for approval.');
                window.location.href = 'login.php';
                </script>";
            exit();
        }
        elseif($status == 'rejected') {
            echo "<script>
                alert('Your account verification was rejected. Please contact support.');
                window.location.href = 'login.php';
                </script>";
            exit();
        }
        elseif($status == 'approved') {
            $_SESSION['donor_email'] = $row['email'];
            header("Location: donor-dashboard.php");
            exit();
        }
    }
    else {
        echo "<script>
            alert('The email or password is invalid.');
            window.location.href = 'login.php';
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
                <p class="status-info">Note: Only verified donors can login. Pending/rejected accounts will be denied access.</p>
            </div>
        </div>
    </div>
</body>
</html>