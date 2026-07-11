<?php
include "includes/session.php";
include "config/db.php";

$admin_id = $_SESSION['admin_id'];
$today = date('Y-m-d');

$total_rides = $pdo->prepare("SELECT COUNT(*) FROM rides WHERE admin_id = ?");
$total_rides->execute([$admin_id]);
$total_rides = $total_rides->fetchColumn();

$today_tickets = $pdo->prepare("SELECT COALESCE(SUM(quantity),0) FROM tickets WHERE admin_id = ? AND DATE(created_at) = ?");
$today_tickets->execute([$admin_id, $today]);
$today_tickets = $today_tickets->fetchColumn();

$today_sale = $pdo->prepare("SELECT COALESCE(SUM(total_amount),0) FROM tickets WHERE admin_id = ? AND DATE(created_at) = ?");
$today_sale->execute([$admin_id, $today]);
$today_sale = $today_sale->fetchColumn();

$total_employees = $pdo->prepare("SELECT COUNT(*) FROM employees WHERE admin_id = ?");
$total_employees->execute([$admin_id]);
$total_employees = $total_employees->fetchColumn();

$today_expense = $pdo->prepare("SELECT COALESCE(SUM(amount),0) FROM expenses WHERE admin_id = ? AND expense_date = ?");
$today_expense->execute([$admin_id, $today]);
$today_expense = $today_expense->fetchColumn();

$today_profit = $today_sale - $today_expense;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Smart FunZone Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/forms.css">
    <link rel="stylesheet" href="assets/css/settings.css">
    <script src="assets/js/script.js" defer></script>
</head>
<body class="<?= $_SESSION['theme'] ?? 'light' ?>">

<div class="layout">

    <?php include "includes/sidebar_root.php"; ?>

    <div class="main">

        <div class="topbar">
            <h1>Dashboard</h1>
            <p>Welcome, <?= $_SESSION['name']; ?> | <?= $_SESSION['role']; ?></p>
        </div>

        <div class="cards">

            <?php if($_SESSION['role'] == "Admin"){ ?>
                <div class="card">
                    <h3>Total Rides</h3>
                    <p><?= $total_rides ?></p>
                </div>

                <div class="card">
                    <h3>Employees</h3>
                    <p><?= $total_employees ?></p>
                </div>
            <?php } ?>

            <?php if($_SESSION['role'] == "Admin" || $_SESSION['role'] == "TicketSeller"){ ?>
                <div class="card">
                    <h3>Today Tickets</h3>
                    <p><?= $today_tickets ?></p>
                </div>

                <div class="card">
                    <h3>Today Sale</h3>
                    <p>Rs. <?= $today_sale ?></p>
                </div>
            <?php } ?>

            <?php if($_SESSION['role'] == "Admin" || $_SESSION['role'] == "Accountant"){ ?>
                <div class="card">
                    <h3>Today Expenses</h3>
                    <p>Rs. <?= $today_expense ?></p>
                </div>

                <div class="card">
                    <h3>Today Profit</h3>
                    <p>Rs. <?= $today_profit ?></p>
                </div>
            <?php } ?>

        </div>

    </div>

</div>

</body>
</html>
