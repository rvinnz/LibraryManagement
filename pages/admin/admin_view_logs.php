<?php 
require_once "../../classes/logs.php";
session_start();

// Updated Check: Uses is_admin !== 1
if (!isset($_SESSION['username']) || $_SESSION['is_admin'] !== 1) {
    header("Location: ../../login.php");
    exit;
}
$admin_username = $_SESSION['username'];

$logObj = new Logs();
$logs = $logObj->displayLogs();

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-R" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Transaction Logs - Admin Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/adminpage.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="../../assets/icons/favicon.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Add specific styles for status badges */
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-borrowed {
            background-color: #ffeeba;
            color: #856404;
        }
        .status-returned {
            background-color: #d4edda;
            color: #155724;
        }
        .status-overdue {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
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
          <li><a href="admin_manage_books.php">Manage Books</a></li>
          <li><a href="admin_view_logs.php" class="active">View Logs</a></li>
          <li class="logout-link"><a href="../../logout.php">Log Out</a></li>
        </ul>
      </nav>

      <main class="main-content">
        <header class="main-header">
          <h1>Transaction Logs</h1>
        </header>

        <!-- LOGS TABLE -->
        <section class="content-card">
          <h2>Borrow & Return History</h2>
          <table id="librarians-table"> <!-- Re-using generic table style -->
            <thead>
              <tr>
                <th>Date Borrowed</th>
                <th>Book Title</th>
                <th>Borrower Name</th>
                <th>Processed By</th>
                <th>Due / Return Date</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php 
                if($logs) {
                    foreach($logs as $log){
                        // Determine status class
                        $statusClass = '';
                        if($log['status'] == 'borrowed') {
                            $statusClass = 'status-borrowed';
                        } else if ($log['status'] == 'returned') {
                            $statusClass = 'status-returned';
                        }
                        
                        // Format Dates
                        $borrowDate = date('M d, Y h:i A', strtotime($log['borrowed_date']));
                        $returnDate = date('M d, Y', strtotime($log['return_date']));
              ?>
              <tr>
                <td><?= $borrowDate ?></td>
                <td><?= htmlspecialchars($log['book_title']) ?></td>
                <td>
                    <?= htmlspecialchars($log['borrower_name']) ?> <br>
                    <small style="color: #777;"><?= htmlspecialchars($log['course'] . ' - ' . $log['year']) ?></small>
                </td>
                <td><?= htmlspecialchars($log['librarian_name']) ?></td>
                <td><?= $returnDate ?></td>
                <td>
                    <span class="status-badge <?= $statusClass ?>">
                        <?= strtoupper($log['status']) ?>
                    </span>
                </td>
              </tr>
              <?php 
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align:center;'>No transaction records found.</td></tr>";
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