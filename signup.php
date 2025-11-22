<?php 
require_once "classes/account.php";

$userObj = new User();
$user = [];
$error = [];
if($_SERVER["REQUEST_METHOD"]=="POST"){
    $user['firstname'] = $_POST['first_name'];
    $user['lastname'] = $_POST['last_name'];
    $user['id_number'] = $_POST['id_number'];
    $user['username'] = $_POST['username'];
    $user['password'] =$_POST['password'];

    if(empty($user['firstname'])){
        $error['firstname'] = "First Name is required";
    }
    if(empty($user["lastname"])){
        $error["lastname"] = "Last Name is required";
    }
    if(empty($user["id_number"])){
        $error["id_number"] = "id_number is required";
    }
    if(empty($user["username"])){
        $error["username"] = "Username is required";
    }
    if(empty($user["password"])){
        $error["password"] = "Password is required";
    }

    if(empty($error)){
        try{
            $userObj->first_name = $user['firstname'];
            $userObj->last_name = $user['lastname'];
            $userObj->id_number = $user['id_number'];
            $userObj->user_name = $user['username'];
            $userObj->password = $user['password'];

            $userObj->registerLibrarian();
            $error['success'] = "Successfully Registered!";
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
    p{
        color: red;
    }
</style>
</head>

<body class="login-page">

<div class="logo-container">
    <img src="assets/img/Zamboanga_Peninsula_Polytechnic_State_University.png" class="top-logo">
</div>

<div class="login-container">
    <h1>ZPPSU Library</h1>
    <h2>Create Account</h2>
    <p style="color: green"><?= $error['success'] ?? ""?></p>
    <form action="" method="post">
        <input type="text" name="first_name" placeholder="First Name">
        <p><?= $error['firstname'] ?? "" ?></p>
        <input type="text" name="last_name" placeholder="Last Name">
        <p><?=  $error['lastname'] ?? "" ?></p>
        <input type="text" name="id_number" placeholder="ID number">
        <p><?=  $error['id_number'] ?? "" ?></p>
        <input type="text" name="username" placeholder="Create Username">
        <p><?=  $error['username'] ?? "" ?></p>
        <input type="password" name="password" placeholder="Create Password">
        <p><?=  $error['password'] ?? "" ?></p>

        <input type="submit" value="Create Account">
    </form>
    <p class="signup-text">
        Already have an account? <a href="login.php">Log In</a>
    </p>
</div>

</body>
</html>
