<?php
    require_once "includes/header.php";
    require_once "includes/auth.php";
    requireRole("client");

    require_once "config/db.php";

    $message = "";
    $service = null;

    if (!isset($_GET["id"])){
        die("No service selected.");
    }

    $service_id = (int) $_GET["id"];

    try{
        $stmt = $conn->prepare("
            SELECT service_id, title
            FROM services
            WHERE service_id = ?
            LIMIT 1
        ");

        $stmt->bind_param("i", $service_id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $service = $result->fetch_assoc();
        } else{
            die("Service not found.");
        }

        $stmt->close();
    } catch(mysqli_sql_exception $e){
        die("Could not load service.");
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $client_id = $_SESSION["user_id"];
        $booking_date = $_POST["booking_date"];
        $booking_time = $_POST["booking_time"];
        $notes = trim($_POST["notes"]);

        try {
            $stmt = $conn->prepare("
                INSERT INTO bookings
                (service_id, client_id, booking_date, booking_time, notes)
                VALUES (?, ?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "iisss",
                $service_id,
                $client_id,
                $booking_date,
                $booking_time,
                $notes
            );

            $stmt->execute();

            $stmt->close();

            $message = "Booking created successfully.";
        } catch(mysqli_sql_exception $e) {
            if ($e->getCode() == 1452) {
                $message = "Invalid booking information.";
            } else {
                $message = "Could not create booking.";
            }
        }
    }
?>

<h1>Book Service</h1>

<p><?php echo htmlspecialchars($message); ?></p>

<?php if ($service): ?>
    <h2>
        <?php echo htmlspecialchars($service["title"]); ?>
    </h2>

    <form method="POST">

        <label>Date</label><br>
        <input type="date" name="booking_date" required>

        <br><br>

        <label>Time</label><br>
        <input type="time" name="booking_time" required>

        <br><br>

        <label>Notes</label><br>
        <textarea name="notes"></textarea>

        <br><br>

        <button type="submit">
            Confirm Booking
        </button>

    </form>
<?php endif; ?>
<br>
<a href="client/services.php">Back to Services</a>
<?php require_once "includes/footer.php"; ?>