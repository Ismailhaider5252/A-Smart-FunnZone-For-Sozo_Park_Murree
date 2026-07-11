<?php
include "../includes/session.php";
include "../includes/auth.php";
include "../config/db.php";

allowRoles(['Admin', 'Accountant']);

$admin_id = $_SESSION['admin_id'];

if(isset($_POST['save'])){
    $stmt = $pdo->prepare("
        INSERT INTO expenses(admin_id, expense_name, category, amount, description, expense_date)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $admin_id,
        $_POST['expense_name'],
        $_POST['category'],
        $_POST['amount'],
        $_POST['description'],
        $_POST['expense_date']
    ]);

    $success = "Expense added successfully";
}

$stmt = $pdo->prepare("SELECT * FROM expenses WHERE admin_id = ? ORDER BY expense_date DESC");
$stmt->execute([$admin_id]);
$expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Expenses</title>
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
            <h2>Add Expense</h2>

            <?php if(isset($success)){ ?>
                <p style="color:green; font-weight:bold;"><?= $success ?></p>
            <?php } ?>

            <form method="POST">
                <div class="form-group">
                    <label>Expense Name</label>
                    <input type="text" name="expense_name" required>
                </div>

                <div class="form-group">
                    <label>Category</label>
                    <select name="category" required>
                        <option>Fuel</option>
                        <option>Electricity</option>
                        <option>Water</option>
                        <option>Ride Maintenance</option>
                        <option>Repair</option>
                        <option>Salary</option>
                        <option>Cleaning</option>
                        <option>Marketing</option>
                        <option>Miscellaneous</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Amount</label>
                    <input type="number" name="amount" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description"></textarea>
                </div>

                <div class="form-group">
                    <label>Expense Date</label>
                    <input type="date" name="expense_date" value="<?= date('Y-m-d') ?>" required>
                </div>

                <button class="btn btn-primary" name="save">Save Expense</button>
            </form>
        </div>

        <br>

        <div class="table-box">
            <h2>Expense List</h2>

            <input type="text" id="searchInput" onkeyup="searchTable()" class="search-box" placeholder="Search Expense...">

            <table>
                <tr>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Expense</th>
                    <th>Amount</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>

                <?php foreach($expenses as $exp){ ?>
                <tr>
                    <td><?= $exp['expense_date'] ?></td>
                    <td><?= $exp['category'] ?></td>
                    <td><?= $exp['expense_name'] ?></td>
                    <td>Rs. <?= $exp['amount'] ?></td>
                    <td><?= $exp['description'] ?></td>
                    <td>
                        <a href="edit_expense.php?id=<?= $exp['id'] ?>">
                            <button class="btn btn-primary">Edit</button>
                        </a>
                        <a href="delete_expense.php?id=<?= $exp['id'] ?>" onclick="return confirm('Are you sure?')">
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
