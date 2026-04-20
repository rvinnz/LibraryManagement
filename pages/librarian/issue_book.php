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

if (isset($_SESSION['success_msg'])) {
    $message = $_SESSION['success_msg'];
    unset($_SESSION['success_msg']);
}
if (isset($_SESSION['error_msg'])) {
    $error = $_SESSION['error_msg'];
    unset($_SESSION['error_msg']);
}

// Handle Form Submission
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
            $_SESSION['success_msg'] = "Book issued successfully!";
            header("Location: issue_book.php");
            exit;
        } else {
            $error = "Failed to issue book. Please try again.";
        }
    }
}

$borrowers = $transObj->getBorrowersList();
$books = $transObj->getBooksList();
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
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Select2 CSS for Searchable Dropdowns -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <style>
        .select2-container .select2-selection--single {
            height: 45px; 
            padding: 8px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            color: #333;
        }
    </style>
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
          <p>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</p>
        </div>
        <ul class="sidebar-links">
          <li><a href="librarian.php">Dashboard</a></li>
          <li><a href="issue_book.php" class="active">Issue Books</a></li>
          <li><a href="return_book.php">Return Books</a></li>
          <li><a href="manage_books.php">Manage Books</a></li>
          <li><a href="manage_borrowers.php">View Borrowers</a></li>
          <li><a href="view_logs.php">View Logs</a></li>
          <li class="logout-link"><a href="../../logout.php">Log Out</a></li>
        </ul>
      </nav>

      <main class="main-content">
        <header class="main-header">
          <h1>Issue a Book</h1>
        </header>

        <section class="content-card">
          <h2>New Transaction</h2>
          
          <?php if($message): ?>
              <script>
                  document.addEventListener('DOMContentLoaded', function() {
                      Swal.fire({
                          icon: 'success',
                          title: 'Success',
                          text: '<?= addslashes($message) ?>',
                          timer: 2000,
                          showConfirmButton: false
                      });
                  });
              </script>
          <?php endif; ?>
          <?php if($error): ?>
              <script>
                  document.addEventListener('DOMContentLoaded', function() {
                      Swal.fire({
                          icon: 'error',
                          title: 'Error',
                          text: '<?= addslashes($error) ?>'
                      });
                  });
              </script>
          <?php endif; ?>

          <form method="post">
            <div class="form-row">
              <div class="form-group" style="flex: 1;">
                <label for="borrower_id">Select Borrower</label>
                <!-- searchable-select -->
                <select name="borrower_id" id="borrower_id" class="searchable-select" required>
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
                <!-- searchable-select -->
                <select name="book_id" id="book_id" class="searchable-select" required>
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
    
    <!-- Scripts -->
    <script src="../../assets/js/logout.js"></script>
    <script src="../../assets/js/admin_sidebar_toggle.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // Initialize Select2 on elements with class 'searchable-select'
        $(document).ready(function() {
            $('#borrower_id').select2({
                width: '100%',
                placeholder: "Choose a Student",
                allowClear: true
            });
            $('#book_id').select2({
                width: '100%', 
                placeholder: "Choose a Book",
                allowClear: true
            });
        });
    </script>
  </body>
</html>