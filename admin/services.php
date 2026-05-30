<?php
    require_once "../includes/header.php";
    require_once "../includes/auth.php";
    requireRole("admin");

    require_once "../config/db.php";

    $message = "";

    try {
        $result = $conn->query("
            SELECT 
                services.service_id,
                services.title,
                services.description,
                services.price,
                services.location,
                services.availability,
                services.status,
                services.created_at,
                categories.category_name,
                users.full_name AS fundi_name,
                users.email AS fundi_email
            FROM services
            INNER JOIN categories ON services.category_id = categories.category_id
            INNER JOIN users ON services.fundi_id = users.user_id
            ORDER BY services.created_at DESC
        ");
    } catch (mysqli_sql_exception $e) {
        $message = "Could not load services.";
    }
?>

<h1>Manage Services</h1>

<p><?php echo htmlspecialchars($message); ?></p>

<?php if (isset($result) && $result->num_rows > 0): ?>
    <?php while ($service = $result->fetch_assoc()): ?>
        <div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
            <h2><?php echo htmlspecialchars($service["title"]); ?></h2>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($service["category_name"]); ?></p>
            <p><strong>Fundi:</strong> <?php echo htmlspecialchars($service["fundi_name"]); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($service["fundi_email"]); ?></p>
            <p><strong>Price:</strong> R<?php echo number_format($service["price"], 2); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($service["location"]); ?></p>
            <p><strong>Availability:</strong> <?php echo htmlspecialchars($service["availability"]); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($service["status"]); ?></p>
            <p><?php echo nl2br(htmlspecialchars($service["description"])); ?></p>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No services found.</p>
<?php endif; ?>

<a href="dashboard.php">Back to Dashboard</a>
<?php require_once "../includes/footer.php"; ?>