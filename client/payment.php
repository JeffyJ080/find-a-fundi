<?php
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
                bookings.status,
                services.title,
                services.price,
                users.full_name AS fundi_name
            FROM bookings
            INNER JOIN services ON bookings.service_id = services.service_id
            INNER JOIN users ON services.fundi_id = users.user_id
            WHERE bookings.booking_id = ?
            AND bookings.client_id = ?
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
        $payment_method = $_POST["payment_method"];
        $amount = $booking["price"];

        try {
            $stmt = $conn->prepare("
                INSERT INTO payments
                (booking_id, amount, payment_method, payment_status)
                VALUES (?, ?, ?, 'paid')
            ");

            $stmt->bind_param("ids", $booking_id, $amount, $payment_method);
            $stmt->execute();
            $stmt->close();

            $message = "Payment completed successfully.";

        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $message = "This booking has already been paid.";
            } else {
                $message = "Could not process payment.";
            }
        }
    }
?>

<h1>Simulated Payment</h1>

<p><?php echo htmlspecialchars($message); ?></p>

<?php if ($booking): ?>

    <h2><?php echo htmlspecialchars($booking["title"]); ?></h2>

    <p><strong>Fundi:</strong> <?php echo htmlspecialchars($booking["fundi_name"]); ?></p>
    <p><strong>Amount:</strong> R<?php echo number_format($booking["price"], 2); ?></p>
    <p><strong>Booking Status:</strong> <?php echo htmlspecialchars($booking["status"]); ?></p>

    <form method="POST">
        <label>Payment Method</label><br>

        <select name="payment_method" required>
            <option value="">Select payment method</option>
            <option value="Simulated Card">Simulated Card</option>
            <option value="Simulated EFT">Simulated EFT</option>
            <option value="Simulated Cash">Simulated Cash</option>
        </select>

        <br><br>

        <button type="submit">Pay Now</button>
    </form>

<?php endif; ?>

<br>

<a href="bookings.php">Back to Bookings</a>