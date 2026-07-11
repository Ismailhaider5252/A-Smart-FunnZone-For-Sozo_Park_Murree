<?php
include "../includes/session.php";
include "../includes/auth.php";
include "../config/db.php";

allowRoles(['Admin', 'Accountant']);

$admin_id = $_SESSION['admin_id'];
$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM expenses WHERE id = ? AND admin_id = ?");
$stmt->execute([$id, $admin_id]);

header("Location: expenses.php");
exit();
?>