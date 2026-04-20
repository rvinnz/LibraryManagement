<?php 
require_once "classes/account.php";
session_start(); // Start session to pass success message

$userObj = new User();
$user = [];
$error = [];

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $user['firstname'] = $_POST['first_name'];
    $user['lastname'] = $_POST['last_name'];
    $user['id_number'] = $_POST['id_number'];
    $user['username'] = $_POST['username'];
    $user['password'] = $_POST['password'];
    $user['confirm_password'] = $_POST['confirm_password'] ?? '';

    // Validation
    if(empty($user['firstname'])) $error['firstname'] = "First Name is required";
    if(empty($user["lastname"])) $error["lastname"] = "Last Name is required";
    if(empty($user["id_number"])) $error["id_number"] = "ID number is required";
    if(empty($user["username"])) $error["username"] = "Username is required";
    if(empty($user["password"])) $error["password"] = "Password is required";
    
    // Password Match Check
    if(!empty($user["password"]) && $user["password"] !== $user['confirm_password']){
        $error['confirm_password'] = "Passwords do not match";
    }

    if(empty($error)){
        try{
            $userObj->first_name = $user['firstname'];
            $userObj->last_name = $user['lastname'];
            $userObj->id_number = $user['id_number'];
            $userObj->user_name = $user['username'];
            $userObj->password = $user['password'];

            if($userObj->registerLibrarian()){
                // Set session message and redirect to login
                $_SESSION['register_success'] = "Successfully Registered!";
                header("Location: login.php");
                exit; 
            } else {
                $error['message'] = "Registration failed. Please try again.";
            }
        } catch(Exception $e){
            $error['message'] = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ZPPSULibrary - Sign Up</title>
<link rel="stylesheet" href="assets/css/login.css">
<link rel="icon" type="image/x-icon" href="assets/icons/favicon.png">
<style>
    p.error { color: red; font-size: 13px; margin: 2px 0 0 5px; text-align: left; }
    p.success { color: green; text-align: center; font-weight: 600; }
</style>
</head>

<body class="login-page">

<div class="logo-container">
    <img src="assets/img/Zamboanga_Peninsula_Polytechnic_State_University.png" class="top-logo" alt="ZPPSU Logo">
</div>

<div class="login-container">
    <h1>ZPPSU Library</h1>
    <h2>Create Account</h2>
    
    <!-- General Error Message -->
    <p class="error" style="text-align:center;"><?= $error['message'] ?? ""?></p>

    <form action="" method="post">
        <input type="text" name="first_name" placeholder="First Name" value="<?= htmlspecialchars($user['firstname'] ?? '') ?>">
        <p class="error"><?= $error['firstname'] ?? "" ?></p>
        
        <input type="text" name="last_name" placeholder="Last Name" value="<?= htmlspecialchars($user['lastname'] ?? '') ?>">
        <p class="error"><?=  $error['lastname'] ?? "" ?></p>
        
        <input type="text" name="id_number" placeholder="ID Number" value="<?= htmlspecialchars($user['id_number'] ?? '') ?>">
        <p class="error"><?=  $error['id_number'] ?? "" ?></p>
        
        <input type="text" name="username" placeholder="Create Username" value="<?= htmlspecialchars($user['username'] ?? '') ?>">
        <p class="error"><?=  $error['username'] ?? "" ?></p>
        
        <!-- Create Password with Toggle -->
        <div class="password-container">
            <input type="password" name="password" id="password" placeholder="Create Password" value="<?= htmlspecialchars($user['password'] ?? '') ?>">
            <span id="toggleSignupPassword" class="toggle-password">Show</span>
        </div>
        <p class="error"><?=  $error['password'] ?? "" ?></p>

        <!-- Confirm Password with Toggle -->
        <div class="password-container">
            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password">
            <span id="toggleConfirmPassword" class="toggle-password">Show</span>
        </div>
        <p class="error"><?=  $error['confirm_password'] ?? "" ?></p>

        <input type="submit" value="Create Account">
    </form>
    <p class="signup-text">
        Already have an account? <a href="login.php">Log In</a>
    </p>
</div>

<!-- Load Script -->
<script src="assets/js/show_password.js"></script>

</body>
</html>