<?php
include "../includes/session.php";
include "../includes/auth.php";
include "../config/db.php";

allowRoles(['Admin', 'Accountant']);

$admin_id = $_SESSION['admin_id'];

$from = isset($_GET['from']) ? $_GET['from'] : date('Y-m-d');
$to = isset($_GET['to']) ? $_GET['to'] : date('Y-m-d');

$stmt = $pdo->prepare("
    SELECT attendance.*, employees.name, employees.designation
    FROM attendance
    JOIN employees ON attendance.employee_id = employees.id
    WHERE attendance.admin_id = ?
    AND attendance.attendance_date BETWEEN ? AND ?
    ORDER BY attendance.attendance_date DESC
");

$stmt->execute([$admin_id, $from, $to]);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

$present = 0;
$absent = 0;
$leave = 0;

foreach($records as $row){
    if($row['status']=="Present") $present++;
    if($row['status']=="Absent") $absent++;
    if($row['status']=="Leave") $leave++;
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Attendance Report</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="../assets/css/dashboard.css">
<link rel="stylesheet" href="../assets/css/forms.css">

<style>
@media print{

form,
.print-btn,
.sidebar{
display:none;
}

.main{
width:100%;
padding:0;
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
<a href="../accounts/expenses.php">Expenses</a>
<a href="../accounts/salary.php">Salary</a>
<a href="sales_report.php">Sales Report</a>
<a href="expense_report.php">Expense Report</a>
<a href="attendance_report.php">Attendance Report</a>
<a href="../logout.php" class="logout">Logout</a>

</div>

<div class="main">

<div class="table-box">

<h2>Attendance Report</h2>

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

<h3>
Present : <?= $present ?> |
Absent : <?= $absent ?> |
Leave : <?= $leave ?>
</h3>

<button class="btn btn-primary print-btn" onclick="window.print()">
Print Report
</button>

<br><br>

<table>

<tr>

<th>Date</th>
<th>Employee</th>
<th>Designation</th>
<th>Status</th>

</tr>

<?php foreach($records as $r){ ?>

<tr>

<td><?= $r['attendance_date'] ?></td>
<td><?= $r['name'] ?></td>
<td><?= $r['designation'] ?></td>
<td><?= $r['status'] ?></td>

</tr>

<?php } ?>

</table>

</div>

</div>

</div>

</body>
</html>