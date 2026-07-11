<?php

include "../includes/session.php";
include "../includes/auth.php";
include "../config/db.php";

allowRoles(['Admin', 'Accountant']);
$admin_id = $_SESSION['admin_id'];
$month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');

$stmt = $pdo->prepare("SELECT * FROM employees WHERE admin_id = ? AND status = 'Active'");
$stmt->execute([$admin_id]);
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Salary Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/forms.css">

    <style>
        @media print {
            form, .print-btn, .sidebar {
                display: none;
            }
            .main {
                width: 100%;
                padding: 0;
            }
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
        <a href="expenses.php">Expenses</a>
        <a href="salary.php">Salary</a>
        <a href="../reports/sales_report.php">Sales Report</a>
        <a href="../reports/expense_report.php">Expense Report</a>
        <a href="../reports/attendance_report.php">Attendance Report</a>
        <a href="../logout.php" class="logout">Logout</a>
    </div>

    <div class="main">
        <div class="table-box">
            <h2>Salary Report</h2>

            <form method="GET">
                <div class="form-group">
                    <label>Select Month</label>
                    <input type="month" name="month" value="<?= $month ?>">
                </div>

                <button class="btn btn-primary">Search</button>
            </form>

            <br>

            <button class="btn btn-primary print-btn" onclick="window.print()">Print Salary Report</button>

            <br><br>

            <table>
                <tr>
                    <th>Employee</th>
                    <th>Monthly Salary</th>
                    <th>Absent Days</th>
                    <th>Per Day Deduction</th>
                    <th>Total Deduction</th>
                    <th>Final Salary</th>
                </tr>

                <?php foreach($employees as $emp){ 
                    $absent_stmt = $pdo->prepare("
                        SELECT COUNT(*) 
                        FROM attendance
                        WHERE admin_id = ?
                        AND employee_id = ?
                        AND status = 'Absent'
                        AND TO_CHAR(attendance_date, 'YYYY-MM') = ?
                    ");
                    $absent_stmt->execute([$admin_id, $emp['id'], $month]);
                    $absent_days = $absent_stmt->fetchColumn();

                    $monthly_salary = 18000;
                    $per_day = 600;
                    $deduction = $absent_days * $per_day;
                    $final_salary = $monthly_salary - $deduction;
                ?>
                <tr>
                    <td><?= $emp['name'] ?></td>
                    <td>Rs. <?= $monthly_salary ?></td>
                    <td><?= $absent_days ?></td>
                    <td>Rs. <?= $per_day ?></td>
                    <td>Rs. <?= $deduction ?></td>
                    <td><b>Rs. <?= $final_salary ?></b></td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>

</div>

</body>
</html>