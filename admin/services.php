<?php
    require_once "../includes/header.php";
    require_once "../includes/auth.php";
    requireRole("admin");

    require_once "../config/db.php";

    $message = "";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $service_id = (int) $_POST["service_id"];
        $status = $_POST["status"];

        if (in_array($status, ["active", "inactive"])) {
            try {
                $stmt = $conn->prepare("
                    UPDATE services
                    SET status = ?
                    WHERE service_id = ?
                ");

                $stmt->bind_param("si", $status, $service_id);
                $stmt->execute();
                $stmt->close();

                $message = "Service status updated.";
            } catch (mysqli_sql_exception $e) {
                $message = "Could not update service.";
            }
        }
    }

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
        <div class="card">
            <h2><?php echo htmlspecialchars($service["title"]); ?></h2>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($service["category_name"]); ?></p>
            <p><strong>Fundi:</strong> <?php echo htmlspecialchars($service["fundi_name"]); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($service["fundi_email"]); ?></p>
            <p><strong>Price:</strong> R<?php echo number_format($service["price"], 2); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($service["location"]); ?></p>
            <p><strong>Availability:</strong> <?php echo htmlspecialchars($service["availability"]); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($service["status"]); ?></p>
            <p><?php echo nl2br(htmlspecialchars($service["description"])); ?></p>
            <form method="POST">
                <input type="hidden" name="service_id" value="<?php echo $service["service_id"]; ?>">
                <input type="hidden" name="status" value="<?php echo $service["status"] === "active" ? "inactive" : "active"; ?>">
                <button type="submit">
                    <?php echo $service["status"] === "active" ? "Deactivate" : "Activate"; ?>
                </button>
            </form>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No services found.</p>
<?php endif; ?>

<a href="dashboard.php">Back to Dashboard</a>
<?php require_once "../includes/footer.php"; ?>