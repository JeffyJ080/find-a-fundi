<?php

    require_once("../includes/auth.php");
    requireRole("fundi");

    require_once("../config/db.php");

    $message = "";

    try {
        $categoryQuery = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name");
        $categories = $categoryQuery->fetch_all(MYSQLI_ASSOC);
    } catch(mysqli_sql_exception $e){
        $categories = [];
        $message = "Failed to load categories.";
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST"){
        $fundi_id = $_SESSION["user_id"];
        $category_id = $_POST["category_id"];
        $title = trim($_POST["title"]);
        $description = trim($_POST["description"]);
        $price = $_POST["price"];
        $location = trim($_POST["location"]);
        $availability = trim($_POST["availability"]);

        if (empty($category_id) || empty($title) || empty($description) || empty($price)) {
        $message = "Please fill in all required fields.";
        } else {
            try {
                $stmt = $conn->prepare("
                    INSERT INTO services 
                    (fundi_id, category_id, title, description, price, location, availability)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");

                $stmt->bind_param(
                    "iissdss",
                    $fundi_id,
                    $category_id,
                    $title,
                    $description,
                    $price,
                    $location,
                    $availability
                );

                $stmt->execute();
                $stmt->close();

                $message = "Service created successfully.";

            } catch (mysqli_sql_exception $e) {
                if ($e->getCode() == 1452) {
                    $message = "Invalid category selected.";
                } else {
                    $message = "Could not create service.";
                }
            }
        }
    }

?>

<h1>Create Service</h1>

<p><?php echo htmlspecialchars($message); ?></p>

<form method="POST">
    <label>Category</label><br>
    <select name="category_id" required>
        <option value="">Select category</option>

        <?php foreach ($categories as $category): ?>
            <option value="<?php echo $category['category_id']; ?>">
                <?php echo htmlspecialchars($category['category_name']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <br><br>

    <label>Service Title</label><br>
    <input type="text" name="title" required>

    <br><br>

    <label>Description</label><br>
    <textarea name="description" required></textarea>

    <br><br>

    <label>Price</label><br>
    <input type="number" step="0.01" name="price" required>

    <br><br>

    <label>Location</label><br>
    <input type="text" name="location">

    <br><br>

    <label>Availability</label><br>
    <input type="text" name="availability" placeholder="Weekends, evenings, etc.">

    <br><br>

    <button type="submit">Create Service</button>
</form>

<br>

<a href="dashboard.php">Back to Dashboard</a>