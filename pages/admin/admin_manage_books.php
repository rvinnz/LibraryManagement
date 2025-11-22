<?php 
require_once "../../classes/books.php";
session_start();

// Updated Check: Uses is_admin !== 1
if (!isset($_SESSION['username']) || $_SESSION['is_admin'] !== 1) {
    header("Location: ../../login.php");
    exit;
}
$admin_username = $_SESSION['username'];

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
        $book = $fetchedBook; // Fill form with existing data
    }
}

// 3. Handle FORM SUBMISSION (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book["title"] = $_POST["title"] ?? "";
    $book["author"] = $_POST["author"] ?? "";
    $book["published_date"] = $_POST["published"] ?? ""; // Note: input name is 'published'
    $book["genre"] = $_POST["genre"] ?? "";
    $book_id = $_POST["book_id"] ?? ""; // Hidden ID field

    // Validation
    if(empty($book["title"]) || empty($book["author"]) || empty($book['published_date']) || empty($book['genre'])){
        $error['all'] = "All Input Fields are required!";
    }

    if(empty($error)){
        try {
            // Set object properties
            $bookObj->title = $book["title"];
            $bookObj->author = $book["author"];
            $bookObj->published_date = $book["published_date"];
            $bookObj->genre = $book["genre"];

            if (!empty($book_id)) {
                // --- UPDATE EXISTING BOOK ---
                $bookObj->updateBook($book_id);
                $message = "Successfully Updated Book!";
                $isEditing = false; // Reset mode
                // Clear form data after update
                $book = ['id' => '', 'title' => '', 'author' => '', 'published_date' => '', 'genre' => ''];
            } else {
                // --- INSERT NEW BOOK ---
                $bookObj->insertBook();
                $message = "Successfully Inserted Book!";
                // Clear form data
                $book = ['id' => '', 'title' => '', 'author' => '', 'published_date' => '', 'genre' => ''];
            }
        } catch(Exception $e) {
            $error["error"] = $e->getMessage();
        }
    }
}

// Refresh list after actions
$displayBook = $bookObj->displayBooks();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-R" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage Books - Admin Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/adminpage.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="../../assets/icons/favicon.png">
  </head>
  <body>
    <div class="sidebar-overlay"></div>
    <button id="sidebarToggleBtn" type="button">&#9776;</button>

    <img src="../../assets/img/Zamboanga_Peninsula_Polytechnic_State_University.png" alt="ZPPSU Logo" class="top-logo" />

    <div class="admin-container">
      <nav class="sidebar">
        <div class="sidebar-header">
          <h3>Admin Panel</h3>
          <p>Welcome, <?= htmlspecialchars($admin_username) ?>!</p>
        </div>
        <ul class="sidebar-links">
          <li><a href="admin_dashboard.php">Dashboard</a></li>
          <li><a href="admin_manage_librarian.php">Manage Librarians</a></li>
          <li><a href="admin_manage_borrowers.php">View Borrowers</a></li>
          <li><a href="admin_manage_books.php" class="active">Manage Books</a></li>
          <li><a href="admin_view_logs.php">View Logs</a></li>
          <li class="logout-link"><a href="../../logout.php">Log Out</a></li>
        </ul>
      </nav>

      <main class="main-content">
        <header class="main-header">
          <h1>Manage Books</h1>
        </header>

        <section class="content-card">
          <h2><?= $isEditing ? "Edit Book" : "Add New Book" ?></h2>
          
          <form id="add-book-form" method="post">
            <p class="error"><?= $error['all'] ?? ""?></p>
            <p class="error"><?= $error['error'] ?? ""?></p>
            <p class="message" style="color: green"><?= $message ?? "" ?></p>

            <input type="hidden" name="book_id" value="<?= $book['id'] ?? '' ?>">

            <div class="form-row">
              <div class="form-group" style="flex: 2">
                <label for="title">Book Title</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($book['title']) ?>" />
              </div>
              <div class="form-group" style="flex: 1">
                <label for="published">Published</label>
                <input type="date" name="published" id="published" 
                       value="<?= !empty($book['published_date']) ? date('Y-m-d', strtotime($book['published_date'])) : '' ?>">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="author">Author</label>
                <input type="text" id="author" name="author" value="<?= htmlspecialchars($book['author']) ?>" />
              </div>
              <div class="form-group" style="flex: 0.5">
                <label for="genre">Genre</label>
                <input type="text" name="genre" value="<?= htmlspecialchars($book['genre']) ?>">
              </div>
            </div>
            <input type="submit" value="<?= $isEditing ? "Update Book" : "Add Book" ?>" 
                   class="<?= $isEditing ? 'btn-edit' : '' ?>"> <?php if($isEditing): ?>
                <a href="admin_manage_books.php" style="text-decoration: none; font-size: 14px; color: #555; margin-left: 10px;">Cancel</a>
            <?php endif; ?>
          </form>
        </section>

        <section class="content-card">
          <h2>Book Inventory</h2>
          <table id="books-table">
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
                <td><?= htmlspecialchars($b['published_date']) ?></td>
                <td><?= htmlspecialchars($b['genre']) ?></td>
                <td>
                    <a href="admin_manage_books.php?edit=<?= $b['id'] ?>" class="btn-action btn-edit" style="text-decoration:none;">Edit</a>
                    
                    <a href="admin_manage_books.php?delete=<?= $b['id'] ?>" 
                       class="btn-action btn-delete" 
                       style="text-decoration:none;">Delete</a>
                </td>
            </tr>
            <?php 
                    } 
                } else {
                    echo "<tr><td colspan='5'>No books found.</td></tr>";
                }
            ?>
            </tbody>
          </table>
        </section>
      </main>
    </div>
    <script src="../../assets/js/logout.js"></script>
    <script src="../../assets/js/delete_confirmation.js"></script>
    <script src="../../assets/js/admin_sidebar_toggle.js"></script>
  </body>
</html>