<?php
    session_start();
    $isAdmin = isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1;

    if (!isset($_SESSION['username'])) {
        header("Location: log_in.php");
        exit;
    }

    if (!$isAdmin) {
        header("Location: home.php");
        exit;
    }
?>