<?php
    require_once "../includes/header.php";
    require_once "../includes/auth.php";
    requireRole("admin");

    require_once "../config/db.php";

    $message = "";

    try {
        $result = $conn->query("
            SELECT user_id, full_name, email, phone, location, role, verified_status, created_at
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
            <p><strong>Joined:</strong> <?php echo htmlspecialchars($user["created_at"]); ?></p>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No users found.</p>
<?php endif; ?>

<a href="dashboard.php">Back to Dashboard</a>
<?php require_once "../includes/footer.php"; ?>