<?php
include "../includes/session.php";
include "../includes/auth.php";
include "../config/db.php";

allowRoles(['Admin']);

$stmt = $pdo->prepare("
    SELECT rides.*, booths.booth_name
    FROM rides
    JOIN booths ON rides.booth_id = booths.id
    WHERE rides.admin_id = ?
    ORDER BY rides.id DESC
");
$stmt->execute([$_SESSION['admin_id']]);
$rides = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ride Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/forms.css">
    <script src="../assets/js/script.js" defer></script>
</head>
<body>

<div class="layout">

    <?php include "../includes/sidebar.php"; ?>

    <div class="main">
        <div class="table-box">
            <h2>Ride Management</h2>

            <input type="text" id="searchInput" onkeyup="searchTable()" class="search-box" placeholder="Search Ride...">

            <a href="add_ride.php">
                <button class="btn btn-primary">Add New Ride</button>
            </a>

            <br><br>

            <table>
                <tr>
                    <th>ID</th>
                    <th>Ride</th>
                    <th>Booth</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>

                <?php foreach($rides as $ride){ ?>
                <tr>
                    <td><?= $ride['id'] ?></td>
                    <td><?= $ride['ride_name'] ?></td>
                    <td><?= $ride['booth_name'] ?></td>
                    <td>Rs. <?= $ride['price'] ?></td>
                    <td><?= $ride['status'] ?></td>
                    <td>
                        <a href="edit_ride.php?id=<?= $ride['id'] ?>">
                            <button class="btn btn-primary">Edit</button>
                        </a>
                        <a href="delete_ride.php?id=<?= $ride['id'] ?>" onclick="return confirm('Are you sure?')">
                            <button class="btn btn-danger">Delete</button>
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>

</div>

</body>
</html>
