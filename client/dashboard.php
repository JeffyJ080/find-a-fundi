<?php

require_once "../includes/auth.php";
requireRole("client");

?>

<h1>Client Dashboard</h1>
<p>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>.</p>
<a href="../logout.php">Logout</a>