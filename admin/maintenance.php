<?php
include "../includes/session.php";
include "../includes/auth.php";
include "../config/db.php";

allowRoles(['Admin']);

$admin_id = $_SESSION['admin_id'];
allowRoles(['Admin']);
$rides_stmt = $pdo->prepare("SELECT * FROM rides WHERE admin_id = ? AND status = 'Active'");
$rides_stmt->execute([$admin_id]);
$rides = $rides_stmt->fetchAll(PDO::FETCH_ASSOC);

if(isset($_POST['save'])){
    $stmt = $pdo->prepare("
        INSERT INTO maintenance_reports(admin_id, ride_id, report_date, report_text, status)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $admin_id,
        $_POST['ride_id'],
        $_POST['report_date'],
        $_POST['report_text'],
        $_POST['status']
    ]);

    $success = "Maintenance report saved successfully";
}

$stmt = $pdo->prepare("
    SELECT maintenance_reports.*, rides.ride_name
    FROM maintenance_reports
    JOIN rides ON maintenance_reports.ride_id = rides.id
    WHERE maintenance_reports.admin_id = ?
    ORDER BY maintenance_reports.report_date DESC
");
$stmt->execute([$admin_id]);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Maintenance</title>
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
        <a href="../reports/expense_report.php">Expense Report</a>
        <a href="../reports/attendance_report.php">Attendance Report</a>
        <a href="../logout.php" class="logout">Logout</a>
    </div>

    <div class="main">

        <div class="form-container">
            <h2>Ride Maintenance</h2>

            <?php if(isset($success)){ ?>
                <p style="color:green; font-weight:bold;"><?= $success ?></p>
            <?php } ?>

            <form method="POST">
                <div class="form-group">
                    <label>Select Ride</label>
                    <select name="ride_id" required>
                        <?php foreach($rides as $ride){ ?>
                            <option value="<?= $ride['id'] ?>">
                                <?= $ride['ride_name'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Maintenance Date</label>
                    <input type="date" name="report_date" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="form-group">
                    <label>Remarks</label>
                    <textarea name="report_text" required></textarea>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option>Completed</option>
                        <option>Pending</option>
                        <option>Under Repair</option>
                    </select>
                </div>

                <button class="btn btn-primary" name="save">Save Report</button>
            </form>
        </div>

        <br>

        <div class="table-box">
            <h2>Maintenance Reports</h2>

            <table>
                <tr>
                    <th>Date</th>
                    <th>Ride</th>
                    <th>Remarks</th>
                    <th>Status</th>
                </tr>

                <?php foreach($reports as $r){ ?>
                <tr>
                    <td><?= $r['report_date'] ?></td>
                    <td><?= $r['ride_name'] ?></td>
                    <td><?= $r['report_text'] ?></td>
                    <td><?= $r['status'] ?></td>
                </tr>
                <?php } ?>
            </table>
        </div>

    </div>

</div>

</body>
</html>