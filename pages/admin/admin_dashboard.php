<?php
require_once "../../classes/dashboard.php";
session_start();

// Updated Check: Uses is_admin !== 1
if (!isset($_SESSION['username']) || $_SESSION['is_admin'] !== 1) {
    header("Location: ../../login.php");
    exit;
}
$admin_username = $_SESSION['username'];

$statsObj = new DashboardStats();

// Fetch real-time data

$librarianCount = $statsObj->getLibrarianCount();
$borrowerCount = $statsObj->getBorrowerCount();
$bookCount = $statsObj->getBookCount();
$activeLoansCount = $statsObj->getBorrowedCount();

// NEW: Fetch recent activity
$recentActivity = $statsObj->getRecentActivity();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-R" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard - ZPPSULibrary</title>
    <link rel="stylesheet" href="../../assets/css/adminpage.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="../../assets/icons/favicon.png">
  </head>
  <body>
    
    <!-- 🌟 ADDED: Hardcoded Button & Overlay -->
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
          <li><a href="admin_dashboard.php" class="active">Dashboard</a></li>
          <li><a href="admin_manage_librarian.php">Manage Librarians</a></li>
          <li><a href="admin_manage_borrowers.php">View Borrowers</a></li>
          <li><a href="admin_manage_books.php">Manage Books</a></li>
          <li><a href="admin_view_logs.php">View Logs</a></li>
          <li class="logout-link"><a href="../../logout.php">Log Out</a></li>
        </ul>
      </nav>

      <main class="main-content">
        <header class="main-header">
          <h1>Dashboard Overview</h1>
        </header>

        <section class="dashboard-cards">
          <div class="summary-card">
            <h4>Total Librarians</h4>
            <p class="stat-number"><?= $librarianCount ?></p>
            <a href="admin_manage_librarian.php" class="card-link">Manage Librarians &rarr;</a>
          </div>
          <div class="summary-card">
            <h4>Total Borrowers</h4>
            <p class="stat-number"><?= $borrowerCount ?></p>
            <a href="admin_manage_borrowers.php" class="card-link">View All Borrowers &rarr;</a>
          </div>
          <div class="summary-card">
            <h4>Transaction</h4>
            <p class="stat-number"><?= $activeLoansCount ?></p>
            <a href="admin_view_logs.php" class="card-link">View borrower transactions &rarr;</a>
          </div>
          <div class="summary-card">
            <h4>Total Books</h4>
            <p class="stat-number"><?= $bookCount ?></p>
            <a href="admin_manage_books.php" class="card-link">Manage Inventory &rarr;</a>
          </div>
        </section>

        <section class="content-card">
          <h2>Recent Activity</h2>
          <?php if (!empty($recentActivity)): ?>
              <ul style="list-style: none; padding: 0;">
                <?php foreach ($recentActivity as $log): ?>
                    <?php 
                        $action = ($log['status'] == 'borrowed') ? "borrowed" : "returned";
                        $color = ($log['status'] == 'borrowed') ? "#b45309" : "#155724"; 
                        $date = date('M d, h:i A', strtotime($log['borrowed_date']));
                    ?>
                    <li style="padding: 10px 0; border-bottom: 1px solid #eee; display: flex; justify-content: space-between;">
                        <span>
                            <strong><?= htmlspecialchars($log['borrower_name']) ?></strong> 
                            <span style="color: <?= $color ?>; font-weight: 600;"><?= $action ?></span> 
                            "<?= htmlspecialchars($log['title']) ?>"
                        </span>
                        <small style="color: #888;"><?= $date ?></small>
                    </li>
                <?php endforeach; ?>
              </ul>
              <div style="margin-top: 15px;">
                <a href="admin_view_logs.php" style="color: #800000; font-weight: 500; text-decoration: none;">View Full Log History &rarr;</a>
              </div>
          <?php else: ?>
              <p style="color: #666; font-style: italic;">No recent activity found.</p>
          <?php endif; ?>
        </section>

      </main>
    </div>
    <script src="../../assets/js/logout.js"></script>
    <script src="../../assets/js/admin_sidebar_toggle.js"></script>
  </body>
</html>