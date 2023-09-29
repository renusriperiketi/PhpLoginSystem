<?php
session_start();
require_once 'User.php';

if (!isset($_SESSION['authUser'])) {
    header("Location: login.php");
    exit;
}

$user = new User();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    $errors = [];
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $errors[] = "All password fields are required.";
    } else {
        if ($newPassword !== $confirmPassword) {
            $errors[] = "New password and confirm password do not match.";
        } else {
            if (strlen($newPassword) < 6) {
                $errors[] = "New password must have at least 6 characters.";
            }

            if (!preg_match('/[A-Z]/', $newPassword)) {
                $errors[] = "New password must contain at least one capital letter.";
            }

            if (!preg_match('/[a-z]/', $newPassword)) {
                $errors[] = "New password must contain at least one lowercase letter.";
            }

            if (!preg_match('/\d/', $newPassword)) {
                $errors[] = "New password must contain at least one digit.";
            }

            if (!preg_match('/[@$!%*?&]/', $newPassword)) {
                $errors[] = "New password must contain at least one special character (@, $, !, %, *, ?, or &).";
            }
        }
    }

    if (!empty($errors)) {
        echo json_encode(["error" => implode(" ", $errors)]);
        exit;
    }

    $userData = $user->getUserByEmail($_SESSION['authUser']['email']);

    if (password_verify($currentPassword, $userData['password'])) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        if ($user->updateUserPassword($_SESSION['authUser']['email'], $hashedPassword)) {
            session_destroy();
            echo json_encode(["success" => "Password successfully changed. <a href='login.php'>Login Again</a>"]);            exit;
        } else {
            echo json_encode(["error" => "Failed to update the password. Please try again later."]);
        }
    } else {
        echo json_encode(["error" => "Current password is incorrect."]);
    }
    exit;
}
?>



<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <link rel="stylesheet" type="text/css" href="poc.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="change-password-container">
    <h3>Change Password</h3>
        <div id="error-message" class="error-message"></div>
        <form method="POST" id="change-password-form">
            <input type="password" name="current_password" placeholder="Current Password" ><br>
            <input type="password" name="new_password" placeholder="New Password" ><br>
            <input type="password" name="confirm_password" placeholder="Confirm Password" ><br>
            <input type="submit" value="Change Password">
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var errorMessage = document.getElementById("error-message");
            var form = document.getElementById("change-password-form");

            form.addEventListener("submit", function (event) {
                event.preventDefault();
                errorMessage.textContent = "";

                var currentPassword = form.querySelector("input[name='current_password']").value;
                var newPassword = form.querySelector("input[name='new_password']").value;
                var confirmPassword = form.querySelector("input[name='confirm_password']").value;

                if (newPassword !== confirmPassword) {
                    errorMessage.textContent = "New password and confirm password do not match.";
                    return;
                }

                $.ajax({
                    type: "POST",
                    url: "change_password.php",
                    data: {
                        current_password: currentPassword,
                        new_password: newPassword,
                        confirm_password: confirmPassword
                    },
                    dataType: "json", 
                    success: function (response) {
                        if (response.success) {
                            errorMessage.innerHTML = "<span class='success-message'>" + response.success + "</span>";
                        } else {
                            errorMessage.textContent = response.error;
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
