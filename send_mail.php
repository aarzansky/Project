<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
include 'connect.php';
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    // SMTP Settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'aarzanstudy@gmail.com';
    $mail->Password   = 'jixx mmjo kfrz nrqy';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Email Settings
    $mail->setFrom('aarzanstudy@gmail.com', 'Blood Donor Management System');
    $mail->$mail->addAddress($user['email']);

    $mail->isHTML(true);
    $mail->Subject = 'PHPMailer Test';
    $mail->Body    = '<h3>Email sent successfully using PHPMailer!</h3>';
    $mail->AltBody = 'Email sent successfully using PHPMailer!';

    $mail->send();
    echo 'Mail sent successfully';
} catch (Exception $e) {
    echo "Mail failed: {$mail->ErrorInfo}";
}