<?php
include "../includes/session.php";
include "../includes/auth.php";
include "../config/db.php";

allowRoles(['Admin', 'Accountant']);

$admin_id = $_SESSION['admin_id'];

$from = isset($_GET['from']) ? $_GET['from'] : date('Y-m-d');
$to = isset($_GET['to']) ? $_GET['to'] : date('Y-m-d');

$stmt = $pdo->prepare("
    SELECT tickets.*, rides.ride_name
    FROM tickets
    JOIN rides ON tickets.ride_id = rides.id
    WHERE tickets.admin_id = ?
    AND DATE(tickets.created_at) BETWEEN ? AND ?
    ORDER BY tickets.created_at DESC
");
$stmt->execute([$admin_id, $from, $to]);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_stmt = $pdo->prepare("
    SELECT COALESCE(SUM(total_amount),0)
    FROM tickets
    WHERE admin_id = ?
    AND DATE(created_at) BETWEEN ? AND ?
");
$total_stmt->execute([$admin_id, $from, $to]);
$total_sale = $total_stmt->fetchColumn();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sales Report</title>
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
            <h2>Sales Report</h2>

            <form method="GET">
                <div class="form-group">
                    <label>From Date</label>
                    <input type="date" name="from" value="<?= $from ?>">
                </div>

                <div class="form-group">
                    <label>To Date</label>
                    <input type="date" name="to" value="<?= $to ?>">
                </div>

                <button class="btn btn-primary">Search</button>
            </form>

            <br>
            <h3>Total Sale: Rs. <?= $total_sale ?></h3>
            <button class="btn btn-primary print-btn" onclick="window.print()">Print Report</button>
            <br><br>

            <table>
                <tr>
                    <th>Ticket No</th>
                    <th>Ride</th>
                    <th>Quantity</th>
                    <th>Total Amount</th>
                    <th>Payment</th>
                    <th>Date</th>
                </tr>

                <?php foreach($tickets as $ticket){ ?>
                <tr>
                    <td><?= $ticket['id'] ?></td>
                    <td><?= $ticket['ride_name'] ?></td>
                    <td><?= $ticket['quantity'] ?></td>
                    <td>Rs. <?= $ticket['total_amount'] ?></td>
                    <td><?= $ticket['payment_method'] ?></td>
                    <td><?= $ticket['created_at'] ?></td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>

</div>

</body>
</html>
