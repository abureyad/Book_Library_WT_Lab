<?php
session_start();

// Redirect to dashboard if already logged in
if (isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

$error = '';
$conn = new mysqli("localhost", "root", "", "users_info");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $uname = $_POST["uname"] ?? '';
    $password = $_POST["password"] ?? '';

    if (!empty($uname) && !empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $uname, $hashed_password);

        if ($stmt->execute()) {
            // Automatically log in the user after registration
            $_SESSION["user_id"] = $stmt->insert_id;
            $_SESSION["uname"] = $uname;

            header("Location: index.php");
            exit();
        } else {
            $error = "Username already exists.";
        }
       // $stmt->close();
    } else {
        $error = "Both fields are required.";
    }
}
//$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container">
        <h2>Sign Up</h2>
        <form method="POST" action="signup.php">
            <div class="form-group">
                <label for="uname">Username:</label>
                <input type="text" id="uname" name="uname" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Sign Up</button>
        </form>
        <p>Already have an account? <a href="signin.php">Sign In</a></p>
        <p>hint: skip to sign in to avoid new signup</p>
        <?php if ($error): ?>
            <p style="color: red; text-align: center;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
