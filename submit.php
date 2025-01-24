<?php
session_start();

// If form data is not available, redirect back to the index page
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "users_info");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Capture form data from POST request
$studentName = htmlspecialchars($_POST['studentName'] ?? 'N/A');
$studentID = htmlspecialchars($_POST['studentID'] ?? 'N/A');
$studentMail = htmlspecialchars($_POST['studentmail'] ?? 'N/A');
$bookTitle = htmlspecialchars($_POST['bookTitle'] ?? 'N/A');
$borrowDate = htmlspecialchars($_POST['borrowDate'] ?? 'N/A');
$returnDate = htmlspecialchars($_POST['returnDate'] ?? 'N/A');
$fees = htmlspecialchars($_POST['fees'] ?? 'N/A');
$token = htmlspecialchars($_POST['token'] ?? 'N/A');
$paid = htmlspecialchars($_POST['paid'] ?? 'N/A');

// Store the book title in a session variable
if (!isset($_SESSION['borrowed_books'])) {
    $_SESSION['borrowed_books'] = [];
}
$_SESSION['borrowed_books'][] = $bookTitle;

// Validate token only if it is provided
$isTokenValid = false;
if (!empty($token)) {
    $tokens_json = file_get_contents('tokens.json');
    $tokens_data = json_decode($tokens_json, true);
    $all_tokens = $tokens_data['tokens'];

    // Check if the token is valid and not used
    $sql = "SELECT COUNT(*) as count FROM used_tokens WHERE token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!in_array($token, $all_tokens)) {
        die("<p>Invalid token. Please use a valid token or leave the field empty.</p><a href='index.php'>Go Back</a>");
    } elseif ($row['count'] > 0) {
        die("<p>Token already used. Please use a different token or leave the field empty.</p><a href='index.php'>Go Back</a>");
    }

    $isTokenValid = true;

    // Add the token to the used_tokens table
    $sql = "INSERT INTO used_tokens (token, user) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $token, $_SESSION["uname"]);
    if (!$stmt->execute()) {
        die("Failed to save token: " . $conn->error);
    }
}

// Determine borrowing period
$borrowDateObj = new DateTime($borrowDate);
$returnDateObj = new DateTime($returnDate);
$dateDifference = $borrowDateObj->diff($returnDateObj)->days;

$allowedPeriod = $isTokenValid ? 60 : 30;

if ($dateDifference > $allowedPeriod) {
    $message = $isTokenValid
        ? "Your token allows a borrowing period of 60 days, but you've exceeded it."
        : "Use a valid token to extend the borrowing period to 60 days.";
    die("<p>$message</p><a href='index.php'>Go Back</a>");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrow Form Submission</title>
    <link rel="stylesheet" href="style.css"> <!-- Ensure this file includes the provided CSS -->
</head>
<body>
    <div class="summary-container">
        <h2>Form Submission Summary</h2>
        <p><strong>Student Name:</strong> <?php echo $studentName; ?></p>
        <p><strong>Student ID:</strong> <?php echo $studentID; ?></p>
        <p><strong>Student Email:</strong> <?php echo $studentMail; ?></p>
        <p><strong>Book Title:</strong> <?php echo $bookTitle; ?></p>
        <p><strong>Borrow Date:</strong> <?php echo $borrowDate; ?></p>
        <p><strong>Return Date:</strong> <?php echo $returnDate; ?></p>
        <p><strong>Fees:</strong> <?php echo $fees; ?></p>
        <p><strong>Token:</strong> <?php echo $token; ?></p>
        <p><strong>Paid:</strong> <?php echo $paid; ?></p>
        <form action="index.php" method="get">
            <button type="submit">Return to Dashboard</button>
        </form>
    </div>
</body>
</html>
