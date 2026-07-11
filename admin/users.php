<?php
include "../includes/session.php";
include "../includes/auth.php";
include "../config/db.php";

allowRoles(['Admin']);

$admin_id = $_SESSION['admin_id'];

$check = $pdo->prepare("SELECT * FROM settings WHERE admin_id = ?");
$check->execute([$admin_id]);
$setting = $check->fetch(PDO::FETCH_ASSOC);

if(!$setting){
    $insert = $pdo->prepare("INSERT INTO settings(admin_id) VALUES (?)");
    $insert->execute([$admin_id]);

    $check->execute([$admin_id]);
    $setting = $check->fetch(PDO::FETCH_ASSOC);
}

if(isset($_POST['save'])){
    $update = $pdo->prepare("
        UPDATE settings 
        SET funzone_name = ?, ticket_footer = ?
        WHERE admin_id = ?
    ");

    $update->execute([
        $_POST['funzone_name'],
        $_POST['ticket_footer'],
        $admin_id
    ]);

    $success = "Settings updated successfully";

    $check->execute([$admin_id]);
    $setting = $check->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Settings</title>
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
        <a href="settings.php">Settings</a>
        <a href="../logout.php" class="logout">Logout</a>
    </div>

    <div class="main">
        <div class="form-container">
            <h2>System Settings</h2>

            <?php if(isset($success)){ ?>
                <p style="color:green; font-weight:bold;"><?= $success ?></p>
            <?php } ?>

            <form method="POST">
                <div class="form-group">
                    <label>FunZone Name</label>
                    <input type="text" name="funzone_name" value="<?= $setting['funzone_name'] ?>" required>
                </div>

                <div class="form-group">
                    <label>Ticket Footer</label>
                    <textarea name="ticket_footer"><?= $setting['ticket_footer'] ?></textarea>
                </div>

                <button class="btn btn-primary" name="save">Save Settings</button>
            </form>
        </div>
    </div>

</div>

</body>
</html>