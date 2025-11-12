<?php
    include 'connect.php';
    if(isset($_POST['submit']))
    {
        $name = $_POST['full_name'];
        $email = $_POST['email'];
        $phone = $_POST['phone_number'];
        $type = $_POST['blood_type'];
        $address = $_POST['address'];
        
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

        $sql="INSERT INTO donors(full_name,email,phone_number,blood_type,address,medical_history,id_proof,password)
        VALUES('$name','$email','$phone','$type','$address','$test_folder','$id_folder','$password');";
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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
        }

        nav {
            position: sticky;
            top: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: white;
            height: 4rem;
            padding: 0 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 100;
        }

        .Links {
            display: flex;
            gap: 2rem;
        }

        .Links a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .Links a:hover {
            color: #c73030;
        }

        .Registration {
            display: flex;
            gap: 1rem;
        }

        .Registration a {
            text-decoration: none;
            border: 2px solid #c73030;
            border-radius: 50px;
            color: #c73030;
            padding: 8px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .Registration a:hover {
            background-color: #c73030;
            color: white;
        }

        .auth-container {
            max-width: 500px;
            margin: 3rem auto;
            padding: 2.5rem;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .auth-container h2 {
            color: #245E96;
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e1e1;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #245E96;
        }

        .file-upload {
            margin: 1.5rem 0;
            padding: 1.5rem;
            background-color: #f8f9fa;
            border-radius: 8px;
            border: 2px dashed #e1e1e1;
        }

        .file-upload label {
            display: block;
            margin-bottom: 1rem;
            color: #245E96;
            font-weight: 500;
        }

        .file-upload input {
            border: none;
            padding: 0;
        }

        .btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            display: block;
            text-decoration: none;
        }

        .btn-primary {
            background-color: #c73030;
            color: white;
        }

        .btn-primary:hover {
            background-color: #a52525;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(199, 48, 48, 0.3);
        }

        .auth-container p {
            text-align: center;
            margin-top: 1.5rem;
            color: #666;
        }

        .auth-container a {
            color: #245E96;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .auth-container a:hover {
            color: #c73030;
            text-decoration: underline;
        }

        footer {
            background-color: #c73030;
            color: white;
            padding: 2rem;
            text-align: center;
            margin-top: 3rem;
        }
    </style>
</head>
<body>
    <nav>
        <div class="Links">
            <a href="index.html">Home</a>
            <a href="login.html">Login</a>
            <a href="register-donor.html" style="color: #c73030;">Become Donor</a>
            <a href="register-hospital.html">Hospital Registration</a>
        </div>
        <div class="Registration">
            <a href="login.html">Login</a>
            <a href="register-donor.html">Sign Up</a>
        </div>
    </nav>

    <div class="auth-container">
        <h2>Become a Blood Donor</h2>
        <form id="donorRegisterForm" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <input type="text" placeholder="Full Name" name="full_name"required>
            </div>
            
            <div class="form-group">
                <input type="email" placeholder="Email" name="email" required>
            </div>

            <div class="form-group">
                <input type="password" placeholder="password" name="password" required>
            </div>
            
            <div class="form-group">
                <input type="tel" placeholder="Phone Number" name="phone_number" required>
            </div>
            
            <div class="form-group">
                <select required name="blood_type">
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
            
            <div class="form-group">
                <input type="text" placeholder="Address/Location" name="address" required>
            </div>
            
            <div class="file-upload">
                <label>Upload ID for Verification</label>
                <input type="file" accept=".jpg,.jpeg,.png,.pdf" name="id_proof" required>
            </div>

            <div class="file-upload">
                <label>Upload Latest Medical Test Report (In PDF Formar)</label>
                <input type="file" accept=".pdf" name="test_proof" required>
            </div>
            
            <button type="submit" class="btn btn-primary" name="submit">Register for Verification</button>
        </form>
        
        <p>Already registered? <a href="login.html">Login here</a></p>
    </div>

    <footer>
        <p>&copy; 2025 Blood Donor Management System. All rights reserved.</p>
    </footer>
</body>
</html>