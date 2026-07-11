<div class="sidebar">
    <h2>FunZone</h2>
    <div style="text-align:center; margin-bottom:20px;">
    <?php if(!empty($_SESSION['profile_image'])){ ?>
        <img src="assets/images/<?= $_SESSION['profile_image'] ?>" style="width:70px;height:70px;border-radius:50%;object-fit:cover;">
    <?php } ?>
    <p style="margin-top:8px;"><?= $_SESSION['name'] ?></p>
    <small><?= $_SESSION['role'] ?></small>
</div>

    <a href="dashboard.php">Dashboard</a>

    <?php if($_SESSION['role'] == "Admin"){ ?>
        <a href="admin/rides.php">Ride Management</a>
        <a href="admin/employees.php">Employees</a>
        <a href="admin/attendance.php">Attendance</a>
        <a href="admin/maintenance.php">Maintenance</a>
        <a href="admin/settings.php">Settings</a>
    <?php } ?>

    <?php if($_SESSION['role'] == "Admin" || $_SESSION['role'] == "TicketSeller"){ ?>
        <a href="tickets/sale.php">Ticket Sale</a>
        <a href="tickets/history.php">Ticket History</a>
    <?php } ?>

    <?php if($_SESSION['role'] == "Admin" || $_SESSION['role'] == "Accountant"){ ?>
        <a href="accounts/expenses.php">Expenses</a>
        <a href="accounts/salary.php">Salary</a>
        <a href="reports/daily_cash.php">Daily Cash</a>
        <a href="reports/sales_report.php">Sales Report</a>
        <a href="reports/expense_report.php">Expense Report</a>
        <a href="reports/attendance_report.php">Attendance Report</a>
    <?php } ?>

    <a href="logout.php" class="logout">Logout</a>
</div>
