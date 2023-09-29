<?php
use PHPMailer\PHPMailer\PHPMailer;
require_once 'database.php';

class User {
    private $conn;

    public function __construct() {
        $this->conn = new mysqli(servername, username, password, db);
    }

    public function authenticate($username, $password) {
        $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
        $result = $this->conn->query($sql);
        return $result->fetch_assoc();
    }

    public function getUsersSortedByDOB() {
        $sql = "SELECT * FROM users";
        $result = $this->conn->query($sql);
    
        $users = array();
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    
        usort($users, function($a, $b) {
            return strcmp($a['dateofbirth'], $b['dateofbirth']);
        });
    
        return $users;
    }

    public function getListOfUsers() {
        $sql = "SELECT * FROM users";
        $result = $this->conn->query($sql);
    
        $users = array();
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }    
        return $users;
    }

    public function registerUser($email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (email, password, is_approved, is_admin) VALUES ('$email', '$hashedPassword', 0, 0)";
        return $this->conn->query($sql);
    }

    public function getUserByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = $this->conn->query($sql);
        return $result->fetch_assoc();
    }

    public function updateUserPassword($email, $newPassword) {
        $userData = $this->getUserByEmail($email);
        if (password_verify($newPassword, $userData['password'])) {
            return false;
        }

        $sql = "UPDATE users SET password = '$newPassword' WHERE email = '$email'";
        return $this->conn->query($sql);
    }

    public function approveUser($email) {
        $sql = "UPDATE users SET is_approved = 1 WHERE email = '$email'";
        return $this->conn->query($sql);
    }

    public function getPendingUsers() {
        $sql = "SELECT * FROM users WHERE is_approved = 0";
        $result = $this->conn->query($sql);
    
        $users = array();
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    
        return $users;
    }

    public function storePasswordResetToken($email, $token) {
        $sql = "UPDATE users SET reset_token = '$token', reset_token_expiry = NOW() + INTERVAL 1 HOUR WHERE email = '$email'";
        return $this->conn->query($sql);
    }
    
    public function getEmailByResetToken($token) {
        $sql = "SELECT email FROM users WHERE reset_token = '$token' AND reset_token_expiry > NOW()";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return $row ? $row['email'] : null;
    }
    
    public function invalidateResetToken($token) {
        $sql = "UPDATE users SET reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = '$token'";
        return $this->conn->query($sql);
    }

    public function sendOTP($email) {
        $otp = mt_rand(100000, 999999);

        $sql = "UPDATE users SET otp = '$otp' WHERE email = '$email'";
        $this->conn->query($sql);
       
        require_once 'PHPMailer-master/src/PHPMailer.php';
        require_once 'PHPMailer-master/src/SMTP.php';
        require_once 'PHPMailer-master/src/Exception.php';
        $mail = new PHPMailer;
        
        $mail->isSMTP(); 
        $mail->Host = 'smtp.gmail.com'; 
        $mail->Port = 587; 
        $mail->SMTPAuth = true; 
        $mail->Username = 'test@gmail.com'; 
        $mail->Password = 'smtp password'; 
        $mail->SMTPSecure = 'tls'; 

        $mail->setFrom('test@gmail.com', 'test'); 
        $mail->addAddress($email);
        $mail->Subject = 'Your OTP for Two-Factor Authentication'; 
        $mail->Body = "Your OTP is: $otp"; 

        if ($mail->send()) {
            return true; 
        } else {
            return false; 
        }
    }

    public function verifyOTP($email, $otp) {
        $sql = "SELECT otp FROM users WHERE email = '$email'";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();

        if ($row && $row['otp'] == $otp) {
            return true; // OTP is valid
        } else {
            return false; // Invalid OTP
        }
    }

}
?>
