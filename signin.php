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
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $uname);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                // Set session variables
                $_SESSION["user_id"] = $user_id;
                $_SESSION["uname"] = $uname;

                // Redirect to the dashboard
                header("Location: index.php");
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "Invalid username.";
        }
      //  $stmt->close();
    } else {
        $error = "Please enter both username and password.";
    }
}
// $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container">
        <h2>Sign In</h2>
        <form method="POST" action="signin.php">
            <div class="form-group">
                <label for="uname">Username:</label>
                <input type="text" id="uname" name="uname" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Sign In</button>
        </form>
        <p>Don’t have an account? <a href="signup.php">Sign Up</a></p>
        <p>hint: ben@student.aiub.edu, ben10</p>
        <?php if ($error): ?>
            <p style="color: red; text-align: center;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
