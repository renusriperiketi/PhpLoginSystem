<?php
session_start();
require_once 'User.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];
    
    $user = new User();

    $existingUser = $user->getUserByEmail($email);
    if ($existingUser) {
        echo "User with this email already exists.";
        exit;
    }

    // Register the user
    $result = $user->registerUser($email, $password);

    if ($result) {
        echo "success";
        exit;
    } else {
        echo "Registration failed. Please try again."; 
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration Page</title>
    <link rel="stylesheet" type="text/css" href="poc.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $("form").submit(function(event) {
                event.preventDefault();
                var email = $("input[name='email']").val();
                var password = $("input[name='password']").val();
                var confirmPassword = $("input[name='confirm-password']").val();

                $(".error-message").text("");

                if (email === "") {
                    $(".error-message").text("Email is empty!");
                    return;
                }
                
                if (password === "") {
                    $(".error-message").text("Password is empty!");
                    return;
                }
                
                if (password !== confirmPassword) {
                    $(".error-message").text("Passwords do not match.");
                    return;
                }
                
                if (password.length < 6) {
                    $(".error-message").text("Password must have at least 6 characters.");
                    return;
                }
                
                if (!/[A-Z]/.test(password)) {
                    $(".error-message").text("Password must contain at least one capital letter.");
                    return;
                }
                
                if (!/[a-z]/.test(password)) {
                    $(".error-message").text("Password must contain at least one lowercase letter.");
                    return;
                }
                
                if (!/\d/.test(password)) {
                    $(".error-message").text("Password must contain at least one digit.");
                    return;
                }
                
                if (!/[@$!%*?&]/.test(password)) {
                    $(".error-message").text("Password must contain at least one special character (@, $, !, %, *, ?, or &).");
                    return;
                }

                $.ajax({
                    type: "POST",
                    url: "register.php",
                    data: { email: email, password: password, "confirm-password": confirmPassword },
                    success: function(response) {
                        if (response === "success") {
                            window.location.href = "home.php"; 
                        } else {
                            $(".error-message").text(response);
                        }
                    }
                });
            });
        });
    </script>
</head>
<body>
    <div class="registration-container">
        <h2>Registration</h2>
        <p class="error-message"></p>
        <form>
            <input type="text" name="name" placeholder="Name"><br>
            <input type="text" name="phone" placeholder="Phone"><br>
            <input type="text" name="blood_group" placeholder="Blood Group"><br>
            <input type="date" name="date_of_birth" placeholder="Date of Birth"><br>
            <input type="email" name="email" placeholder="Email*" ><br>
            <input type="password" name="password" placeholder="Password*" ><br>
            <input type="password" name="confirm-password" placeholder="Confirm Password*" ><br>
            
            <input type="submit" value="Register">
        </form><br>
        <a>Already have an account? </a><a href="login.php">Login here!</a>
    </div>
</body>
</html>
