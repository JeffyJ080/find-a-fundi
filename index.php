<?php
session_start();

if (isset($_SESSION["user_id"])) {

    switch ($_SESSION["role"]) {

        case "admin":
            header("Location: admin/dashboard.php");
            exit();

        case "fundi":
            header("Location: fundi/dashboard.php");
            exit();

        case "client":
            header("Location: client/dashboard.php");
            exit();
    }
}
?>

<?php require_once "includes/header.php"; ?>

<h2>Welcome to Find a Fundi</h2>

<p>
    Find trusted local service providers quickly and easily.
</p>

<p>
    <a href="login.php">Login</a>
</p>

<p>
    <a href="register.php">Register</a>
</p>

<?php require_once "includes/footer.php"; ?>