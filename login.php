<?php 
require_once "classes/account.php";
session_start();
$userObj = new User();

$user = [];
$error = [];

// Handle success message from signup
$success_msg = $_SESSION['register_success'] ?? null;
unset($_SESSION['register_success']); // Clear after getting it

if($_SERVER["REQUEST_METHOD"]=="POST"){

    $user["username"] = $_POST["username"];
    $user["password"] = $_POST["password"];

    if(empty($user['username'])){
        $error["username"] = 'Username required';
    }

    if(empty($user['password'])){
        $error['password'] = 'Password required';
    }

    if(empty($error)){
        $loginUser = $userObj->login($user['username'],$user['password']);

        if($loginUser){
            $_SESSION['username'] = $user['username'];
            $_SESSION['password'] = $user['password'];
            $_SESSION['is_admin'] = $loginUser['is_admin']; 

            if($loginUser['is_admin']==1){
                header("Location: pages/admin/admin_dashboard.php");
            }else{
                header("Location: pages/librarian/librarian.php");
            }
           exit; 
        }else {
            $error["login"] = "Invalid username or password";
        }

    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ZPPSULibrary - Login</title>
<link rel="stylesheet" href="assets/css/login.css">
<link rel="icon" type="image/x-icon" href="assets/icons/favicon.png">
<style>
    .reg-success-banner {
        background-color: #d4edda;
        color: #155724;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 15px;
        font-weight: 600;
        border: 1px solid #c3e6cb;
        font-size: 14px;
        text-align: center;
    }

    .login-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
</style>
</head>

<body class="login-page">

<div class="logo-container">
    <img src="assets/img/Zamboanga_Peninsula_Polytechnic_State_University.png" alt="ZPPSU Logo" class="top-logo">
</div>

<div class="login-wrapper">
    <div class="login-container">
        <h1>ZPPSU Library</h1>
        <h2>Log In</h2>

        <!-- Success Message moved INSIDE container -->
        <?php if ($success_msg): ?>
            <div class="reg-success-banner">
                <?= htmlspecialchars($success_msg) ?>
            </div>
        <?php endif; ?>

        <p style="color: red;"><?= $error['login'] ?? '' ?></p>
        <form action="" method="post">
            <input type="text" name="username" placeholder="Username">
            <p style="color: red"><?= $error["username"] ?? '' ?></p>
            
            <!-- Password Field with Toggle -->
            <div class="password-container">
                <input type="password" name="password" id="password" placeholder="Password">
                <span id="togglePassword" class="toggle-password">Show</span>
            </div>
            <p style="color: red;"><?= $error["password"] ?? '' ?></p>
            
            <input type="submit" value="Log In">
        </form>
            <p>Don’t have an account? <a href="signup.php">Sign Up</a></p>
    </div>
</div>

<!-- Load Script -->
<script src="assets/js/show_password.js"></script>

</body>
</html>