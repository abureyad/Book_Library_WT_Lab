<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uname = $_POST["uname"];
    $pass = $_POST["pass"];

    // Database connection
    $conn = mysqli_connect('localhost', 'root', '', 'users_info');
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Check if the user exists
    $sql = "SELECT * FROM users WHERE username = '$uname'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($pass, $user['password'])) {
        // Save username to session
        $_SESSION["uname"] = $uname;

        // Redirect to index.html
        header("Location: index.html");
        exit();
    } else {
        echo "Invalid username or password.";
        header("refresh: 2; url = signin.php");
    }

    mysqli_close($conn);
}
?>
