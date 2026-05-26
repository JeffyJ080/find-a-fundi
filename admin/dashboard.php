<?php

require_once "../includes/auth.php";
requireRole("admin");

?>

<h1>Admin Dashboard</h1>
<p>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>.</p>
<p><a href="users.php">Manage Users</a></p>
<p><a href="services.php">Manage Services</a></p>
<p><a href="bookings.php">Manage Bookings</a></p>
<p><a href="../logout.php">Logout</a></p>