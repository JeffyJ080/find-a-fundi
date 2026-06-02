<?php
    require_once "includes/header.php";
    require_once "config/db.php";
    require_once "includes/auth.php";
    requireRole("client");

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

    <div class="card">
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

        <hr>

        <h3>Reviews</h3>

        <?php
        try {

            $stmt = $conn->prepare("
                SELECT
                    reviews.rating,
                    reviews.comment,
                    reviews.created_at,
                    users.full_name
                FROM reviews
                INNER JOIN users
                    ON reviews.client_id = users.user_id
                WHERE reviews.booking_id IN (
                    SELECT booking_id
                    FROM bookings
                    WHERE service_id = ?
                )
                ORDER BY reviews.created_at DESC
            ");

            $stmt->bind_param("i", $service_id);
            $stmt->execute();

            $reviewResult = $stmt->get_result();

            if ($reviewResult->num_rows > 0):

                while ($review = $reviewResult->fetch_assoc()):
        ?>

                    <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">

                        <p>
                            <strong>
                                <?php echo htmlspecialchars($review["full_name"]); ?>
                            </strong>
                        </p>

                        <p>
                            Rating:
                            <?php echo htmlspecialchars($review["rating"]); ?>/5
                        </p>

                        <p>
                            <?php echo nl2br(htmlspecialchars($review["comment"])); ?>
                        </p>

                        <p>
                            <small>
                                <?php echo htmlspecialchars($review["created_at"]); ?>
                            </small>
                        </p>

                    </div>

            <?php
                    endwhile;

                else:
                    echo "<p>No reviews yet.</p>";
                endif;

                $stmt->close();

            } catch (mysqli_sql_exception $e) {
                echo "<p>Could not load reviews.</p>";
            }
            ?>

    </div>

<?php endif; ?>

<a href="client/services.php">Back to Services</a>

<?php require_once "includes/footer.php"; ?>