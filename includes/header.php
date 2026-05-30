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
    <link rel="stylesheet" href="/find-a-fundi/assets/css/style.css">
</head>
<body>
<header>
    <h1>Find a Fundi</h1>
    <nav class="nav-links">
        <a href="/find-a-fundi/index.php">Home</a>

        <?php if (isset($_SESSION["user_id"])): ?>

            <?php if ($_SESSION["role"] === "admin"): ?>

                <a href="/find-a-fundi/admin/dashboard.php">Dashboard</a>

            <?php elseif ($_SESSION["role"] === "fundi"): ?>

                <a href="/find-a-fundi/fundi/dashboard.php">Dashboard</a>

            <?php elseif ($_SESSION["role"] === "client"): ?>

                <a href="/find-a-fundi/client/dashboard.php">Dashboard</a>

            <?php endif; ?>

            <a href="/find-a-fundi/logout.php">Logout</a>

        <?php else: ?>

            <a href="/find-a-fundi/login.php">Login</a>
            <a href="/find-a-fundi/register.php">Register</a>

        <?php endif; ?>
    </nav>
</header>

<main>