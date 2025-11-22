<?php 
require_once "classes/account.php";
session_start();
$userObj = new User();

$user = [];
$error = [];
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
</head>

<body class="login-page">

<div class="logo-container">
    <img src="assets/img/Zamboanga_Peninsula_Polytechnic_State_University.png" alt="ZPPSU Logo" class="top-logo">
</div>

<div class="login-container">
    <h1>ZPPSU Library</h1>
    <h2>Log In</h2>
    <p style="color: red;"><?= $error['login'] ?? '' ?></p>
    <form action="" method="post">
        <input type="text" name="username" placeholder="Username">
        <p style="color: red"><?= $error["username"] ?? '' ?></p>
        <input type="password" name="password" placeholder="Password">
        <p style="color: red;"><?= $error["password"] ?? '' ?></p>
        
        <input type="submit" value="Log In">
    </form>
        <p>Don’t have an account? <a href="signup.php">Sign Up</a></p>
</div>

<script>
function login() {
    const role = document.getElementById("role").value;
    const user = document.getElementById("username").value;
    const pass = document.getElementById("password").value;

    const accounts = {
        admin: { username: "admin", password: "bayotarvin123" },
        librarian: { username: "librarian", password: "lib123" },
        borrower: { username: "borrower", password: "borrow123" }
    };

    if (user === accounts[role].username && pass === accounts[role].password) {
        if (role === "admin") window.location.href = "admin.html";
        if (role === "librarian") window.location.href = "librarian.html";
        if (role === "borrower") window.location.href = "borrower.html";
    } else {
        alert("Incorrect username or password!");
    }
}
</script>

</body>
</html>

