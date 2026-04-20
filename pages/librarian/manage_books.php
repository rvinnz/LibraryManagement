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
$isEditing = false;

$swal_message = "";
$swal_type = "";

if (isset($_SESSION['swal_message'])) {
    $swal_message = $_SESSION['swal_message'];
    $swal_type = $_SESSION['swal_type'];
    unset($_SESSION['swal_message']);
    unset($_SESSION['swal_type']);
}

// Handle DELETE Request (GET)
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if($bookObj->deleteBook($id)){
        $_SESSION['swal_message'] = "Book deleted successfully!";
        $_SESSION['swal_type'] = "success";
        header("Location: manage_books.php");
        exit;
    } else {
        $error['error'] = "Failed to delete book.";
    }
}

// Handle EDIT Request (GET)
if (isset($_GET['edit'])) {
    $isEditing = true;
    $id = $_GET['edit'];
    $fetchedBook = $bookObj->fetchBook($id);
    if ($fetchedBook) {
        $book = $fetchedBook;
    }
}

// Handle FORM SUBMISSION (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book["title"] = $_POST["title"] ?? "";
    $book["author"] = $_POST["author"] ?? "";
    $book["published_date"] = $_POST["published"] ?? "";
    $book["genre"] = $_POST["genre"] ?? "";
    $book_id = $_POST["book_id"] ?? "";

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
                if($bookObj->updateBook($book_id)){
                    $_SESSION['swal_message'] = "Book updated successfully!";
                    $_SESSION['swal_type'] = "success";
                } else {
                     $error["error"] = "Failed to update book.";
                }
            } else {
                // --- ADD NEW ---
                if($bookObj->insertBook()){
                    $_SESSION['swal_message'] = "New book added to inventory!";
                    $_SESSION['swal_type'] = "success";
                } else {
                    $error["error"] = "Failed to add book.";
                }
            }

            if(empty($error)){
                header("Location: manage_books.php");
                exit;
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
    
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  </head>
  <body>
    <!-- Sidebar Elements -->
    <div class="sidebar-overlay"></div>
    <button id="sidebarToggleBtn" type="button">&#9776;</button>

    <img src="../../assets/img/Zamboanga_Peninsula_Polytechnic_State_University.png" alt="ZPPSU Logo" class="top-logo" />

    <div class="panel-container">
      <nav class="sidebar">
        <div class="sidebar-header">
          <h3>Librarian Panel</h3>
          <p>User: <?= htmlspecialchars($_SESSION['username']) ?></p>
        </div>
        <ul class="sidebar-links">
          <li><a href="librarian.php">Dashboard</a></li>
          <li><a href="issue_book.php">Issue Books</a></li>
          <li><a href="return_book.php">Return Books</a></li>
          <li><a href="manage_books.php" class="active">Manage Books</a></li> 
          <li><a href="manage_borrowers.php">View Borrowers</a></li>
          <li><a href="view_logs.php">View Logs</a></li>
          <li class="logout-link"><a href="../../logout.php">Log Out</a></li>
        </ul>
      </nav>

      <main class="main-content">
        <header class="main-header">
          <h1>Manage Books</h1>
        </header>

        <!-- ADD / EDIT FORM -->
        <section class="content-card">
          <h2><?= $isEditing ? "Edit Book Details" : "Add New Book" ?></h2>
          
          <!-- PHP Error Display (Fallback) -->
          <?php if(!empty($error)): ?>
             <script>
                  document.addEventListener('DOMContentLoaded', function() {
                      let errorMsg = '<?= addslashes(implode(" ", $error)) ?>';
                      Swal.fire({
                          icon: 'error',
                          title: 'Error',
                          text: errorMsg
                      });
                  });
              </script>
          <?php endif; ?>

          <!-- SweetAlert Trigger Script -->
          <?php if(!empty($swal_message)): ?>
              <script>
                  document.addEventListener('DOMContentLoaded', function() {
                      Swal.fire({
                          icon: '<?= $swal_type ?>',
                          title: '<?= $swal_type === "success" ? "Success!" : "Error!" ?>',
                          text: '<?= addslashes($swal_message) ?>',
                          timer: 2000,
                          showConfirmButton: false
                      });
                  });
              </script>
          <?php endif; ?>

          <form id="add-book-form" method="post">
            
            <input type="hidden" name="book_id" value="<?= $book['id'] ?? '' ?>">

            <!-- Row 1 -->
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

            <!-- Row 2 -->
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
                    <a href="manage_books.php?edit=<?= $b['id'] ?>" class="btn-action btn-edit">Edit</a>
                    
                    <a href="manage_books.php?delete=<?= $b['id'] ?>" 
                       class="btn-action btn-delete btn-delete-swal">Delete</a>
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
    <script src="../../assets/js/logout.js"></script>
    <script src="../../assets/js/delete_confirmation.js"></script>
    <script src="../../assets/js/admin_sidebar_toggle.js"></script>
  </body>
</html>