<?php
use PHPMailer\PHPMailer\PHPMailer;
session_start();
require_once 'User.php';
require_once 'database.php';
require_once 'PHPMailer-master/src/PHPMailer.php';
require_once 'PHPMailer-master/src/SMTP.php';
require_once 'PHPMailer-master/src/Exception.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    
    $user = new User();
    $userData = $user->getUserByEmail($email);

    if ($userData) {
        $token = bin2hex(random_bytes(32));
        $user->storePasswordResetToken($email, $token);

        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; 
        $mail->Port = 587; 
        $mail->SMTPAuth = true;
        $mail->Username = 'test@gmail.com'; 
        $mail->Password = 'smtp password'; 
        $mail->setFrom('test@gmail.com', 'Renusri'); 
        $mail->addAddress($email);

        $subject = "Password Reset Request";
        $message = "Please click the following link to reset your password: http://localhost/Aakash/PhpPoc/reset_password_form.php?token=$token";

        $mail->Subject = $subject;
        $mail->Body = $message;

        if ($mail->send()) {
            $response = array("status" => "success", "message" => "Password reset instructions sent! Check your email.");
        } else {
            $response = array("status" => "error", "message" => "Error sending email: " . $mail->ErrorInfo);
        }
        echo json_encode($response);
    } else {
        echo "No user found with that email address.";
    }
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" type="text/css" href="poc.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src= "forgot_password.js"></script>
</head>
<body>
    <div class="reset-password-container">
        <h2>Reset Password</h2>
        <div id="message-container"></div>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="submit" value="Send Reset Instructions">
        </form>
    </div>
</body>
</html>
