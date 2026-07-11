<?php
include "../includes/session.php";
include "../includes/auth.php";
include "../config/db.php";

allowRoles(['Admin']);

if(isset($_POST['save'])){

    $stmt = $pdo->prepare("
        INSERT INTO employees
        (admin_id, name, cnic, phone, designation, salary, joining_date, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $_SESSION['admin_id'],
        $_POST['name'],
        $_POST['cnic'],
        $_POST['phone'],
        $_POST['designation'],
        18000,
        $_POST['joining_date'],
        $_POST['status']
    ]);

    header("Location: employees.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Employee</title>
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
        <div class="form-container">
            <h2>Add Employee</h2>

            <form method="POST">
                <div class="form-group">
                    <label>Employee Name</label>
                    <input type="text" name="name" required>
                </div>

                <div class="form-group">
                    <label>CNIC</label>
                    <input type="text" name="cnic">
                </div>

                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone">
                </div>

                <div class="form-group">
                    <label>Designation</label>
                    <input type="text" name="designation" required>
                </div>

                <div class="form-group">
                    <label>Salary</label>
                    <input type="number" value="18000" readonly>
                </div>

                <div class="form-group">
                    <label>Joining Date</label>
                    <input type="date" name="joining_date" required>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option>Active</option>
                        <option>Inactive</option>
                    </select>
                </div>

                <button class="btn btn-primary" name="save">Save Employee</button>
            </form>
        </div>
    </div>

</div>

</body>
</html>