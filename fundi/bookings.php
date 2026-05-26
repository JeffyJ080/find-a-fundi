<?php

    require_once "../includes/auth.php";
    requireRole("fundi");

    require_once "../config/db.php";

    $message = "";
    $fundi_id = $_SESSION["user_id"];

    if ($_SERVER["REQUEST_METHOD"] === "POST"){
        $booking_id = (int) $_POST["booking_id"];
        $status = $_POST["status"];

        $allowed = ["pending", "accepted", "completed", "cancelled"];

        if (in_array($status, $allowed)){
            try {
                $stmt = $conn->prepare("
                    UPDATE bookings
                    INNER JOIN services ON bookings.service_id = services.service_id
                    SET bookings.status = ?
                    WHERE bookings.booking_id = ?
                    AND services.fundi_id = ?
                ");

                $stmt->bind_param("sii", $status, $booking_id, $fundi_id);
                $stmt->execute();
            } catch (\mysqli_sql_exception $e) {
                $message = "Could not update the booking.";
            }
        }
    }

    $filter = $_GET["filter"] ?? "active";

    $allowedFilters = ["active", "pending", "accepted", "completed", "cancelled", "all"];

    if (!in_array($filter, $allowedFilters)) {
        $filter = "active";
    }

    try {
        $sql = "
            SELECT 
                bookings.booking_id,
                bookings.booking_date,
                bookings.booking_time,
                bookings.status,
                bookings.notes,
                services.title,
                users.full_name AS client_name,
                users.email AS client_email,
                users.phone AS client_phone
            FROM bookings
            INNER JOIN services ON bookings.service_id = services.service_id
            INNER JOIN users ON bookings.client_id = users.user_id
            WHERE services.fundi_id = ?
        ";

        if ($filter === "active") {
            $sql .= " AND bookings.status != 'cancelled'";
        } elseif ($filter !== "all") {
            $sql .= " AND bookings.status = ?";
        }

        $sql .= " ORDER BY bookings.booking_date DESC, bookings.booking_time DESC";

        $stmt = $conn->prepare($sql);

        if ($filter === "active" || $filter === "all") {
            $stmt->bind_param("i", $fundi_id);
        } else {
            $stmt->bind_param("is", $fundi_id, $filter);
        }

        $stmt->execute();
        $result = $stmt->get_result();
    } catch (mysqli_sql_exception $e) {
        $message = "Could not load bookings.";
}
?>

<h1>My Bookings</h1>

<form method="GET">
    <label>Filter bookings:</label>

    <select name="filter" onchange="this.form.submit()">
        <option value="active" <?php if ($filter === "active") echo "selected"; ?>>Active</option>
        <option value="pending" <?php if ($filter === "pending") echo "selected"; ?>>Pending</option>
        <option value="accepted" <?php if ($filter === "accepted") echo "selected"; ?>>Accepted</option>
        <option value="completed" <?php if ($filter === "completed") echo "selected"; ?>>Completed</option>
        <option value="cancelled" <?php if ($filter === "cancelled") echo "selected"; ?>>Cancelled</option>
        <option value="all" <?php if ($filter === "all") echo "selected"; ?>>All</option>
    </select>
</form>

<br>

<p><?php echo htmlspecialchars($message); ?></p>

<?php if (isset($result) && $result->num_rows > 0): ?>

    <?php while ($booking = $result->fetch_assoc()): ?>
        <div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
            <h2><?php echo htmlspecialchars($booking["title"]); ?></h2>

            <p><strong>Client:</strong> <?php echo htmlspecialchars($booking["client_name"]); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($booking["client_email"]); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($booking["client_phone"]); ?></p>
            <p><strong>Date:</strong> <?php echo htmlspecialchars($booking["booking_date"]); ?></p>
            <p><strong>Time:</strong> <?php echo htmlspecialchars($booking["booking_time"]); ?></p>
            <p><strong>Notes:</strong> <?php echo nl2br(htmlspecialchars($booking["notes"])); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($booking["status"]); ?></p>

            <form method="POST">
                <input type="hidden" name="booking_id" value="<?php echo $booking["booking_id"]; ?>">

                <select name="status">
                    <option value="pending">Pending</option>
                    <option value="accepted">Accepted</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>

                <button type="submit">Update Status</button>
            </form>
        </div>
    <?php endwhile; ?>

<?php else: ?>
    <p>No bookings yet.</p>
<?php endif; ?>

<a href="dashboard.php">Back to Dashboard</a>