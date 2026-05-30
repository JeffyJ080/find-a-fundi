<?php
    require_once "../includes/header.php";
    require_once "../includes/auth.php";
    requireRole("client");

    require_once "../config/db.php";

    $message = "";
    $booking = null;

    if (!isset($_GET["booking_id"])) {
        die("No booking selected.");
    }

    $booking_id = (int) $_GET["booking_id"];
    $client_id = $_SESSION["user_id"];

    try {
    $stmt = $conn->prepare("
        SELECT
            bookings.booking_id,
            bookings.service_id,
            services.title,
            services.fundi_id
        FROM bookings
        INNER JOIN services
            ON bookings.service_id = services.service_id
        WHERE bookings.booking_id = ?
        AND bookings.client_id = ?
        AND bookings.status = 'completed'
        LIMIT 1
    ");

    $stmt->bind_param("ii", $booking_id, $client_id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $booking = $result->fetch_assoc();
    } else {
        die("Invalid booking.");
    }

    $stmt->close();
    } catch (mysqli_sql_exception $e) {
        die("Could not load booking.");
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $rating = (int) $_POST["rating"];
        $comment = trim($_POST["comment"]);

        if ($rating < 1 || $rating > 5) {
            $message = "Rating must be between 1 and 5.";
        } else {
            try {
                $stmt = $conn->prepare("
                    INSERT INTO reviews
                    (booking_id, client_id, fundi_id, rating, comment)
                    VALUES (?, ?, ?, ?, ?)
                ");

                $stmt->bind_param(
                    "iiiis",
                    $booking_id,
                    $client_id,
                    $booking["fundi_id"],
                    $rating,
                    $comment
                );

                $stmt->execute();
                $stmt->close();

                $message = "Review submitted successfully.";
            } catch (mysqli_sql_exception $e) {
                if ($e->getCode() == 1062) {
                    $message = "You already reviewed this booking.";
                } else {
                    $message = "Could not submit review.";
                }
            }
        }
    }
?>

<h1>Leave Review</h1>

<p><?php echo htmlspecialchars($message); ?></p>

<h2>
    <?php echo htmlspecialchars($booking["title"]); ?>
</h2>

<form method="POST">
    <label>Rating</label><br>

    <select name="rating" required>
        <option value="">Select Rating</option>
        <option value="1">1 Star</option>
        <option value="2">2 Stars</option>
        <option value="3">3 Stars</option>
        <option value="4">4 Stars</option>
        <option value="5">5 Stars</option>
    </select>
    <br><br>
    <label>Comment</label><br>
    <textarea name="comment"></textarea>
    <br><br>
    <button type="submit">
        Submit Review
    </button>

</form>
<br>
<a href="bookings.php">Back to Bookings</a>
<?php require_once "../includes/footer.php"; ?>