<?php
include "../includes/session.php";
include "../includes/auth.php";
include "../config/db.php";

allowRoles(['Admin', 'TicketSeller']);

$admin_id = $_SESSION['admin_id'];

$stmt = $pdo->prepare("SELECT * FROM rides WHERE admin_id = ? AND status = 'Active'");
$stmt->execute([$admin_id]);
$rides = $stmt->fetchAll(PDO::FETCH_ASSOC);

if(isset($_POST['sell'])){
    $ride_id = $_POST['ride_id'];
    $quantity = $_POST['quantity'];
    $payment_method = $_POST['payment_method'];

    $ride_stmt = $pdo->prepare("SELECT * FROM rides WHERE id = ? AND admin_id = ?");
    $ride_stmt->execute([$ride_id, $admin_id]);
    $ride = $ride_stmt->fetch(PDO::FETCH_ASSOC);

    if(!$ride){
        die("Ride not found");
    }

    $total = $ride['price'] * $quantity;

    $insert = $pdo->prepare("
        INSERT INTO tickets(admin_id, ride_id, quantity, total_amount, payment_method)
        VALUES (?, ?, ?, ?, ?)
        RETURNING id
    ");

    $insert->execute([$admin_id, $ride_id, $quantity, $total, $payment_method]);
    $ticket_id = $insert->fetchColumn();

    header("Location: print_ticket.php?id=".$ticket_id);
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ticket Sale</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/forms.css">
    <script src="../assets/js/script.js" defer></script>
</head>
<body>

<div class="layout">

    <?php include "../includes/sidebar.php"; ?>

    <div class="main">
        <div class="form-container">
            <h2>Sell Ticket</h2>

            <form method="POST">
                <div class="form-group">
                    <label>Select Ride</label>
                    <select name="ride_id" required>
                        <?php foreach($rides as $ride){ ?>
                            <option value="<?= $ride['id'] ?>">
                                <?= $ride['ride_name'] ?> - Rs. <?= $ride['price'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Quantity</label>
                    <input type="number" name="quantity" value="1" min="1" required>
                </div>

                <div class="form-group">
                    <label>Payment Method</label>
                    <select name="payment_method">
                        <option>Cash</option>
                        <option>Online</option>
                        <option>QR Payment</option>
                    </select>
                </div>

                <button class="btn btn-primary" name="sell">Generate Ticket</button>
            </form>
        </div>
    </div>

</div>

</body>
</html>
