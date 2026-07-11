<?php

include "../includes/session.php";
include "../includes/auth.php";
include "../config/db.php";

allowRoles(['Admin']);
$stmt = $pdo->prepare("SELECT * FROM employees WHERE admin_id = ? ORDER BY id DESC");
$stmt->execute([$_SESSION['admin_id']]);
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Employees</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/forms.css">
</head>
<body>

<div class="layout">

    <div class="sidebar">
        <h2>FunZone</h2>
        <a href="../dashboard.php">Dashboard</a>
        <a href="rides.php">Ride Management</a>
        <a href="../tickets/sale.php">Ticket Sale</a>
        <a href="employees.php">Employees</a>
        <a href="attendance.php">Attendance</a>
        <a href="maintenance.php">Maintenance</a>
        <a href="../accounts/expenses.php">Expenses</a>
        <a href="../accounts/salary.php">Salary</a>
        <a href="../reports/sales_report.php">Sales Report</a>
        <a href="../logout.php" class="logout">Logout</a>
    </div>

    <div class="main">
        <div class="table-box">
            <h2>Employee Management</h2>

            <a href="add_employee.php">
                <button class="btn btn-primary">Add Employee</button>
            </a>

            <br><br>
            <input type="text" id="searchInput" onkeyup="searchTable()" class="search-box" placeholder="Search here...">
<script src="../assets/js/script.js"></script>

            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>CNIC</th>
                    <th>Phone</th>
                    <th>Designation</th>
                    <th>Salary</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>

                <?php foreach($employees as $emp){ ?>
                <tr>
                    <td><?= $emp['id'] ?></td>
                    <td><?= $emp['name'] ?></td>
                    <td><?= $emp['cnic'] ?></td>
                    <td><?= $emp['phone'] ?></td>
                    <td><?= $emp['designation'] ?></td>
                    <td>Rs. <?= $emp['salary'] ?></td>
                    <td><?= $emp['status'] ?></td>
                </tr>
                <td>
    <a href="edit_employee.php?id=<?= $emp['id'] ?>">
        <button class="btn btn-primary">Edit</button>
    </a>

    <a href="delete_employee.php?id=<?= $emp['id'] ?>" onclick="return confirm('Are you sure?')">
        <button class="btn btn-danger">Delete</button>
    </a>
</td>
                <?php } ?>
            </table>
        </div>
    </div>

</div>

</body>
</html>