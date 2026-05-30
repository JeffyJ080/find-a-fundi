<?php
    require_once "../includes/header.php";
    require_once "../includes/auth.php";
    requireRole("client");
?>

<h1>Client Dashboard</h1>
<p>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>.</p>

<p><a href="services.php">Browse Services</a></p>
<p><a href="bookings.php">My Bookings</a></p>

<p><a href="../logout.php">Logout</a></p>
<?php require_once "../includes/footer.php"; ?>