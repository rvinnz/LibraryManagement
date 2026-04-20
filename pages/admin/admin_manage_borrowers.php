<?php
session_start();
// Uses is_admin !== 1
if (!isset($_SESSION['username']) || $_SESSION['is_admin'] !== 1) {
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
$isEditing = false;

$swal_message = "";
$swal_type = "";

if (isset($_SESSION['swal_message'])) {
    $swal_message = $_SESSION['swal_message'];
    $swal_type = $_SESSION['swal_type']; // 'success' or 'error'
    unset($_SESSION['swal_message']);
    unset($_SESSION['swal_type']);
}

// Handle DELETE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($borrowerObj->deleteBorrower($id)) {
        $_SESSION['swal_message'] = "Borrower record deleted successfully!";
        $_SESSION['swal_type'] = "success";
        header("Location: admin_manage_borrowers.php");
        exit;
    } else {
        $error['error'] = "Failed to delete borrower.";
    }
}

// Handle EDIT
if (isset($_GET['edit'])) {
    $isEditing = true;
    $id = $_GET['edit'];
    $fetchedB = $borrowerObj->fetchBorrower($id);
    if ($fetchedB) {
        $borrower = $fetchedB;
    }
}

// Handle FORM SUBMISSION
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $b_id = $_POST['b_id'] ?? '';
    
    $first_name = $_POST['first_name'] ?? '';
    $middle_name = $_POST['middle_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $course = $_POST['course'] ?? '';
    $year = $_POST['year'] ?? '';
    $email = $_POST['email'] ?? '';

    if (empty($first_name) || empty($last_name) || empty($course) || empty($year)) {
        $error['all'] = "Name, Course, and Year are required fields.";
    } 
    elseif ($year < 1 || $year > 4) {
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
                    $_SESSION['swal_message'] = "Borrower updated successfully!";
                    $_SESSION['swal_type'] = "success";
                } else {
                    $error['error'] = "Failed to update borrower.";
                }
            } else {
                // --- CREATE ---
                if($borrowerObj->addBorrower()){
                    $_SESSION['swal_message'] = "Borrower added successfully!";
                    $_SESSION['swal_type'] = "success";
                } else {
                    $error['error'] = "Failed to add borrower.";
                }
            }

            // Redirect if no errors
            if(empty($error)){
                header("Location: admin_manage_borrowers.php");
                exit;
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
    <title>Manage Borrowers - Admin Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/adminpage.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="../../assets/icons/favicon.png">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  </head>
  <body>
    <!-- Hardcoded Button & Overlay -->
    <div class="sidebar-overlay"></div>
    <button id="sidebarToggleBtn" type="button">&#9776;</button>

    <img src="../../assets/img/Zamboanga_Peninsula_Polytechnic_State_University.png" alt="ZPPSU Logo" class="top-logo" />

    <div class="admin-container">
      <nav class="sidebar">
        <div class="sidebar-header">
          <h3>Admin Panel</h3>
          <p>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</p>
        </div>
        <ul class="sidebar-links">
          <li><a href="admin_dashboard.php">Dashboard</a></li>
          <li><a href="admin_manage_librarian.php">Manage Librarians</a></li>
          <li><a href="admin_manage_borrowers.php" class="active">View Borrowers</a></li>
          <li><a href="admin_manage_books.php">Manage Books</a></li>
          <li><a href="admin_view_logs.php">View Logs</a></li>
          <li class="logout-link"><a href="../../logout.php">Log Out</a></li>
        </ul>
      </nav>

      <main class="main-content">
        <header class="main-header">
          <h1>Manage Borrowers</h1>
        </header>

        <!-- ADD / EDIT FORM -->
        <section class="content-card">
          <h2><?= $isEditing ? "Edit Borrower Details" : "Add New Borrower" ?></h2>
          
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

          <form id="add-borrower-form" method="post">
            
            <input type="hidden" name="b_id" value="<?= $borrower['id'] ?? '' ?>">

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
            
            <input type="submit" value="<?= $isEditing ? "Update Borrower" : "Add Borrower" ?>" class="btn-primary">
            <?php if($isEditing): ?>
                <a href="admin_manage_borrowers.php" style="margin-left:10px; text-decoration:none; color:#555;">Cancel</a>
            <?php endif; ?>
          </form>
        </section>

        <!-- TABLE -->
        <section class="content-card">
          <h2>Registered Borrowers</h2>
          <table id="librarians-table">
            <thead>
              <tr>
                <th>Full Name</th>
                <th>Course & Year</th>
                <th>Email</th>
                <th style="min-width: 160px;">Actions</th>
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
                  <a href="admin_manage_borrowers.php?edit=<?= $b['id'] ?>" class="btn-action btn-edit">Edit</a>
                  
                  <!-- SweetAlert Delete Button -->
                  <a href="admin_manage_borrowers.php?delete=<?= $b['id'] ?>" 
                     class="btn-action btn-delete btn-delete-swal" 
                     style="text-decoration:none">Delete</a>
                </td>
              </tr>
              <?php 
                    }
                } else {
                    echo "<tr><td colspan='4'>No borrowers found.</td></tr>";
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