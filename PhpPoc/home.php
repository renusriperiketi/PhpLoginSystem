<?php
session_start();
require_once 'User.php';
require_once 'database.php'; 

if (!isset($_SESSION['authUser'])) {
    header("Location: login.php");
    exit;
}

$user = new User();

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

if ($_SESSION['authUser']['is_admin'] == 1) {
    $pendingUsers = $user->getPendingUsers();

    if (isset($_GET['approve'])) {
        $email = $_GET['approve'];
        $user->approveUser($email);
        header("Location: home.php");
        exit;
    }
}
$users = $user->getListOfUsers();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home Page</title>
    <link rel="stylesheet" type="text/css" href="poc.css">
</head>
<body>
    <div class="content">
        <h2>Welcome, <?php echo $_SESSION['authUser']['email']; ?>!</h2>
        <button onclick="window.location.href='?logout=1';" class="logout-button">Logout</button> 
        <?php if (isset($_SESSION['authUser'])) { ?>
            <div class="user-profile">
                <h3>User Profile</h3>
                <p>Email: <?php echo $_SESSION['authUser']['email']; ?></p>
                <?php
                echo '<td><a style="color:white;" class="approve-button" href="change_password.php">Change Password</a></td>';
                ?>
            </div>
        <?php } ?>
        

        <?php
        if ($_SESSION['authUser']['is_admin'] == 1) {
            ?>
            <h3>List of Users</h3>    
            <table>
                <tr>
                    <th>Email</th>
                </tr>
                <?php foreach ($users as $user) { ?>
                    <tr>
                        <td><?php echo $user['email']; ?></td>
                    </tr>
                <?php } ?>
            </table>
            <h3>Pending User Approval Requests</h3>
            <table>
            <tr><th>Email</th><th>Actions</th></tr>
            <?php
            foreach ($pendingUsers as $pendingUser) {
                echo '<tr>';
                echo '<td>' . $pendingUser['email'] . '</td>';
                echo '<td><a style="color:white;" class="approve-button" href="?approve=' . $pendingUser['email'] . '">Approve</a></td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        ?>
    </div>
</body>
</html>
