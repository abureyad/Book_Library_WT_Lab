<?php
session_start();

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture form data
    $bookTitle = htmlspecialchars($_POST['bookTitleSmall'] ?? '');
    $authorName = htmlspecialchars($_POST['authorName'] ?? '');
    $isbnNo = htmlspecialchars($_POST['isbn_no'] ?? '');

    if (empty($bookTitle) || empty($authorName) || empty($isbnNo)) {
        die("All fields are required. Please fill out the form completely.");
    }

    $conn = new mysqli("localhost", "root", "", "users_info");
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // Insert data into the database
    $sql = "INSERT INTO requested_books (book_title, author_name, isbn_no) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $bookTitle, $authorName, $isbnNo);

    if ($stmt->execute()) {
        echo "<p>Book request added successfully!</p><a href='index.php'>Return to Dashboard</a>";
    } else {
        echo "<p>Failed to add the book request: " . $conn->error . "</p><a href='index.php'>Return to Dashboard</a>";
    }

} else {
    // If the form was not submitted, redirect back to the index page
    header("Location: index.php");
    exit();
}
?>
