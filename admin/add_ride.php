<?php
include "../includes/session.php";
include "../includes/auth.php";
include "../config/db.php";

allowRoles(['Admin']);
$booths = $pdo->query("SELECT * FROM booths")->fetchAll(PDO::FETCH_ASSOC);

if(isset($_POST['save'])){
    $stmt = $pdo->prepare("
        INSERT INTO rides(admin_id, booth_id, ride_name, price, status, description)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $_SESSION['admin_id'],
        $_POST['booth_id'],
        $_POST['ride_name'],
        $_POST['price'],
        $_POST['status'],
        $_POST['description']
    ]);

    header("Location: rides.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Ride</title>
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
            <h2>Add New Ride</h2>

            <form method="POST">
                <div class="form-group">
                    <label>Ride Name</label>
                    <input type="text" name="ride_name" required>
                </div>

                <div class="form-group">
                    <label>Booth</label>
                    <select name="booth_id" required>
                        <?php foreach($booths as $b){ ?>
                            <option value="<?= $b['id'] ?>"><?= $b['booth_name'] ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Ticket Price</label>
                    <input type="number" name="price" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description"></textarea>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option>Active</option>
                        <option>Inactive</option>
                    </select>
                </div>

                <button class="btn btn-primary" name="save">Save Ride</button>
            </form>
        </div>
    </div>

</div>

</body>
</html>