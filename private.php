<?php
session_start();
if (!isset($_SESSION["uname"])) {
    header("Location: signup.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Destroy session and redirect to signup
    session_destroy();
    header("Location: signup.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Private</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container">
        <h2>Currently Signed In As:</h2>
        <p><b><?php echo htmlspecialchars($_SESSION["uname"]); ?></b></p>
        <form method="POST">
            <button type="submit">Sign Out</button>
        </form>
    </div>
</body>
</html>
