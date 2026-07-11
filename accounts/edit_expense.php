<?php
include "../includes/session.php";
include "../includes/auth.php";
include "../config/db.php";

allowRoles(['Admin', 'Accountant']);

$admin_id = $_SESSION['admin_id'];
$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM expenses WHERE id = ? AND admin_id = ?");
$stmt->execute([$id, $admin_id]);
$exp = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$exp){
    die("Expense not found");
}

if(isset($_POST['update'])){
    $update = $pdo->prepare("
        UPDATE expenses
        SET expense_name = ?, category = ?, amount = ?, description = ?, expense_date = ?
        WHERE id = ? AND admin_id = ?
    ");

    $update->execute([
        $_POST['expense_name'],
        $_POST['category'],
        $_POST['amount'],
        $_POST['description'],
        $_POST['expense_date'],
        $id,
        $admin_id
    ]);

    header("Location: expenses.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Expense</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/forms.css">
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
        <a href="../logout.php" class="logout">Logout</a>
    </div>

    <div class="main">
        <div class="form-container">
            <h2>Edit Expense</h2>

            <form method="POST">
                <div class="form-group">
                    <label>Expense Name</label>
                    <input type="text" name="expense_name" value="<?= $exp['expense_name'] ?>" required>
                </div>

                <div class="form-group">
                    <label>Category</label>
                    <select name="category" required>
                        <?php
                        $categories = ['Fuel','Electricity','Water','Ride Maintenance','Repair','Salary','Cleaning','Marketing','Miscellaneous'];
                        foreach($categories as $cat){
                        ?>
                            <option <?= ($exp['category'] == $cat) ? 'selected' : '' ?>><?= $cat ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Amount</label>
                    <input type="number" name="amount" value="<?= $exp['amount'] ?>" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description"><?= $exp['description'] ?></textarea>
                </div>

                <div class="form-group">
                    <label>Expense Date</label>
                    <input type="date" name="expense_date" value="<?= $exp['expense_date'] ?>" required>
                </div>

                <button class="btn btn-primary" name="update">Update Expense</button>
            </form>
        </div>
    </div>

</div>

</body>
</html>