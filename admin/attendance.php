<?php
include "../includes/session.php";
include "../includes/auth.php";
include "../config/db.php";

allowRoles(['Admin']);

$admin_id = $_SESSION['admin_id'];

$employees_stmt = $pdo->prepare("SELECT * FROM employees WHERE admin_id = ? AND status = 'Active'");
$employees_stmt->execute([$admin_id]);
$employees = $employees_stmt->fetchAll(PDO::FETCH_ASSOC);

if(isset($_POST['save'])){

    $attendance_date = $_POST['attendance_date'];

    foreach($_POST['status'] as $employee_id => $status){

        $check = $pdo->prepare("
            SELECT id FROM attendance 
            WHERE admin_id = ? AND employee_id = ? AND attendance_date = ?
        ");
        $check->execute([$admin_id, $employee_id, $attendance_date]);

        if($check->rowCount() > 0){
            $update = $pdo->prepare("
                UPDATE attendance SET status = ?
                WHERE admin_id = ? AND employee_id = ? AND attendance_date = ?
            ");
            $update->execute([$status, $admin_id, $employee_id, $attendance_date]);
        } else {
            $insert = $pdo->prepare("
                INSERT INTO attendance(admin_id, employee_id, attendance_date, status)
                VALUES (?, ?, ?, ?)
            ");
            $insert->execute([$admin_id, $employee_id, $attendance_date, $status]);
        }
    }

    $success = "Attendance saved successfully";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Attendance</title>
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
        <a href="../reports/attendance_report.php">Attendance Report</a>
        <a href="../logout.php" class="logout">Logout</a>
    </div>

    <div class="main">
        <div class="table-box">
            <h2>Mark Attendance</h2>

            <?php if(isset($success)){ ?>
                <p style="color:green; font-weight:bold;"><?= $success ?></p>
            <?php } ?>

            <form method="POST">
                <div class="form-group">
                    <label>Attendance Date</label>
                    <input type="date" name="attendance_date" value="<?= date('Y-m-d') ?>" required>
                </div>

                <table>
                    <tr>
                        <th>Employee</th>
                        <th>Designation</th>
                        <th>Status</th>
                    </tr>

                    <?php foreach($employees as $emp){ ?>
                    <tr>
                        <td><?= $emp['name'] ?></td>
                        <td><?= $emp['designation'] ?></td>
                        <td>
                            <select name="status[<?= $emp['id'] ?>]" required>
                                <option value="Present">Present</option>
                                <option value="Absent">Absent</option>
                                <option value="Leave">Leave</option>
                            </select>
                        </td>
                    </tr>
                    <?php } ?>
                </table>

                <br>

                <button class="btn btn-primary" name="save">Save Attendance</button>
            </form>
        </div>
    </div>

</div>

</body>
</html>