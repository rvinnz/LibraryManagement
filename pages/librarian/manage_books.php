<?php 
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit;
}

require_once "../../classes/books.php";

$bookObj = new Books();

// Initialize variables
$book = [
    'id' => '',
    'title' => '',
    'author' => '',
    'published_date' => '', 
    'genre' => ''
];
$error = [];
$message = "";
$isEditing = false;

// 1. Handle DELETE Request (GET)
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if($bookObj->deleteBook($id)){
        $message = "Book deleted successfully!";
    } else {
        $error['error'] = "Failed to delete book.";
    }
}

// 2. Handle EDIT Request (GET) - Populates the form
if (isset($_GET['edit'])) {
    $isEditing = true;
    $id = $_GET['edit'];
    $fetchedBook = $bookObj->fetchBook($id);
    if ($fetchedBook) {
        $book = $fetchedBook;
    }
}

// 3. Handle FORM SUBMISSION (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book["title"] = $_POST["title"] ?? "";
    $book["author"] = $_POST["author"] ?? "";
    $book["published_date"] = $_POST["published"] ?? "";
    $book["genre"] = $_POST["genre"] ?? "";
    $book_id = $_POST["book_id"] ?? "";

    // Validation
    if(empty($book["title"]) || empty($book["author"]) || empty($book['published_date']) || empty($book['genre'])){
        $error['all'] = "All fields are required!";
    }

    if(empty($error)){
        try {
            $bookObj->title = $book["title"];
            $bookObj->author = $book["author"];
            $bookObj->published_date = $book["published_date"];
            $bookObj->genre = $book["genre"];

            if (!empty($book_id)) {
                // --- UPDATE ---
                $bookObj->updateBook($book_id);
                $message = "Book updated successfully!";
                $isEditing = false;
                $book = ['id' => '', 'title' => '', 'author' => '', 'published_date' => '', 'genre' => ''];
            } else {
                // --- CREATE ---
                $bookObj->insertBook();
                $message = "New book added to inventory!";
                $book = ['id' => '', 'title' => '', 'author' => '', 'published_date' => '', 'genre' => ''];
            }
        } catch(Exception $e) {
            $error["error"] = $e->getMessage();
        }
    }
}

$displayBook = $bookObj->displayBooks();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-R" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage Books - Librarian Panel</title>
    <link rel="stylesheet" href="../../assets/css/librarian.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="../../assets/icons/favicon.png">
    <style>
        /* Ensure form layout matches admin exactly since librarian.css might lack these specific flex classes */
        .form-row { display: flex; gap: 20px; margin-bottom: 15px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { font-weight: 500; margin-bottom: 8px; font-size: 14px; }
        .form-group input { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc; font-size: 15px; font-family: "Poppins", sans-serif; box-sizing: border-box; }
    </style>
  </head>
  <body>
    <img src="../../assets/img/Zamboanga_Peninsula_Polytechnic_State_University.png" alt="ZPPSU Logo" class="top-logo" />

    <div class="panel-container">
      <!-- SIDEBAR -->
      <nav class="sidebar">
        <div class="sidebar-header">
          <h3>Librarian Panel</h3>
          <p>User: <?= htmlspecialchars($_SESSION['username']) ?></p>
        </div>
        <ul class="sidebar-links">
          <li><a href="librarian.php">Dashboard</a></li>
          <li><a href="issue_book.php">Issue Books</a></li>
          <li><a href="return_book.php">Return Books</a></li>
          <li><a href="manage_books.php" class="active">Manage Books</a></li> <!-- Active -->
          <li><a href="manage_borrowers.php">View Borrowers</a></li>
          <li class="logout-link"><a href="../../login.php">Log Out</a></li>
        </ul>
      </nav>

      <main class="main-content">
        <header class="main-header">
          <h1>Manage Books</h1>
        </header>

        <!-- ADD / EDIT FORM -->
        <section class="content-card">
          <h2><?= $isEditing ? "Edit Book Details" : "Add New Book" ?></h2>
          
          <form id="add-book-form" method="post">
            
            <!-- Message Container (Clean spacing like Admin) -->
            <div class="message-container" style="margin-bottom: 20px; margin-top: 5px; min-height: 20px;">
                <p class="error"><?= $error['all'] ?? ""?></p>
                <p class="error"><?= $error['error'] ?? ""?></p>
                <p class="message" style="color: green; margin: 0;"><?= $message ?? "" ?></p>
            </div>

            <input type="hidden" name="book_id" value="<?= $book['id'] ?? '' ?>">

            <!-- Row 1: Title (Wide) and Date (Narrow) -->
            <div class="form-row">
              <div class="form-group" style="flex: 2;">
                <label for="title">Book Title</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($book['title']) ?>" />
              </div>
              <div class="form-group" style="flex: 1;">
                <label for="published">Published Date</label>
                <input type="date" name="published" id="published" value="<?= !empty($book['published_date']) ? date('Y-m-d', strtotime($book['published_date'])) : '' ?>">
              </div>
            </div>

            <!-- Row 2: Author (Normal) and Genre (Narrow) -->
            <div class="form-row">
              <div class="form-group" style="flex: 1;">
                <label for="author">Author</label>
                <input type="text" id="author" name="author" value="<?= htmlspecialchars($book['author']) ?>" />
              </div>
              <div class="form-group" style="flex: 0.5;">
                <label for="genre">Genre</label>
                <input type="text" name="genre" value="<?= htmlspecialchars($book['genre']) ?>">
              </div>
            </div>
            
            <button type="submit" class="btn-primary" style="margin-top: 10px;"><?= $isEditing ? "Update Book" : "Add Book" ?></button>
            
            <?php if($isEditing): ?>
                <a href="manage_books.php" style="margin-left: 15px; text-decoration: none; color: #666; font-size: 14px;">Cancel</a>
            <?php endif; ?>
          </form>
        </section>

        <!-- TABLE SECTION -->
        <section class="content-card">
          <h2>Book Inventory</h2>
          <table id="recent-books-table"> 
            <thead>
              <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Published</th>
                <th>Genre</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
            <?php 
                if($displayBook) {
                    foreach($displayBook as $b){
            ?>
              <tr>
                <td><?= htmlspecialchars($b['title']) ?></td>
                <td><?= htmlspecialchars($b['author']) ?></td>
                <td><?= htmlspecialchars(date('M d, Y', strtotime($b['published_date']))) ?></td>
                <td><?= htmlspecialchars($b['genre']) ?></td>
                <td>
                    <a href="manage_books.php?edit=<?= $b['id'] ?>" class="btn-secondary" style="padding: 6px 12px; text-decoration: none; font-size: 13px; border: none;">Edit</a>
                    <a href="manage_books.php?delete=<?= $b['id'] ?>" 
                       onclick="return confirm('Are you sure you want to delete this book?');" 
                       class="btn-secondary" 
                       style="padding: 6px 12px; text-decoration: none; font-size: 13px; color: #c92a2a; background: #fff5f5; border: 1px solid #ffc9c9;">Delete</a>
                </td>
            </tr>
            <?php 
                    } 
                } else {
                    echo "<tr><td colspan='5' style='text-align:center; padding: 20px;'>No books found in inventory.</td></tr>";
                }
            ?>
            </tbody>
          </table>
        </section>
      </main>
    </div>
  </body>
</html>