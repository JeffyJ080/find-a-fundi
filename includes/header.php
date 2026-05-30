<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find a Fundi</title>

    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<header>
    <h1>Find a Fundi</h1>
    <hr>
    <nav>
    <a href="../find-a-fundi/index.php">Home</a>

    <?php if (isset($_SESSION["user_id"])): ?>
        <a href="../find-a-fundi/logout.php">Logout</a>
    <?php endif; ?>
</nav>
</header>