<?php
include "../includes/session.php";
include "../includes/auth.php";
include "../config/db.php";

allowRoles(['Admin']);

$admin_id = $_SESSION['admin_id'];
$id = $_GET['id'];

$booths = $pdo->query("SELECT * FROM booths")->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM rides WHERE id = ? AND admin_id = ?");
$stmt->execute([$id, $admin_id]);
$ride = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$ride){
    die("Ride not found");
}

if(isset($_POST['update'])){
    $update = $pdo->prepare("
        UPDATE rides
        SET booth_id = ?, ride_name = ?, price = ?, status = ?, description = ?
        WHERE id = ? AND admin_id = ?
    ");

    $update->execute([
        $_POST['booth_id'],
        $_POST['ride_name'],
        $_POST['price'],
        $_POST['status'],
        $_POST['description'],
        $id,
        $admin_id
    ]);

    header("Location: rides.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Ride</title>
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
        <a href="../logout.php" class="logout">Logout</a>
    </div>

    <div class="main">
        <div class="form-container">
            <h2>Edit Ride</h2>

            <form method="POST">
                <div class="form-group">
                    <label>Ride Name</label>
                    <input type="text" name="ride_name" value="<?= $ride['ride_name'] ?>" required>
                </div>

                <div class="form-group">
                    <label>Booth</label>
                    <select name="booth_id" required>
                        <?php foreach($booths as $b){ ?>
                            <option value="<?= $b['id'] ?>" <?= ($ride['booth_id'] == $b['id']) ? 'selected' : '' ?>>
                                <?= $b['booth_name'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Ticket Price</label>
                    <input type="number" name="price" value="<?= $ride['price'] ?>" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description"><?= $ride['description'] ?></textarea>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option <?= ($ride['status']=="Active") ? 'selected' : '' ?>>Active</option>
                        <option <?= ($ride['status']=="Inactive") ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>

                <button class="btn btn-primary" name="update">Update Ride</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>