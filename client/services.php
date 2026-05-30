<?php
    require_once "../includes/header.php";
    require_once "../config/db.php";

    $message = "";

    try {
        $query = "
            SELECT
                services.service_id,
                services.title,
                services.price,
                services.location,
                services.description,
                categories.category_name,
                users.full_name
            FROM services
            INNER JOIN categories
                ON services.category_id = categories.category_id
            INNER JOIN users
                ON services.fundi_id = users.user_id
            WHERE services.status = 'active'
            ORDER BY services.created_at DESC
        ";

        $result = $conn->query($query);
    } catch(mysqli_sql_exception $e){
        $message = "Could not load services: " . $e->getMessage();
    }
?>

<h1>Browse Services</h1>

<p><?php echo htmlspecialchars($message); ?></p>

<?php if (isset($result) && $result->num_rows > 0): ?>

    <?php while ($service = $result->fetch_assoc()): ?>

        <div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">

            <h2>
                <?php echo htmlspecialchars($service["title"]); ?>
            </h2>

            <p>
                <strong>Category:</strong>
                <?php echo htmlspecialchars($service["category_name"]); ?>
            </p>

            <p>
                <strong>Fundi:</strong>
                <?php echo htmlspecialchars($service["full_name"]); ?>
            </p>

            <p>
                <strong>Location:</strong>
                <?php echo htmlspecialchars($service["location"]); ?>
            </p>

            <p>
                <strong>Description:</strong>
                <?php echo htmlspecialchars($service["description"]); ?>
            </p>

            <p>
                <strong>Price:</strong>
                R<?php echo number_format($service["price"], 2); ?>
            </p>

            <a href="../service-details.php?id=<?php echo $service["service_id"]; ?>">
                View Details
            </a>

        </div>

    <?php endwhile; ?>

<?php else: ?>

    <p>No services available.</p>

<?php endif; ?>

<?php require_once "../includes/footer.php"; ?>