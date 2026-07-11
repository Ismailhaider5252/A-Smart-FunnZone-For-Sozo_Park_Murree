<?php

if (!isset($_SESSION['role'])) {
    header("Location: ../login.php");
    exit();
}

function allowRoles($roles)
{
    if (!in_array($_SESSION['role'], $roles)) {
        die("<h2>Access Denied</h2><p>You don't have permission to access this page.</p>");
    }
}