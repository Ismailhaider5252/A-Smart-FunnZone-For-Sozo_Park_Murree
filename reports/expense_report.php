<?php
include "../includes/session.php";
include "../includes/auth.php";
include "../config/db.php";

allowRoles(['Admin', 'Accountant']);

$admin_id = $_SESSION['admin_id'];

$from = isset($_GET['from']) ? $_GET['from'] : date('Y-m-d');
$to = isset($_GET['to']) ? $_GET['to'] : date('Y-m-d');

$stmt = $pdo->prepare("
    SELECT * FROM expenses
    WHERE admin_id = ?
    AND expense_date BETWEEN ? AND ?
    ORDER BY expense_date DESC
");
$stmt->execute([$admin_id, $from, $to]);
$expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_stmt = $pdo->prepare("
    SELECT COALESCE(SUM(amount),0)
    FROM expenses
    WHERE admin_id = ?
    AND expense_date BETWEEN ? AND ?
");
$total_stmt->execute([$admin_id, $from, $to]);
$total_expense = $total_stmt->fetchColumn();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Expense Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/forms.css">

    <style>
        @media print {
            form, .print-btn, .sidebar { display: none; }
            .main { width: 100%; padding: 0; }
        }
    </style>
</head>
<body>

<div class="layout">

    <div class="sidebar">
        <h2>FunZone</h2>
        <a href="../dashboard.php">Dashboard</a>
        <a href="../admin/rides.php">Ride Management</a>
        <a href="../tickets/sale.php">Ticket Sale</a>
        <a href="../admin/employees.php">Employees</a>
        <a href="../admin/attendance.php">Attendance</a>
        <a href="../admin/maintenance.php">Maintenance</a>
        <a href="../accounts/expenses.php">Expenses</a>
        <a href="../accounts/salary.php">Salary</a>
        <a href="sales_report.php">Sales Report</a>
        <a href="expense_report.php">Expense Report</a>
        <a href="attendance_report.php">Attendance Report</a>
        <a href="../logout.php" class="logout">Logout</a>
    </div>

    <div class="main">
        <div class="table-box">
            <h2>Expense Report</h2>

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

            <h3>Total Expense: Rs. <?= $total_expense ?></h3>

            <button class="btn btn-primary print-btn" onclick="window.print()">Print Report</button>

            <br><br>

            <table>
                <tr>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Expense</th>
                    <th>Amount</th>
                    <th>Description</th>
                </tr>

                <?php foreach($expenses as $exp){ ?>
                <tr>
                    <td><?= $exp['expense_date'] ?></td>
                    <td><?= $exp['category'] ?></td>
                    <td><?= $exp['expense_name'] ?></td>
                    <td>Rs. <?= $exp['amount'] ?></td>
                    <td><?= $exp['description'] ?></td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>

</div>

</body>
</html>