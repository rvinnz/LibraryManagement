<?php
session_start();
// Uses is_admin !== 1
if (!isset($_SESSION['username']) || $_SESSION['is_admin'] !== 1) {
    header("Location: ../../login.php");
    exit;
}
require_once "../../classes/account.php";

$userObj = new User();

// Initialize variables
$librarian = [
    'id' => '',
    'first_name' => '',
    'last_name' => '',
    'user_name' => '',
    'id_number' => '',
    'password' => ''
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
    if ($userObj->deleteLibrarian($id)) {
        $_SESSION['swal_message'] = "Librarian deleted successfully!";
        $_SESSION['swal_type'] = "success";
        header("Location: admin_manage_librarian.php");
        exit;
    } else {
        $error['error'] = "Failed to delete librarian.";
    }
}

// Handle EDIT (Populate Form)
if (isset($_GET['edit'])) {
    $isEditing = true;
    $id = $_GET['edit'];
    $fetchedLib = $userObj->fetchLibrarian($id);
    if ($fetchedLib) {
        $librarian = $fetchedLib;
    }
}

// Handle FORM SUBMISSION (Add or Update)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lib_id = $_POST['lib_id'] ?? '';
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $user_name = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    // Auto-generate ID Number
    $id_number = "LIB-" . rand(1000, 9999); 

    // Validation
    if (empty($first_name) || empty($last_name) || empty($user_name)) {
        $error['all'] = "Name and Username are required!";
    }
    
    // Password required only for new accounts
    if (!$isEditing && empty($password)) {
        $error['password'] = "Password is required for new accounts.";
    }

    if (empty($error)) {
        try {
            $userObj->first_name = $first_name;
            $userObj->last_name = $last_name;
            $userObj->user_name = $user_name;
            $userObj->id_number = $id_number;

            if (!empty($lib_id)) {
                // --- UPDATE ---
                if (empty($password)) {
                    $currentData = $userObj->fetchLibrarian($lib_id);
                    $userObj->password = $currentData['password'];
                } else {
                    $userObj->password = $password;
                }
                
                if($userObj->updateLibrarian($lib_id)){
                    $_SESSION['swal_message'] = "Librarian updated successfully!";
                    $_SESSION['swal_type'] = "success";
                } else {
                    $error['error'] = "Failed to update librarian.";
                }
            } else {
                // --- CREATE ---
                $userObj->password = $password;
                if($userObj->registerLibrarian()){
                    $_SESSION['swal_message'] = "Librarian created successfully!";
                    $_SESSION['swal_type'] = "success";
                } else {
                    $error['error'] = "Failed to create librarian.";
                }
            }

            // Redirect if no errors
            if(empty($error)){
                header("Location: admin_manage_librarian.php");
                exit;
            }

        } catch (Exception $e) {
            $error['error'] = $e->getMessage();
        }
    }
}

// Fetch list for table
$allLibrarians = $userObj->displayLibrarians();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-R" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage Librarians - Admin Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/adminpage.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="../../assets/icons/favicon.png">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  </head>
  <body>
    <div class="sidebar-overlay"></div>
    <button id="sidebarToggleBtn" type="button">&#9776;</button>

    <img src="../../assets/img/Zamboanga_Peninsula_Polytechnic_State_University.png" alt="ZPPSU Logo" class="top-logo" />

    <div class="admin-container">
      <nav class="sidebar">
        <div class="sidebar-header">
          <h3>Admin Panel</h3>
          <p>Welcome, Admin!</p>
        </div>
        <ul class="sidebar-links">
          <li><a href="admin_dashboard.php">Dashboard</a></li>
          <li><a href="admin_manage_librarian.php" class="active">Manage Librarians</a></li>
          <li><a href="admin_manage_borrowers.php">View Borrowers</a></li>
          <li><a href="admin_manage_books.php">Manage Books</a></li>
          <li><a href="admin_view_logs.php">View Logs</a></li>
          <li class="logout-link"><a href="../../logout.php">Log Out</a></li>
        </ul>
      </nav>

      <main class="main-content">
        <header class="main-header">
          <h1>Manage Librarians</h1>
        </header>

        <section class="content-card">
          <h2><?= $isEditing ? "Edit Librarian" : "Add New Librarian" ?></h2>
          
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
          
          <form id="add-librarian-form" method="post">
            
            <input type="hidden" name="lib_id" value="<?= $librarian['id'] ?? '' ?>">

            <div class="form-row">
              <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($librarian['first_name']) ?>" required />
              </div>
              <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($librarian['last_name']) ?>" required />
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($librarian['user_name']) ?>" required />
              </div>
              <div class="form-group">
                <label for="password">Password</label>
                <input type="text" id="password" name="password" value="<?= htmlspecialchars($librarian['password']) ?>" />
              </div>
            </div>
            
            <input type="submit" value="<?= $isEditing ? "Update Librarian" : "Create Account" ?>" class="btn-primary">
            <?php if($isEditing): ?>
                <a href="admin_manage_librarian.php" style="margin-left:10px; text-decoration:none; color:#555;">Cancel</a>
            <?php endif; ?>
          </form>
        </section>

        <section class="content-card">
          <h2>All Librarians</h2>
          <table id="librarians-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Username</th>
                <th>Date Added</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php 
                if($allLibrarians) {
                    foreach($allLibrarians as $lib){
                        $statusColor = ($lib['is_active'] == 1) ? 'green' : 'red';
                        $statusText = ($lib['is_active'] == 1) ? 'Active' : 'Inactive';
              ?>
              <tr>
                <td><?= htmlspecialchars($lib['first_name'] . " " . $lib['last_name']) ?></td>
                <td><?= htmlspecialchars($lib['user_name']) ?></td>
                <td><?= date('Y-m-d', strtotime($lib['created_at'])) ?></td>
                <td>
                    <span style="color: <?= $statusColor ?>; font-weight:600;">
                        <?= $statusText ?>
                    </span>
                </td>
                <td>
                  <a href="admin_manage_librarian.php?edit=<?= $lib['id'] ?>" class="btn-action btn-edit" style="text-decoration:none">Edit</a>
                  
                  <!-- SweetAlert Delete Button -->
                  <a href="admin_manage_librarian.php?delete=<?= $lib['id'] ?>" 
                     class="btn-action btn-delete btn-delete-swal" 
                     style="text-decoration:none">Delete</a>
                </td>
              </tr>
              <?php 
                    }
                } else {
                    echo "<tr><td colspan='5'>No librarians found.</td></tr>";
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