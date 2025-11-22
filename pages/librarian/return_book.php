<?php 
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit;
}

require_once "../../classes/borrow_transaction.php";
$transObj = new BorrowTransaction();
$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['return_id'])) {
    $transaction_id = $_POST['return_id'];
    if ($transObj->returnBook($transaction_id)) {
        $message = "Book returned successfully!";
    } else {
        $error = "Failed to process return.";
    }
}
$activeLoans = $transObj->getActiveBorrows();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-R" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Return Books - Librarian Panel</title>
    <link rel="stylesheet" href="../../assets/css/librarian.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="../../assets/icons/favicon.png">
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
          <li><a href="issue_book.php">Issue Books</a></li> <!-- Working Link -->
          <li><a href="return_book.php" class="active">Return Books</a></li> <!-- Active -->
          <li><a href="manage_books.php">Manage Books</a></li>
          <li><a href="manage_borrowers.php">View Borrowers</a></li>
          <li class="logout-link"><a href="../../login.php">Log Out</a></li>
        </ul>
      </nav>

      <main class="main-content">
        <header class="main-header">
          <h1>Return Books</h1>
        </header>

        <section class="content-card">
          <h2>Active Borrowed Books</h2>
          <?php if($message): ?>
              <p style="color: green; background: #e6fffa; padding: 10px; border-radius: 5px; border: 1px solid green;"><?= $message ?></p>
          <?php endif; ?>
          <?php if($error): ?>
              <p style="color: red; background: #ffe6e6; padding: 10px; border-radius: 5px; border: 1px solid red;"><?= $error ?></p>
          <?php endif; ?>

          <table id="recent-books-table">
            <thead>
              <tr>
                <th>Book Title</th>
                <th>Borrower Name</th>
                <th>Date Borrowed</th>
                <th>Due Date</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($activeLoans) > 0): ?>
                  <?php foreach($activeLoans as $loan): 
                      $borrowerName = $loan['first_name'] . " " . $loan['last_name'];
                      $borrowDate = date('M d, Y', strtotime($loan['borrowed_date']));
                      $dueDate = date('M d, Y', strtotime($loan['return_date']));
                      $isOverdue = strtotime($loan['return_date']) < time();
                      $dueDateStyle = $isOverdue ? "color: red; font-weight: bold;" : "";
                  ?>
                  <tr>
                    <td>
                        <strong><?= htmlspecialchars($loan['title']) ?></strong><br>
                        <small style="color: #777;">by <?= htmlspecialchars($loan['author']) ?></small>
                    </td>
                    <td>
                        <?= htmlspecialchars($borrowerName) ?><br>
                        <small style="color: #777;"><?= htmlspecialchars($loan['course'] . "-" . $loan['year']) ?></small>
                    </td>
                    <td><?= $borrowDate ?></td>
                    <td style="<?= $dueDateStyle ?>">
                        <?= $dueDate ?>
                        <?= $isOverdue ? "<br><small>Overdue</small>" : "" ?>
                    </td>
                    <td>
                        <form method="post" onsubmit="return confirm('Mark this book as returned?');">
                            <input type="hidden" name="return_id" value="<?= $loan['transaction_id'] ?>">
                            <button type="submit" class="btn-secondary" style="padding: 8px 15px; font-size: 13px;">Return</button>
                        </form>
                    </td>
                  </tr>
                  <?php endforeach; ?>
              <?php else: ?>
                  <tr><td colspan="5" style="text-align: center; padding: 20px;">No active borrowed books found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </section>
      </main>
    </div>
  </body>
</html>