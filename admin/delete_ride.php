<?php
include "../includes/session.php";
include "../includes/auth.php";
include "../config/db.php";

allowRoles(['Admin']);

$admin_id = $_SESSION['admin_id'];
$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM rides WHERE id = ? AND admin_id = ?");
$stmt->execute([$id, $admin_id]);

header("Location: rides.php");
exit();
?>