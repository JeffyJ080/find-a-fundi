<?php
    require_once "../includes/header.php";
    require_once "../includes/auth.php";
    requireRole("fundi");
?>

<h1>Fundi Dashboard</h1>
<p>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>.</p>

<p><a href="create-service.php">Create Service</a></p>
<p><a href="services.php">My Services</a></p>
<p><a href="bookings.php">View Bookings</a></p>

<p><a href="../logout.php">Logout</a></p>
<?php require_once "../includes/footer.php"; ?>