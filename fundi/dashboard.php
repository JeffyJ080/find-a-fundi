<?php

require_once "../includes/auth.php";
requireRole("fundi");

?>

<h1>Fundi Dashboard</h1>

<p>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>.</p>
<a href="create-service.php">Create New Service</a>

<a href="../logout.php">Logout</a>
<a href="bookings.php">View Bookings</a>