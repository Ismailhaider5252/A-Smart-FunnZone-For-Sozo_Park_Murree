<?php
include "../includes/session.php";
include "../includes/auth.php";
include "../config/db.php";

allowRoles(['Admin', 'Accountant']);

$admin_id = $_SESSION['admin_id'];
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

$sale_stmt = $pdo->prepare("SELECT COALESCE(SUM(total_amount),0) FROM tickets WHERE admin_id = ? AND DATE(created_at) = ?");
$sale_stmt->execute([$admin_id, $date]);
$total_sale = $sale_stmt->fetchColumn();

$ticket_stmt = $pdo->prepare("SELECT COALESCE(SUM(quantity),0) FROM tickets WHERE admin_id = ? AND DATE(created_at) = ?");
$ticket_stmt->execute([$admin_id, $date]);
$total_tickets = $ticket_stmt->fetchColumn();

$expense_stmt = $pdo->prepare("SELECT COALESCE(SUM(amount),0) FROM expenses WHERE admin_id = ? AND expense_date = ?");
$expense_stmt->execute([$admin_id, $date]);
$total_expense = $expense_stmt->fetchColumn();

$cash_stmt = $pdo->prepare("SELECT COALESCE(SUM(total_amount),0) FROM tickets WHERE admin_id = ? AND DATE(created_at) = ? AND payment_method = 'Cash'");
$cash_stmt->execute([$admin_id, $date]);
$cash_sale = $cash_stmt->fetchColumn();

$online_stmt = $pdo->prepare("SELECT COALESCE(SUM(total_amount),0) FROM tickets WHERE admin_id = ? AND DATE(created_at) = ? AND payment_method IN ('Online','QR Payment')");
$online_stmt->execute([$admin_id, $date]);
$online_sale = $online_stmt->fetchColumn();

$net_profit = $total_sale - $total_expense;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daily Cash Closing</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/forms.css">
    <style>
        @media print { form, .print-btn, .sidebar { display: none; } .main { width: 100%; padding: 0; } }
    </style>
</head>
<body>

<div class="layout">

    <?php include "../includes/sidebar.php"; ?>

    <div class="main">
        <div class="table-box">
            <h2>Daily Cash Closing</h2>

            <form method="GET">
                <div class="form-group">
                    <label>Select Date</label>
                    <input type="date" name="date" value="<?= $date ?>">
                </div>
                <button class="btn btn-primary">Search</button>
            </form>

            <br>
            <h3>Date: <?= $date ?></h3>

            <table>
                <tr><th>Description</th><th>Amount</th></tr>
                <tr><td>Total Ticket Sales</td><td>Rs. <?= $total_sale ?></td></tr>
                <tr><td>Total Tickets Sold</td><td><?= $total_tickets ?></td></tr>
                <tr><td>Cash Payments</td><td>Rs. <?= $cash_sale ?></td></tr>
                <tr><td>Online / QR Payments</td><td>Rs. <?= $online_sale ?></td></tr>
                <tr><td>Total Expenses</td><td>Rs. <?= $total_expense ?></td></tr>
                <tr><td><b>Net Profit</b></td><td><b>Rs. <?= $net_profit ?></b></td></tr>
            </table>

            <br>
            <button class="btn btn-primary print-btn" onclick="window.print()">Print Closing Report</button>
        </div>
    </div>

</div>

</body>
</html>
