<?php

    require_once "config/db.php";

    $message = "";
    $service = NULL;

    if (!isset($_GET["id"])){
        die("No service selected.");
    }

    $service_id = (int) $_GET["id"];

    try {
            $stmt = $conn->prepare("
                SELECT 
                    services.service_id,
                    services.title,
                    services.description,
                    services.price,
                    services.location,
                    services.availability,
                    services.created_at,
                    categories.category_name,
                    users.user_id AS fundi_id,
                    users.full_name,
                    users.email,
                    users.phone
                FROM services
                INNER JOIN categories 
                    ON services.category_id = categories.category_id
                INNER JOIN users 
                    ON services.fundi_id = users.user_id
                WHERE services.service_id = ?
                LIMIT 1
            ");

        $stmt->bind_param("i", $service_id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1){
            $service = $result->fetch_assoc();
        } else{
            $message = "Service not found.";
        }

        $stmt->close();
    } catch(mysqli_sql_exception $e){
        $message = "Could not load service.";
    }

?>

<h1>Service Details</h1>

<p><?php echo htmlspecialchars($message); ?></p>

<?php if ($service): ?>

    <div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">

        <h2><?php echo htmlspecialchars($service["title"]); ?></h2>

        <p>
            <strong>Category:</strong>
            <?php echo htmlspecialchars($service["category_name"]); ?>
        </p>

        <p>
            <strong>Description:</strong><br>
            <?php echo nl2br(htmlspecialchars($service["description"])); ?>
        </p>

        <p>
            <strong>Price:</strong>
            R<?php echo number_format($service["price"], 2); ?>
        </p>

        <p>
            <strong>Location:</strong>
            <?php echo htmlspecialchars($service["location"]); ?>
        </p>

        <p>
            <strong>Availability:</strong>
            <?php echo htmlspecialchars($service["availability"]); ?>
        </p>

        <hr>

        <h3>Fundi Information</h3>

        <p>
            <strong>Name:</strong>
            <?php echo htmlspecialchars($service["full_name"]); ?>
        </p>

        <p>
            <strong>Email:</strong>
            <?php echo htmlspecialchars($service["email"]); ?>
        </p>

        <p>
            <strong>Phone:</strong>
            <?php echo htmlspecialchars($service["phone"]); ?>
        </p>

        <a href="book-service.php?id=<?php echo $service["service_id"]; ?>">
            Book This Service
        </a>

    </div>

<?php endif; ?>

<a href="client/services.php">Back to Services</a>