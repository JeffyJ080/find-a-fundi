<?php

    require_once "../includes/auth.php";
    requireRole("admin");

    require_once "../config/db.php";

    $message = "";

    try {
        $result = $conn->query("
            SELECT
                bookings.booking_id,
                bookings.booking_date,
                bookings.booking_time,
                bookings.status,
                bookings.notes,
                bookings.created_at,
                services.title AS service_title,
                client.full_name AS client_name,
                client.email AS client_email,
                fundi.full_name AS fundi_name,
                fundi.email AS fundi_email
            FROM bookings
            INNER JOIN services ON bookings.service_id = services.service_id
            INNER JOIN users AS client ON bookings.client_id = client.user_id
            INNER JOIN users AS fundi ON services.fundi_id = fundi.user_id
            ORDER BY bookings.created_at DESC
        ");
    } catch (mysqli_sql_exception $e) {
        $message = "Could not load bookings.";
    }
?>

<h1>Manage Bookings</h1>

<p><?php echo htmlspecialchars($message); ?></p>

<?php if (isset($result) && $result->num_rows > 0): ?>
    <?php while ($booking = $result->fetch_assoc()): ?>
        <div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
            <h2><?php echo htmlspecialchars($booking["service_title"]); ?></h2>

            <p><strong>Client:</strong> <?php echo htmlspecialchars($booking["client_name"]); ?></p>
            <p><strong>Client Email:</strong> <?php echo htmlspecialchars($booking["client_email"]); ?></p>

            <p><strong>Fundi:</strong> <?php echo htmlspecialchars($booking["fundi_name"]); ?></p>
            <p><strong>Fundi Email:</strong> <?php echo htmlspecialchars($booking["fundi_email"]); ?></p>

            <p><strong>Date:</strong> <?php echo htmlspecialchars($booking["booking_date"]); ?></p>
            <p><strong>Time:</strong> <?php echo htmlspecialchars($booking["booking_time"]); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($booking["status"]); ?></p>
            <p><strong>Notes:</strong> <?php echo nl2br(htmlspecialchars($booking["notes"])); ?></p>
            <p><strong>Created:</strong> <?php echo htmlspecialchars($booking["created_at"]); ?></p>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No bookings found.</p>
<?php endif; ?>

<a href="dashboard.php">Back to Dashboard</a>