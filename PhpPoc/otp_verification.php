<?php
session_start();
require_once 'User.php';

if (!isset($_SESSION['authUser']) || !isset($_SESSION['otpEmail'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = $_POST['otp'];
    // $email = $_SESSION['authUser']['email'];
    $email = $_SESSION['otpEmail'];

    $user = new User();
    
    if ($user->verifyOTP($email, $otp)) {
        unset($_SESSION['otpEmail']); 
        header("Location: home.php");
        exit;
    } else {
        $error_message = "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Verification</title>
    <link rel="stylesheet" type="text/css" href="poc.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="otp-verification-container">
        <h2>OTP Verification</h2>
        <?php if (isset($error_message)): ?>
        <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="otp" placeholder="Enter OTP">
            <input type="submit" value="Verify OTP">
        </form>
    </div>
</body>
</html>
