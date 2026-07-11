<?php
session_start();
include "../config/db.php";

$username = $_POST['username'];
$password = $_POST['password'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
$stmt->execute([$username, $password]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $_SESSION['user_id'] = $user['id'];

    if($user['role'] == "Admin"){
        $_SESSION['admin_id'] = $user['id'];
    } else {
        $_SESSION['admin_id'] = 1;
    }

    $_SESSION['name'] = $user['name'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['theme'] = $user['theme'] ?? 'light';
    $_SESSION['profile_image'] = $user['profile_image'] ?? '';

    header("Location: ../dashboard.php");
    exit();
} else {
    header("Location: ../login.php?error=1");
    exit();
}
?>