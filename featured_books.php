<?php
$conn = new mysqli('localhost', 'root', '', 'users_info');

// Check the connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch featured books
$sql = "SELECT image_path FROM featured_books";
$result = $conn->query($sql);

$featured_books = [];

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $featured_books[] = $row['image_path'];
        }
    } else {
        echo "No featured books found in the database.";
    }
} else {
    echo "Error executing query: " . $conn->error;
}

?>