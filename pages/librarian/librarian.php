<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit;
}

require_once "../../classes/borrow_transaction.php";

// Initialize Transaction Object
$transObj = new BorrowTransaction();

// Fetch Recent Issues
$recentIssued = $transObj->getRecentIssued();
$borrowedBooks = $transObj->getCountBorrowed();
$overdued = $transObj->countOverdue();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-R" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Librarian Dashboard - ZPPSULibrary</title>
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
          <li><a href="librarian.php" class="active">Dashboard</a></li>
          <li><a href="issue_book.php">Issue Books</a></li>
          <li><a href="return_book.php">Return Books</a></li>
          <li><a href="manage_books.php">Manage Books</a></li>
          <li><a href="manage_borrowers.php">View Borrowers</a></li>
          <li class="logout-link"><a href="../../login.php">Log Out</a></li>
        </ul>
      </nav>

      <main class="main-content">
        <header class="main-header">
          <h1>Librarian Dashboard</h1>
        </header>

        <!-- Summary Cards (Static for now, or connect to DashboardStats if preferred) -->
        <section class="dashboard-cards">
          <div class="summary-card">
            <h4>Books on Loan</h4>
            <p class="stat-number"><?= $borrowedBooks["borrowed_count"] ?? "none" ?></p>
            <a href="return_book.php" class="card-link">View All &rarr;</a>
          </div>
          <div class="summary-card">
            <h4>Overdue Books</h4>
            <p class="stat-number"><?= $overdued['count'] ?? "0" ?></p>
            <a href="return_book.php" class="card-link">View Details &rarr;</a>
          </div>
          <div class="summary-card">
            <h4>Total Borrowers</h4>
            <p class="stat-number">-</p>
            <a href="#" class="card-link">View All Borrowers &rarr;</a>
          </div>
          <div class="summary-card">
            <h4>Books to Re-shelf</h4>
            <p class="stat-number">0</p>
            <a href="manage_books.php" class="card-link">View List &rarr;</a>
          </div>
        </section>

        <!-- Quick Actions -->
        <section class="content-card">
          <h2>Quick Actions</h2>
          <div class="quick-actions">
            <a href="issue_book.php">
                <button class="btn-primary">Issue a New Book</button>
            </a>
            <a href="return_book.php">
                <button class="btn-secondary">Process a Return</button>
            </a>
          </div>
        </section>

        <!-- Recently Issued Books (Dynamic Data) -->
        <section class="content-card">
          <h2>Recently Issued Books</h2>
          <table id="recent-books-table">
            <thead>
              <tr>
                <th>Book Title</th>
                <th>Borrower Name</th>
                <th>Date Issued</th>
                <th>Return Date</th>
              </tr>
            </thead>
            <tbody>
              <?php if(!empty($recentIssued)): ?>
                  <?php foreach($recentIssued as $row): ?>
                  <tr>
                    <td>
                        <span style="font-weight: 600; color: #333;"><?= htmlspecialchars($row['title']) ?></span>
                    </td>
                    <td><?= htmlspecialchars($row['first_name'] . " " . $row['last_name']) ?></td>
                    <td><?= date('M d, Y', strtotime($row['borrowed_date'])) ?></td>
                    <td style="color: #800000; font-weight: 500;">
                        <?= date('M d, Y', strtotime($row['return_date'])) ?>
                    </td>
                  </tr>
                  <?php endforeach; ?>
              <?php else: ?>
                  <tr><td colspan="4" style="text-align:center; padding: 15px; color: #777;">No books issued recently.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </section>
      </main>
    </div>
  </body>
</html>