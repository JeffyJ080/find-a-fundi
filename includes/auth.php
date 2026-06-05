<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");
    header("Expires: 0");

    function requireLogin() {
        if (!isset($_SESSION["user_id"])) {
            header("Location: /login.php");
            exit();
        }
    }

    function requireRole($role) {
        requireLogin();

        if ($_SESSION["role"] !== $role) {
            header("Location: /login.php");
            exit();
        }
    }
?>