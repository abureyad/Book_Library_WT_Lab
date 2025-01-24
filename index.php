<?php
session_start();
if (!isset($_SESSION["uname"])) {
    header("Location: signup.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "users_info");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Include featured books
require 'featured_books.php';

// Fetch books from requested_books table
$sql = "SELECT book_title FROM requested_books";
$result = $conn->query($sql);
$available_books = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $available_books[] = $row['book_title'];
    }
}

// Retrieve borrowed books from the session
$borrowed_books = $_SESSION['borrowed_books'] ?? [];

// Fetch used tokens from the database
$sql = "SELECT token FROM used_tokens";
$result = $conn->query($sql);
$used_tokens = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $used_tokens[] = $row['token'];
    }
}

// Fetch tokens from tokens.json
$tokens_json = file_get_contents('tokens.json');
$tokens_data = json_decode($tokens_json, true);
$all_tokens = $tokens_data['tokens'];
$available_tokens = array_diff($all_tokens, $used_tokens);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Header Section -->
<div class="header">
        <p>Welcome, <b><?php echo htmlspecialchars($_SESSION["uname"]); ?></b> | <a href="private.php">Sign Out</a></p>
    </div>

    <!-- Layout Wrapper -->
    <div class="layout-wrapper">
        <h2 style="position: absolute; top: 5px; left: 80px;">User Dashboard</h2>

        <!-- Featured Books Section -->
        <div id="large-container">
            <h3>Featured Books</h3>
            <div class="featured-books-wrapper">
                <?php if (!empty($featured_books)): ?>
                    <?php foreach ($featured_books as $image): ?>
                        <div class="featured-book-item">
                            <img src="<?php echo $image; ?>" alt="Featured Book">
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No featured books available at the moment.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Info and Forms Section -->
        <div class="row-containers">
            <!-- Borrow Stats -->
            <div class="small-container">
                <h3>Borrow Stats:</h3>
                <ul>
                    <?php if (!empty($borrowed_books)): ?>
                        <?php foreach ($borrowed_books as $book): ?>
                            <li><?php echo htmlspecialchars($book); ?></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No books borrowed yet.</p>
                    <?php endif; ?>
                </ul>
            </div>

<!-- Available Books -->
<div class="small-container">
    <h3>Available Books:</h3>
    <ul>
        <!-- Predefined Book Names -->
        <li>Da Vinci Code</li>
        <li>The Art of War</li>
        <li>The Great Gatsby</li>
        <li>Principles of Accounting</li>
        <li>Introduction to JAVA</li>
        <li>Introduction to Database</li>
        <li>Web Tech and Development</li>
        <li>Microprocessor and Embedded System</li>
        <li>Data Communication</li>
        <li>Dracula</li>
        <li>Good To Great</li>
        <li>Animal Farm</li>
        
        <!-- Dynamically Fetched Book Names -->
        <?php if (!empty($available_books)): ?>
            <?php foreach ($available_books as $book): ?>
                <li><?php echo htmlspecialchars($book); ?></li>
            <?php endforeach; ?>
        <?php else: ?>
            <p>....</p>
        <?php endif; ?>
    </ul>
</div>


            <!-- Tokens Section -->
            <div class="small-container">
                <h3>Tokens Available:</h3>
                <ul>
                    <?php if (!empty($available_tokens)): ?>
                        <?php foreach ($available_tokens as $token): ?>
                            <li><?php echo htmlspecialchars($token); ?></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No tokens available at the moment.</p>
                    <?php endif; ?>
                </ul>
                <h3>Used Tokens:</h3>
                <ul>
                    <?php if (!empty($used_tokens)): ?>
                        <?php foreach ($used_tokens as $token): ?>
                            <li><?php echo htmlspecialchars($token); ?></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No tokens used yet.</p>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- Borrow and Request Book Forms -->
        <div class="forms-wrapper">
            <!-- Borrow Form -->
            <div class="form-container">
                <h2>Borrow Form</h2>
                <form method="POST" action="submit.php">
                    <div class="form-group">
                        <label for="studentName">Student Full Name:</label>
                        <input type="text" id="studentName" name="studentName" required>
                    </div>
                    <div class="form-group">
                        <label for="studentID">Student ID:</label>
                        <input type="text" id="studentID" name="studentID" required>
                    </div>
                    <div class="form-group">
                        <label for="studentmail">Student Email:</label>
                        <input type="email" id="studentmail" name="studentmail" pattern="[a-zA-Z0-9._%+-]+@student\.aiub\.edu" title="Email must follow the format: user@student.aiub.edu" required>
                    </div>
                    <div class="form-group">
    <label for="bookTitle">Book Title:</label>
    <select id="bookTitle" name="bookTitle" required>
        <option value="" disabled selected>Select a book</option>
        <!-- Predefined Books -->
        <option value="Da Vinci Code">Da Vinci Code</option>
        <option value="The Art of War">The Art of War</option>
        <option value="The Great Gatsby">The Great Gatsby</option>
        <option value="Principles of Accounting">Principles of Accounting</option>
        <option value="Introduction to JAVA">Introduction to JAVA</option>
        <option value="Introduction to Database">Introduction to Database</option>
        <option value="Web Tech and Development">Web Tech and Development</option>
        <option value="Microprocessor and Embedded System">Microprocessor and Embedded System</option>
        <option value="Data Communication">Data Communication</option>
        <option value="Dracula">Dracula</option>
        <option value="Good To Great">Good To Great</option>
        <option value="Animal Farm">Animal Farm</option>

        <!-- Dynamically Fetched Books -->
        <?php foreach ($available_books as $book): ?>
            <option value="<?php echo htmlspecialchars($book); ?>"><?php echo htmlspecialchars($book); ?></option>
        <?php endforeach; ?>
    </select>
</div>

                    <div class="form-group">
                        <label for="borrowDate">Borrow Date:</label>
                        <input type="date" id="borrowDate" name="borrowDate" required>
                    </div>
                    <div class="form-group">
                        <label for="returnDate">Return Date:</label>
                        <input type="date" id="returnDate" name="returnDate" required>
                    </div>
                    <div class="form-group">
                        <label for="fees">Fees:</label>
                        <input type="number" id="fees" name="fees" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="token">Token:</label>
                        <input type="text" id="token" name="token" placeholder="Optional">
                        <small>Use a valid token to extend the borrowing period to 60 days.</small>
                    </div>
                    <div class="form-group">
                        <label>Paid:</label>
                        <div class="radio-group">
                            <label><input type="radio" name="paid" value="yes"> Yes</label>
                            <label><input type="radio" name="paid" value="no"> No</label>
                        </div>
                    </div>
                    <button type="submit">Submit</button>
                </form>
            </div>

            <!-- Request Book Form -->
            <div class="form-container">
                <h2>Book Details</h2>
                <form method="POST" action="add_book.php">
                    <div class="form-group">
                        <label for="bookTitleSmall">Book Title:</label>
                        <input type="text" id="bookTitleSmall" name="bookTitleSmall" required>
                    </div>
                    <div class="form-group">
                        <label for="authorName">Author Name:</label>
                        <input type="text" id="authorName" name="authorName" required>
                    </div>
                    <div class="form-group">
                        <label for="isbn_no">ISBN Number:</label>
                        <input type="text" id="isbn_no" name="isbn_no" required>
                    </div>
                    <button type="submit">Request Book</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
