<?php
    require_once "../includes/header.php";
    require_once "../includes/auth.php";
    requireRole("admin");

    require_once "../config/db.php";

    $message = "";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $user_id = (int) $_POST["user_id"];
        $account_status = $_POST["account_status"];

        if (in_array($account_status, ["active", "inactive"])) {
            try {
                $stmt = $conn->prepare("
                    UPDATE users
                    SET account_status = ?
                    WHERE user_id = ?
                    AND role != 'admin'
                ");

                $stmt->bind_param("si", $account_status, $user_id);
                $stmt->execute();
                $stmt->close();

                $message = "User status updated.";
            } catch (mysqli_sql_exception $e) {
                $message = "Could not update user.";
            }
        }
    }

    try {
        $result = $conn->query("
            SELECT user_id, full_name, email, phone, location, role, verified_status, account_status, created_at
            FROM users
            ORDER BY created_at DESC
        ");
    } catch (mysqli_sql_exception $e) {
        $message = "Could not load users.";
    }
?>

<h1>Manage Users</h1>

<p><?php echo htmlspecialchars($message); ?></p>

<?php if (isset($result) && $result->num_rows > 0): ?>
    <?php while ($user = $result->fetch_assoc()): ?>
        <div class="card">
            <h2><?php echo htmlspecialchars($user["full_name"]); ?></h2>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user["email"]); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($user["phone"]); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($user["location"]); ?></p>
            <p><strong>Role:</strong> <?php echo htmlspecialchars($user["role"]); ?></p>
            <p><strong>Verified:</strong> <?php echo $user["verified_status"] ? "Yes" : "No"; ?></p>
            <p><strong>Account Status:</strong> <?php echo htmlspecialchars($user["account_status"]); ?></p>
            <p><strong>Joined:</strong> <?php echo htmlspecialchars($user["created_at"]); ?></p>
            <form method="POST">
                <input type="hidden" name="user_id" value="<?php echo $user["user_id"]; ?>">
                <input type="hidden" name="account_status" value="<?php echo $user["account_status"] === "active" ? "inactive" : "active"; ?>">
                <button type="submit">
                    <?php echo $user["account_status"] === "active" ? "Deactivate" : "Activate"; ?>
                </button>
            </form>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No users found.</p>
<?php endif; ?>

<a href="dashboard.php">Back to Dashboard</a>
<?php require_once "../includes/footer.php"; ?>