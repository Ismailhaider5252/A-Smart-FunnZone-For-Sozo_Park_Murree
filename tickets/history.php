<?php
include "../includes/session.php";
include "../includes/auth.php";
include "../config/db.php";

allowRoles(['Admin', 'TicketSeller']);

$admin_id = $_SESSION['admin_id'];

$stmt = $pdo->prepare("
    SELECT tickets.*, rides.ride_name
    FROM tickets
    JOIN rides ON rides.id = tickets.ride_id
    WHERE tickets.admin_id = ?
    ORDER BY tickets.created_at DESC
");
$stmt->execute([$admin_id]);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ticket History</title>
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
            <h2>Ticket History</h2>

            <input type="text" id="searchInput" onkeyup="searchTable()" class="search-box" placeholder="Search Ticket">

            <table>
                <tr>
                    <th>Ticket</th>
                    <th>Ride</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>

                <?php foreach($tickets as $ticket){ ?>
                <tr>
                    <td><?= $ticket['id'] ?></td>
                    <td><?= $ticket['ride_name'] ?></td>
                    <td><?= $ticket['quantity'] ?></td>
                    <td>Rs. <?= $ticket['total_amount'] ?></td>
                    <td><?= $ticket['payment_method'] ?></td>
                    <td><?= date('d-m-Y H:i', strtotime($ticket['created_at'])) ?></td>
                    <td>
                        <a href="print_ticket.php?id=<?= $ticket['id'] ?>">
                            <button class="btn btn-primary">Print</button>
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
