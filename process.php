<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "users_info");

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Handle different form submissions
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Debugging: Display received POST data
    echo "POST data received:<br>";
    print_r($_POST);

    // Handle Book Details Form Submission
    if (isset($_POST['bookTitleSmall'], $_POST['authorName'], $_POST['numBooks'], $_POST['isbn_no'])) {
        // Sanitize and prepare data
        $bookTitleSmall = $conn->real_escape_string($_POST['bookTitleSmall']);
        $authorName = $conn->real_escape_string($_POST['authorName']);
        $numBooks = $conn->real_escape_string($_POST['numBooks']);
        $isbnNo = $conn->real_escape_string($_POST['isbn_no']);

        // SQL query to insert book details into the database
        $sql = "INSERT INTO requested_books (book_title, author_name, number_of_books, isbn_no)
                VALUES ('$bookTitleSmall', '$authorName', '$numBooks', '$isbnNo')";

        // Execute the query and handle errors
        if ($conn->query($sql) === TRUE) {
            echo "Requested book added successfully!";
            header("Location: index.php");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Error: Required fields are missing in the Book Details form.";
    }
}

// Close the database connection
$conn->close();
?>
