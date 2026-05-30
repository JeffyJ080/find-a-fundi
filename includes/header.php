<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find a Fundi</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header>
    <h1>Find a Fundi</h1>
    <nav class="nav-links">
        <a href="/index.php">Home</a>

        <?php if (isset($_SESSION["user_id"])): ?>

            <?php if ($_SESSION["role"] === "admin"): ?>

                <a href="/admin/dashboard.php">Dashboard</a>

            <?php elseif ($_SESSION["role"] === "fundi"): ?>

                <a href="/fundi/dashboard.php">Dashboard</a>

            <?php elseif ($_SESSION["role"] === "client"): ?>

                <a href="/client/dashboard.php">Dashboard</a>

            <?php endif; ?>

            <a href="/logout.php">Logout</a>

        <?php else: ?>

            <a href="/login.php">Login</a>
            <a href="/register.php">Register</a>

        <?php endif; ?>
    </nav>
</header>

<main>