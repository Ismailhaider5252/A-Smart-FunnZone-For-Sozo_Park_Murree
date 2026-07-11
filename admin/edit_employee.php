<?php
include "../includes/session.php";
include "../includes/auth.php";
include "../config/db.php";

allowRoles(['Admin']);

$admin_id = $_SESSION['admin_id'];
$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ? AND admin_id = ?");
$stmt->execute([$id, $admin_id]);
$emp = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$emp){
    die("Employee not found");
}

if(isset($_POST['update'])){
    $update = $pdo->prepare("
        UPDATE employees
        SET name = ?, cnic = ?, phone = ?, designation = ?, joining_date = ?, status = ?
        WHERE id = ? AND admin_id = ?
    ");

    $update->execute([
        $_POST['name'],
        $_POST['cnic'],
        $_POST['phone'],
        $_POST['designation'],
        $_POST['joining_date'],
        $_POST['status'],
        $id,
        $admin_id
    ]);

    header("Location: employees.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Employee</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/forms.css">
</head>
<body>

<div class="layout">
    <div class="sidebar">
        <h2>FunZone</h2>
        <a href="../dashboard.php">Dashboard</a>
        <a href="employees.php">Employees</a>
        <a href="../logout.php" class="logout">Logout</a>
    </div>

    <div class="main">
        <div class="form-container">
            <h2>Edit Employee</h2>

            <form method="POST">
                <div class="form-group">
                    <label>Employee Name</label>
                    <input type="text" name="name" value="<?= $emp['name'] ?>" required>
                </div>

                <div class="form-group">
                    <label>CNIC</label>
                    <input type="text" name="cnic" value="<?= $emp['cnic'] ?>">
                </div>

                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" value="<?= $emp['phone'] ?>">
                </div>

                <div class="form-group">
                    <label>Designation</label>
                    <input type="text" name="designation" value="<?= $emp['designation'] ?>" required>
                </div>

                <div class="form-group">
                    <label>Joining Date</label>
                    <input type="date" name="joining_date" value="<?= $emp['joining_date'] ?>">
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option <?= ($emp['status']=="Active") ? 'selected' : '' ?>>Active</option>
                        <option <?= ($emp['status']=="Inactive") ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>

                <button class="btn btn-primary" name="update">Update Employee</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>