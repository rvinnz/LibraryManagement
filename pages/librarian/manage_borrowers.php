<?php 
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit;
}

require_once "../../classes/borrower.php";

$borrowerObj = new Borrower();

// Initialize variables
$borrower = [
    'id' => '',
    'first_name' => '',
    'middle_name' => '',
    'last_name' => '',
    'course' => '',
    'year' => '',
    'email' => ''
];
$error = [];
$message = "";
$isEditing = false;

// Handle DELETE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($borrowerObj->deleteBorrower($id)) {
        $message = "Borrower removed successfully!";
    } else {
        $error['error'] = "Failed to delete borrower.";
    }
}

// Handle EDIT (Populate Form)
if (isset($_GET['edit'])) {
    $isEditing = true;
    $id = $_GET['edit'];
    $fetchedB = $borrowerObj->fetchBorrower($id);
    if ($fetchedB) {
        $borrower = $fetchedB;
    }
}

// Handle FORM SUBMISSION (Add or Update)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $b_id = $_POST['b_id'] ?? '';
    $first_name = $_POST['first_name'] ?? '';
    $middle_name = $_POST['middle_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $course = $_POST['course'] ?? '';
    $year = $_POST['year'] ?? '';
    $email = $_POST['email'] ?? '';

    // Validation
    if (empty($first_name) || empty($last_name) || empty($course) || empty($year)) {
        $error['all'] = "Name, Course, and Year are required fields.";
    } elseif ($year < 1 || $year > 4) {
        $error['year'] = "Year level must be between 1 and 4.";
    }

    if (empty($error)) {
        try {
            $borrowerObj->first_name = $first_name;
            $borrowerObj->middle_name = $middle_name;
            $borrowerObj->last_name = $last_name;
            $borrowerObj->course = $course;
            $borrowerObj->year = $year;
            $borrowerObj->email = $email;

            if (!empty($b_id)) {
                // --- UPDATE ---
                if($borrowerObj->updateBorrower($b_id)){
                    $message = "Borrower details updated!";
                    $isEditing = false;
                    $borrower = ['id'=>'', 'first_name'=>'', 'middle_name'=>'', 'last_name'=>'', 'course'=>'', 'year'=>'', 'email'=>''];
                }
            } else {
                // --- CREATE ---
                if($borrowerObj->addBorrower()){
                    $message = "New borrower registered!";
                    $borrower = ['id'=>'', 'first_name'=>'', 'middle_name'=>'', 'last_name'=>'', 'course'=>'', 'year'=>'', 'email'=>''];
                }
            }
        } catch (Exception $e) {
            $error['error'] = $e->getMessage();
        }
    }
}

$allBorrowers = $borrowerObj->displayBorrowers();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-R" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>View Borrowers - Librarian Panel</title>
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
          <p>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</p>
        </div>
        <ul class="sidebar-links">
          <li><a href="librarian.php">Dashboard</a></li>
          <li><a href="issue_book.php">Issue Books</a></li>
          <li><a href="return_book.php">Return Books</a></li>
          <li><a href="manage_books.php">Manage Books</a></li>
          <li><a href="manage_borrowers.php" class="active">View Borrowers</a></li>
          <li><a href="view_logs.php">View Logs</a></li>
          <li class="logout-link"><a href="../../logout.php">Log Out</a></li>
        </ul>
      </nav>

      <main class="main-content">
        <header class="main-header">
          <h1>View Borrowers</h1>
        </header>

        <!-- ADD / EDIT FORM -->
        <section class="content-card">
          <h2><?= $isEditing ? "Edit Borrower Details" : "Register New Borrower" ?></h2>
          
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

          <form id="add-borrower-form" method="post">
            
            <input type="hidden" name="b_id" value="<?= $borrower['id'] ?? '' ?>">

            <!-- Row 1 -->
            <div class="form-row col-3">
              <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($borrower['first_name']) ?>" required />
              </div>
              <div class="form-group">
                <label for="middle_name">Middle Name</label>
                <input type="text" id="middle_name" name="middle_name" value="<?= htmlspecialchars($borrower['middle_name']) ?>" />
              </div>
              <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($borrower['last_name']) ?>" required />
              </div>
            </div>

            <!-- Row 2 -->
            <div class="form-row col-3">
              <div class="form-group">
                <label for="course">Course</label>
                <input type="text" id="course" name="course" placeholder="e.g. BSIT" value="<?= htmlspecialchars($borrower['course']) ?>" required />
              </div>
              <div class="form-group">
                <label for="year">Year Level</label>
                <input type="number" id="year" name="year" min="1" max="4" placeholder="1-4" value="<?= htmlspecialchars($borrower['year']) ?>" required />
              </div>
              <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($borrower['email']) ?>" required />
              </div>
            </div>
            
            <button type="submit" class="btn-primary" style="margin-top: 10px;"><?= $isEditing ? "Update Borrower" : "Register Borrower" ?></button>
            <?php if($isEditing): ?>
                <a href="manage_borrowers.php" style="margin-left:15px; text-decoration:none; color:#666; font-size: 14px;">Cancel</a>
            <?php endif; ?>
          </form>
        </section>

        <!-- TABLE -->
        <section class="content-card">
          <h2>Registered Borrowers</h2>
          <table id="recent-books-table">
            <thead>
              <tr>
                <th>Full Name</th>
                <th>Course & Year</th>
                <th>Email</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php 
                if($allBorrowers) {
                    foreach($allBorrowers as $b){
                        $fullName = $b['first_name'] . ' ' . ($b['middle_name'] ? $b['middle_name'][0].'. ' : '') . $b['last_name'];
              ?>
              <tr>
                <td><?= htmlspecialchars($fullName) ?></td>
                <td><?= htmlspecialchars($b['course'] . " - " . $b['year']) ?></td>
                <td><?= htmlspecialchars($b['email']) ?></td>
                <td>
                  <a href="manage_borrowers.php?edit=<?= $b['id'] ?>" class="btn-action btn-edit">Edit</a>
                  
                  <a href="manage_borrowers.php?delete=<?= $b['id'] ?>" 
                     class="btn-action btn-delete btn-delete-swal">Delete</a>
                </td>
              </tr>
              <?php 
                    }
                } else {
                    echo "<tr><td colspan='4' style='text-align:center; padding: 20px;'>No borrowers found.</td></tr>";
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