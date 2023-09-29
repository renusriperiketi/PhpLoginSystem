<?php
session_start();
require_once 'User.php';
require_once 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['token'])) {
    $token = $_GET['token'];
    $user = new User();
    $email = $user->getEmailByResetToken($token);

    if ($email) {
        $_SESSION['reset_token'] = $token;
    } else {
        echo "Invalid or expired reset token.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['reset_token'])) {
    $token = $_SESSION['reset_token'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        echo "New password and confirm password do not match.";
        exit;
    }

    $user = new User();
    $email = $user->getEmailByResetToken($token);

    if ($email) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $user->updateUserPassword($email, $hashedPassword);

        $user->invalidateResetToken($token);

        echo "Password successfully reset. <a href='login.php'>Login Again</a>";
        exit;
    } else {
        echo "Invalid or expired reset token.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" type="text/css" href="poc.css">
</head>
<body>
    <div class="reset-password-container">
        <h2>Reset Password</h2>
        <form method="POST">
            <input type="password" name="new_password" placeholder="New Password" required><br>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required><br>
            <input type="submit" value="Reset Password">
        </form>
    </div>
</body>
</html>
