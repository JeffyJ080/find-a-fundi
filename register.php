<?php
require_once "config/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = trim($_POST["full_name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $location = trim($_POST["location"]);
    $role = $_POST["role"];
    $password = $_POST["password"];

    if (empty($full_name) || empty($email) || empty($password) || empty($role)) {
        $message = "Please fill in all required fields.";
    } else {
        try {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("
                INSERT INTO users (full_name, email, password_hash, phone, location, role)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param("ssssss", $full_name, $email, $password_hash, $phone, $location, $role);
            $stmt->execute();

            $stmt->close();
            $conn->close();

            header("Location: login.php");
            exit();

        } catch (mysqli_sql_exception $e) {
            if (isset($stmt)) {
                $stmt->close();
            }

            $conn->close();

            if ($e->getCode() == 1062) {
                $message = "An account with this email already exists.";
            } else {
                $message = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<h1>Register</h1>

<p><?php echo htmlspecialchars($message); ?></p>

<form method="POST">
    <input type="text" name="full_name" placeholder="Full Name" required><br><br>
    <input type="email" name="email" placeholder="Email" required><br><br>
    <input type="text" name="phone" placeholder="Phone"><br><br>
    <input type="text" name="location" placeholder="Location"><br><br>

    <select name="role" required>
        <option value="">Select Role</option>
        <option value="client">Client</option>
        <option value="fundi">Fundi</option>
    </select><br><br>

    <input type="password" name="password" placeholder="Password" required><br><br>

    <button type="submit">Register</button>
</form>

<p>Already have an account? <a href="login.php">Login</a></p>