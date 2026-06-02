<?php
    require_once "includes/header.php";
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    require_once "config/db.php";

    $message = "";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $email = trim($_POST["email"]);
        $password = $_POST["password"];

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if ($user["account_status"] !== "active") {
                $message = "This account has been deactivated.";
            } elseif (password_verify($password, $user["password_hash"])) {
                $_SESSION["user_id"] = $user["user_id"];
                $_SESSION["full_name"] = $user["full_name"];
                $_SESSION["role"] = $user["role"];

                if ($user["role"] === "admin") {
                    header("Location: admin/dashboard.php");
                } elseif ($user["role"] === "fundi") {
                    header("Location: fundi/dashboard.php");
                } else {
                    header("Location: client/dashboard.php");
                }
                exit();
            }
        }

        $message = "Invalid email or password.";
    }
?>

<h1>Login</h1>

<p><?php echo htmlspecialchars($message); ?></p>

<form method="POST">
    <input type="email" name="email" placeholder="Email" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>

    <button type="submit">Login</button>
</form>

<p>No account? <a href="register.php">Register</a></p>
<?php require_once "includes/footer.php"; ?>