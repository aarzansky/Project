<?php
    include 'connect.php';
    if(isset($_POST['submit']))
    {
        $name = $_POST['hospital_name'];
        $email = $_POST['email'];
        $phone = $_POST['phone_number'];
        $type = $_POST['emergency_contact_person'];
        $address = $_POST['address'];
        $city = $_POST['city'];
        $district = $_POST['district'];
        
        $rc_file=$_FILES['registration_certificate'];
        $rc_filename=$rc_file['name'];
        $temp_path1=$rc_file['tmp_name'];
        $new_rc_filename = trim($name).'_'.$rc_filename;
        $rc_folder = 'certificate/'.$new_rc_filename;
        move_uploaded_file($temp_path1,$rc_folder);

        $license_file=$_FILES['license_proof'];
        $license_filename=$license_file['name'];
        $temp=$license_file['tmp_name'];
        $new_license_filename = trim($name). '_' .$license_filename;
        $license_folder='med_license/'.$new_license_filename;
        move_uploaded_file($temp,$license_folder);

        $password=md5($_POST['log_password']);

        $sql="INSERT INTO hospitals(hospital_name,email,phone_number,emergency_contact_person,address,city,district,registration_certificate,medical_license,log_password)
        VALUES('$name','$email','$phone','$type','$address','$city','$district','$rc_folder','$license_folder','$password');";
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
    <title>Hospital Registration - Blood Donor Management System</title>
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

        /* Registration Container */
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

        .auth-container .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 2rem;
            font-size: 1rem;
        }

        /* Form Styles */
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
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e1e1;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #245E96;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
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

        .form-row {
            display: flex;
            gap: 1rem;
        }

        .form-row .form-group {
            flex: 1;
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

        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                height: auto;
                padding: 1rem;
            }

            .Links {
                margin-bottom: 1rem;
            }

            .auth-container {
                margin: 2rem 1rem;
                padding: 2rem;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <nav>
        <div class="Links">
            <a href="index.html">Home</a>
            <a href="login.html">Login</a>
            <a href="register-donor.html">Become Donor</a>
            <a href="register-hospital.html" style="color: #c73030;">Hospital Registration</a>
        </div>
        <div class="Registration">
            <a href="login.html">Login</a>
            <a href="register-donor.html">Sign Up</a>
        </div>
    </nav>

    <div class="auth-container">
        <h2>Hospital Registration</h2>
        <p class="subtitle">Register your medical facility to connect with blood donors</p>
        
        <form id="hospitalRegisterForm" enctype="multipart/form-data" method="POST"> 
            <div class="form-group">
                <input type="text" placeholder="Hospital Name" required name="hospital_name">
            </div>
            
            <div class="form-group">
                <input type="email" placeholder="Official Email Address" required name="email">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <input type="tel" placeholder="Phone Number" required name="phone_number">
                </div>
                <div class="form-group">
                    <input type="text" placeholder="Emergency Contact Person" required name="emergency_contact_person">
                </div>
            </div>
            
            <div class="form-group">
                <textarea placeholder="Address" required name="address"></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <input type="text" placeholder="City" name="city" required>
                </div>
                <div class="form-group">
                    <input type="text" placeholder="District" name="district" required>
                </div>
            </div>
            
            <div class="file-upload">
                <label>Upload Hospital Registration Certificate</label>
                <input type="file" accept=".jpg,.jpeg,.png,.pdf" required name="registration_certificate">
                <small style="color: #666; display: block; margin-top: 0.5rem;">
                    Accepted formats: JPG, PNG, PDF (Max size: 5MB)
                </small>
            </div>
            
            <div class="file-upload">
                <label>Upload Medical License (if applicable)</label>
                <input type="file" accept=".jpg,.jpeg,.png,.pdf" name="medical_license">
                <small style="color: #666; display: block; margin-top: 0.5rem;">
                    Optional: Upload your medical facility license
                </small>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="log_password" required>
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