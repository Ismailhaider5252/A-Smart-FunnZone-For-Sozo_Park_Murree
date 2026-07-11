<?php
session_start();

if(isset($_SESSION['user_id'])){
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>SOZO Adventure Park Login</title>

<link rel="stylesheet" href="assets/css/login.css">

</head>

<body>

<div class="login-container">

    <!-- LEFT SIDE -->
    <div class="banner-section">

        
        <img src="assets/images/banner.png" alt="SOZO Adventure Park">

        <div class="overlay">

            <h1>SOZO Adventure Park</h1>

            <p>
                Murree's Ultimate Adventure Experience
            </p>

        </div>

    </div>

    <!-- RIGHT SIDE -->

    <div class="login-section">

        <div class="login-box">

            <img src="assets/images/logo.png" class="logo">

            <h2>Welcome Back</h2>

            <p class="subtitle">
                Login to Smart FunZone Management System
            </p>

            <?php
            if(isset($_GET['error'])){
                echo "<div class='error'>Invalid Username or Password</div>";
            }
            ?>

            <form action="auth/login_process.php" method="POST">

                <input
                    type="text"
                    name="username"
                    placeholder="Username"
                    required
                >

                <input
                    type="password"
                    name="password"
                    placeholder="Password"
                    required
                >

                <button type="submit">

                    Login

                </button>

            </form>

        </div>

    </div>

</div>

</body>
</html>