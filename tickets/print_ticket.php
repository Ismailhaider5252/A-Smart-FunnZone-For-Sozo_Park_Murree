<?php
include "../includes/session.php";
include "../includes/auth.php";
include "../config/db.php";

allowRoles(['Admin', 'TicketSeller']);

$admin_id = $_SESSION['admin_id'];
$ticket_id = $_GET['id'];

$stmt = $pdo->prepare("
    SELECT tickets.*, rides.ride_name, rides.price
    FROM tickets
    JOIN rides ON tickets.ride_id = rides.id
    WHERE tickets.id = ? AND tickets.admin_id = ?
");

$stmt->execute([$ticket_id, $admin_id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$ticket){
    die("Ticket not found");
}

$settings_stmt = $pdo->prepare("SELECT * FROM settings WHERE admin_id = ?");
$settings_stmt->execute([$admin_id]);
$settings = $settings_stmt->fetch(PDO::FETCH_ASSOC);

$funzone_name = $settings ? $settings['funzone_name'] : 'SMART FUNZONE';
$ticket_footer = $settings ? $settings['ticket_footer'] : 'Thank you for visiting!';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Print Ticket</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            padding: 20px;
        }

        .ticket {
            width: 320px;
            background: white;
            margin: auto;
            padding: 18px;
            border: 2px dashed #111827;
            text-align: center;
        }

        .ticket h2 {
            margin: 0;
            font-size: 24px;
            color: #111827;
        }

        .subtitle {
            font-size: 13px;
            margin-top: 4px;
        }

        .line {
            border-top: 1px dashed #111;
            margin: 12px 0;
        }

        .ticket-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin: 8px 0;
            text-align: left;
        }

        .total {
            font-size: 18px;
            font-weight: bold;
        }

        .footer {
            font-size: 13px;
            margin-top: 10px;
        }

        .btn {
            display: block;
            margin: 20px auto;
            padding: 12px 20px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        @media print {
            .btn {
                display: none;
            }

            body {
                background: white;
                padding: 0;
            }

            .ticket {
                margin-top: 0;
            }
        }
    </style>
</head>
<body>

<div class="ticket">
    <h2><?= $funzone_name ?></h2>
    <div class="subtitle">Amusement Park Ticket</div>

    <div class="line"></div>

    <div class="ticket-row">
        <span>Ticket No:</span>
        <b>#<?= $ticket['id'] ?></b>
    </div>

    <div class="ticket-row">
        <span>Ride:</span>
        <b><?= $ticket['ride_name'] ?></b>
    </div>

    <div class="ticket-row">
        <span>Price:</span>
        <b>Rs. <?= $ticket['price'] ?></b>
    </div>

    <div class="ticket-row">
        <span>Quantity:</span>
        <b><?= $ticket['quantity'] ?></b>
    </div>

    <div class="ticket-row total">
        <span>Total:</span>
        <span>Rs. <?= $ticket['total_amount'] ?></span>
    </div>

    <div class="ticket-row">
        <span>Payment:</span>
        <b><?= $ticket['payment_method'] ?></b>
    </div>

    <div class="ticket-row">
        <span>Date:</span>
        <b><?= date('d-m-Y h:i A', strtotime($ticket['created_at'])) ?></b>
    </div>

    <div class="line"></div>

    <div class="footer">
        <?= $ticket_footer ?><br>
        Keep this ticket with you.
    </div>
</div>

<button class="btn" onclick="window.print()">Print Ticket</button>

</body>
</html>