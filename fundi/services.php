<?php
    require_once "../includes/header.php";
    require_once "../includes/auth.php";
    requireRole("fundi");

    require_once "../config/db.php";

    $message = "";
    $fundi_id = $_SESSION["user_id"];

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $service_id = (int) $_POST["service_id"];

        try {
            $stmt = $conn->prepare("
                UPDATE services
                SET status = 'inactive'
                WHERE service_id = ?
                AND fundi_id = ?
            ");

            $stmt->bind_param("ii", $service_id, $fundi_id);
            $stmt->execute();
            $stmt->close();

            $message = "Service removed successfully.";
        } catch (mysqli_sql_exception $e) {
            $message = "Could not remove service.";
        }
    }

    try {
        $stmt = $conn->prepare("
            SELECT 
                services.service_id,
                services.title,
                services.description,
                services.price,
                services.location,
                services.availability,
                services.status,
                services.created_at,
                categories.category_name
            FROM services
            INNER JOIN categories ON services.category_id = categories.category_id
            WHERE services.fundi_id = ?
            ORDER BY services.created_at DESC
        ");

        $stmt->bind_param("i", $fundi_id);
        $stmt->execute();
        $result = $stmt->get_result();
    } catch (mysqli_sql_exception $e) {
        $message = "Could not load services.";
    }
?>

<h1>My Services</h1>

<p><?php echo htmlspecialchars($message); ?></p>

<p><a href="create-service.php">Create New Service</a></p>

<?php if (isset($result) && $result->num_rows > 0): ?>

    <?php while ($service = $result->fetch_assoc()): ?>

        <div class="card">
            <h2><?php echo htmlspecialchars($service["title"]); ?></h2>

            <p><strong>Category:</strong> <?php echo htmlspecialchars($service["category_name"]); ?></p>
            <p><strong>Price:</strong> R<?php echo number_format($service["price"], 2); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($service["location"]); ?></p>
            <p><strong>Availability:</strong> <?php echo htmlspecialchars($service["availability"]); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($service["status"]); ?></p>

            <p><?php echo nl2br(htmlspecialchars($service["description"])); ?></p>

            <?php if ($service["status"] === "active"): ?>
                <form method="POST" onsubmit="return confirm('Remove this service from public listings?');">
                    <input type="hidden" name="service_id" value="<?php echo $service["service_id"]; ?>">
                    <button type="submit">Remove Service</button>
                </form>
            <?php else: ?>
                <p><strong>This service is inactive.</strong></p>
            <?php endif; ?>
        </div>

    <?php endwhile; ?>

<?php else: ?>

    <p>You have not posted any services yet.</p>

<?php endif; ?>

<a href="dashboard.php">Back to Dashboard</a>
<?php require_once "../includes/footer.php"; ?>