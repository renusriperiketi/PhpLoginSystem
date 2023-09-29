<?php
session_start();
require_once 'User.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = new User();
    $userData = $user->getUserByEmail($email);

    if ($userData && password_verify($password, $userData['password'])) {
        if ($userData['is_approved'] == 1) {
            $otpSent = $user->sendOTP($email);

            if ($otpSent) {
                $_SESSION['authUser'] = $userData;
                $_SESSION['otpEmail'] = $email; 
                header("Location: otp_verification.php");
                exit;
            } else {
                echo "Error sending OTP. Please try again later.";
            }
        } else {
            echo "Registration request is still pending. Try again later.";
        }
    } else {
        echo "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Page</title>
    <link rel="stylesheet" type="text/css" href="poc.css">
</head>
<body>
    <div class="search-container">
        <form class="search-form">
            <input style="width:800%" type="text" name="query" placeholder="Search...">
            <input type="submit" class="search-button" value="Search">
        </form>
    </div>
    <div class="login-container">
        <h2>Login</h2>
        <div class="error-message">
            <?php
            if (isset($_GET['error'])) {
                echo "Invalid email or password.";
            }
            ?>
        </div>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="submit" value="Login">
        </form><br>
        <a>Don't have an account? </a><a href="register.php">Register here!</a><br>
        <a href="forgot_password.php">Forgot Password</a>
    </div>
</body>
</html>
