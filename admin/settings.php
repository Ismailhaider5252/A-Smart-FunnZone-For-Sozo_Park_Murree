
<?php
include "../includes/session.php";
include "../includes/auth.php";
include "../config/db.php";

allowRoles(['Admin']);

$admin_id = $_SESSION['admin_id'];

$user_stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->execute([$_SESSION['user_id']]);
$user = $user_stmt->fetch(PDO::FETCH_ASSOC);

$check = $pdo->prepare("SELECT * FROM settings WHERE admin_id = ?");
$check->execute([$admin_id]);
$setting = $check->fetch(PDO::FETCH_ASSOC);

if(!$setting){
    $insert = $pdo->prepare("INSERT INTO settings(admin_id) VALUES (?)");
    $insert->execute([$admin_id]);

    $check->execute([$admin_id]);
    $setting = $check->fetch(PDO::FETCH_ASSOC);
}

if(isset($_POST['update_profile'])){

    $profileImage = $user['profile_image'];

    if(!empty($_FILES['profile_image']['name'])){
        $profileImage = time() . "_" . $_FILES['profile_image']['name'];
        move_uploaded_file($_FILES['profile_image']['tmp_name'], "../assets/images/" . $profileImage);
    }

    $update = $pdo->prepare("
        UPDATE users 
        SET name = ?, username = ?, profile_image = ?
        WHERE id = ?
    ");

    $update->execute([
        $_POST['name'],
        $_POST['username'],
        $profileImage,
        $_SESSION['user_id']
    ]);

    $_SESSION['name'] = $_POST['name'];
    $_SESSION['profile_image'] = $profileImage;

    $success = "Profile updated successfully";
}

if(isset($_POST['change_password'])){

    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if($old_password != $user['password']){
        $error = "Old password is incorrect";
    } elseif($new_password != $confirm_password){
        $error = "New password and confirm password do not match";
    } else {
        $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update->execute([$new_password, $_SESSION['user_id']]);

        $success = "Password changed successfully";
    }
}

if(isset($_POST['update_theme'])){

    $theme = $_POST['theme'];

    $update = $pdo->prepare("UPDATE users SET theme = ? WHERE id = ?");
    $update->execute([$theme, $_SESSION['user_id']]);

    $_SESSION['theme'] = $theme;

    $success = "Theme updated successfully";
}

if(isset($_POST['update_park'])){

    $logoName = $setting['logo'];

    if(!empty($_FILES['logo']['name'])){
        $logoName = time() . "_" . $_FILES['logo']['name'];
        move_uploaded_file($_FILES['logo']['tmp_name'], "../assets/images/" . $logoName);
    }

    $update = $pdo->prepare("
        UPDATE settings 
        SET funzone_name = ?, park_address = ?, contact_number = ?, email = ?, ticket_footer = ?, logo = ?
        WHERE admin_id = ?
    ");

    $update->execute([
        $_POST['funzone_name'],
        $_POST['park_address'],
        $_POST['contact_number'],
        $_POST['email'],
        $_POST['ticket_footer'],
        $logoName,
        $admin_id
    ]);

    $success = "Park settings updated successfully";
}

$user_stmt->execute([$_SESSION['user_id']]);
$user = $user_stmt->fetch(PDO::FETCH_ASSOC);

$check->execute([$admin_id]);
$setting = $check->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Settings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/forms.css">
    <link rel="stylesheet" href="../assets/css/settings.css">
</head>

<body class="<?= $_SESSION['theme'] ?? 'light' ?>">

<div class="layout">

    <?php include "../includes/sidebar.php"; ?>

    <div class="main">

        <div class="topbar">
            <h1>Admin Settings</h1>
            <p>Manage profile, password, appearance and park details</p>
        </div>

        <?php if(isset($success)){ ?>
            <div class="success-msg"><?= $success ?></div>
        <?php } ?>

        <?php if(isset($error)){ ?>
            <div class="error-msg"><?= $error ?></div>
        <?php } ?>

        <div class="settings-grid">

            <div class="settings-card">
                <h2>Profile Settings</h2>

                <form method="POST" enctype="multipart/form-data">

                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" value="<?= $user['name'] ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" value="<?= $user['username'] ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Profile Image</label>
                        <input type="file" name="profile_image">

                        <?php if(!empty($user['profile_image'])){ ?>
                            <br><br>
                            <img src="../assets/images/<?= $user['profile_image'] ?>" class="preview-img">
                        <?php } ?>
                    </div>

                    <button class="btn btn-primary" name="update_profile">Update Profile</button>
                </form>
            </div>

            <div class="settings-card">
                <h2>Change Password</h2>

                <form method="POST">

                    <div class="form-group">
                        <label>Old Password</label>
                        <input type="password" name="old_password" required>
                    </div>

                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" required>
                    </div>

                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" required>
                    </div>

                    <button class="btn btn-primary" name="change_password">Change Password</button>
                </form>
            </div>

            <div class="settings-card">
                <h2>Appearance</h2>

                <form method="POST">

                    <div class="theme-options">

                        <label class="theme-box">
                            <input type="radio" name="theme" value="light" <?= ($user['theme']=="light") ? "checked" : "" ?>>
                            ☀️ Light Theme
                        </label>

                        <label class="theme-box">
                            <input type="radio" name="theme" value="dark" <?= ($user['theme']=="dark") ? "checked" : "" ?>>
                            🌙 Dark Theme
                        </label>

                    </div>

                    <button class="btn btn-primary" name="update_theme">Save Theme</button>
                </form>
            </div>

            <div class="settings-card">
                <h2>Park Settings</h2>

                <form method="POST" enctype="multipart/form-data">

                    <div class="form-group">
                        <label>Park Name</label>
                        <input type="text" name="funzone_name" value="<?= $setting['funzone_name'] ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Park Address</label>
                        <textarea name="park_address"><?= $setting['park_address'] ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Contact Number</label>
                        <input type="text" name="contact_number" value="<?= $setting['contact_number'] ?>">
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= $setting['email'] ?>">
                    </div>

                    <div class="form-group">
                        <label>Ticket Footer</label>
                        <textarea name="ticket_footer"><?= $setting['ticket_footer'] ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Park Logo</label>
                        <input type="file" name="logo">

                        <?php if(!empty($setting['logo'])){ ?>
                            <br><br>
                            <img src="../assets/images/<?= $setting['logo'] ?>" class="preview-img">
                        <?php } ?>
                    </div>

                    <button class="btn btn-primary" name="update_park">Save Park Settings</button>
                </form>
            </div>

        </div>

    </div>

</div>

</body>
</html>