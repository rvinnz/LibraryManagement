<?php 
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit;
}
require_once "../../classes/borrow_transaction.php";

$transObj = new BorrowTransaction();
$borrowers = $transObj->getBorrowersList();
$books = $transObj->getBooksList();

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $borrower_id = $_POST['borrower_id'] ?? '';
    $book_id = $_POST['book_id'] ?? '';
    $return_date = $_POST['return_date'] ?? '';
    $librarian_id = $transObj->getLibrarianId($_SESSION['username']);

    if (empty($borrower_id) || empty($book_id) || empty($return_date)) {
        $error = "Please select a borrower, a book, and a return date.";
    } elseif (!$librarian_id) {
        $error = "Error: Could not identify the librarian. Please relogin.";
    } else {
        if ($transObj->borrowBook($librarian_id, $borrower_id, $book_id, $return_date)) {
            $message = "Book issued successfully!";
        } else {
            $error = "Failed to issue book. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-R" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Issue Book - Librarian Panel</title>
    <link rel="stylesheet" href="../../assets/css/librarian.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="../../assets/icons/favicon.png">
    <style>
        select { width: 100%; padding: 12px; margin-top: 10px; border-radius: 8px; border: 1px solid #ccc; font-size: 15px; font-family: "Poppins", sans-serif; background-color: white; }
    </style>
  </head>
  <body>
    <img src="../../assets/img/Zamboanga_Peninsula_Polytechnic_State_University.png" alt="ZPPSU Logo" class="top-logo" />

    <div class="panel-container">
      <nav class="sidebar">
        <div class="sidebar-header">
          <h3>Librarian Panel</h3>
          <p>User: <?= htmlspecialchars($_SESSION['username']) ?></p>
        </div>
        <ul class="sidebar-links">
          <li><a href="librarian.php">Dashboard</a></li>
          <li><a href="issue_book.php" class="active">Issue Books</a></li> <!-- Active -->
          <li><a href="return_book.php">Return Books</a></li> <!-- Working Link -->
          <li><a href="manage_books.php">Manage Books</a></li>
          <li><a href="manage_borrowers.php">View Borrowers</a></li>
          <li class="logout-link"><a href="../../login.php">Log Out</a></li>
        </ul>
      </nav>

      <main class="main-content">
        <header class="main-header">
          <h1>Issue a Book</h1>
        </header>

        <section class="content-card">
          <h2>New Transaction</h2>
          <?php if($message): ?>
              <p style="color: green; background: #e6fffa; padding: 10px; border-radius: 5px; border: 1px solid green;"><?= $message ?></p>
          <?php endif; ?>
          <?php if($error): ?>
              <p style="color: red; background: #ffe6e6; padding: 10px; border-radius: 5px; border: 1px solid red;"><?= $error ?></p>
          <?php endif; ?>

          <form method="post">
            <div class="form-row" style="display: flex; gap: 20px; flex-wrap: wrap;">
              <div class="form-group" style="flex: 1;">
                <label for="borrower_id">Select Borrower</label>
                <select name="borrower_id" id="borrower_id" required>
                    <option value="" disabled selected>-- Choose Student --</option>
                    <?php foreach($borrowers as $b): ?>
                        <option value="<?= $b['id'] ?>">
                            <?= htmlspecialchars($b['last_name'] . ", " . $b['first_name'] . " (" . $b['course'] . "-" . $b['year'] . ")") ?>
                        </option>
                    <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group" style="flex: 1;">
                <label for="book_id">Select Book</label>
                <select name="book_id" id="book_id" required>
                    <option value="" disabled selected>-- Choose Book --</option>
                    <?php foreach($books as $book): ?>
                        <option value="<?= $book['id'] ?>">
                            <?= htmlspecialchars($book['title'] . " by " . $book['author']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="form-row" style="margin-top: 15px;">
                <div class="form-group">
                    <label for="return_date">Return Date</label>
                    <input type="date" name="return_date" id="return_date" required>
                </div>
            </div>
            <button type="submit" class="btn-primary" style="margin-top: 20px;">Confirm Issue</button>
          </form>
        </section>
      </main>
    </div>
  </body>
</html>