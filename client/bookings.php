<?php

    require_once "../includes/auth.php";
    requireRole("client");

    require_once "../config/db.php";

    $message = "";
    $client_id = $_SESSION["user_id"];

    try {
        $stmt = $conn->prepare("
            SELECT 
                bookings.booking_id,
                bookings.booking_date,
                bookings.booking_time,
                bookings.status,
                bookings.notes,
                services.title,
                services.price,
                services.location,
                users.full_name AS fundi_name,
                users.email AS fundi_email,
                users.phone AS fundi_phone,
                reviews.review_id
            FROM bookings
            INNER JOIN services ON bookings.service_id = services.service_id
            INNER JOIN users ON services.fundi_id = users.user_id
            LEFT JOIN reviews ON bookings.booking_id = reviews.booking_id
            WHERE bookings.client_id = ?
            ORDER BY bookings.booking_date DESC, bookings.booking_time DESC
        ");

        $stmt->bind_param("i", $client_id);
        $stmt->execute();

        $result = $stmt->get_result();
    } catch (\mysqli_sql_exception $e) {
        $message = "Could not load bookings.";
    }
?>

<h1>My Bookings</h1>

<p><?php echo htmlspecialchars($message); ?></p>

<?php if (isset($result) && $result->num_rows > 0): ?>

    <?php while ($booking = $result->fetch_assoc()): ?>
        <div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
            <h2><?php echo htmlspecialchars($booking["title"]); ?></h2>

            <p><strong>Fundi:</strong> <?php echo htmlspecialchars($booking["fundi_name"]); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($booking["fundi_email"]); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($booking["fundi_phone"]); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($booking["location"]); ?></p>
            <p><strong>Price:</strong> R<?php echo number_format($booking["price"], 2); ?></p>
            <p><strong>Date:</strong> <?php echo htmlspecialchars($booking["booking_date"]); ?></p>
            <p><strong>Time:</strong> <?php echo htmlspecialchars($booking["booking_time"]); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($booking["status"]); ?></p>
            <?php if ($booking["status"] === "completed" && empty($booking["review_id"])): ?>

                <a href="review-service.php?booking_id=<?php echo $booking["booking_id"]; ?>">
                    Leave Review
                </a>

            <?php elseif ($booking["status"] === "completed" && !empty($booking["review_id"])): ?>

                <p><strong>Review already submitted.</strong></p>

            <?php endif; ?>
            <p><strong>Notes:</strong> <?php echo nl2br(htmlspecialchars($booking["notes"])); ?></p>
        </div>
    <?php endwhile; ?>

<?php else: ?>

    <p>You have not made any bookings yet.</p>

<?php endif; ?>

<a href="dashboard.php">Back to Dashboard</a>