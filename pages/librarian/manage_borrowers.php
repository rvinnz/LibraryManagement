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

// 1. Handle DELETE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($borrowerObj->deleteBorrower($id)) {
        $message = "Borrower removed successfully!";
    } else {
        $error['error'] = "Failed to delete borrower.";
    }
}

// 2. Handle EDIT (Populate Form)
if (isset($_GET['edit'])) {
    $isEditing = true;
    $id = $_GET['edit'];
    $fetchedB = $borrowerObj->fetchBorrower($id);
    if ($fetchedB) {
        $borrower = $fetchedB;
    }
}

// 3. Handle FORM SUBMISSION (Add or Update)
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
    <style>
        /* Force Admin-like layout styles */
        .form-row { display: flex; gap: 20px; margin-bottom: 15px; }
        .form-group { display: flex; flex-direction: column; flex: 1; }
        .form-group label { font-weight: 500; margin-bottom: 8px; font-size: 14px; }
        .form-group input { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc; font-size: 15px; font-family: "Poppins", sans-serif; box-sizing: border-box; }
    </style>
  </head>
  <body>
    <img src="../../assets/img/Zamboanga_Peninsula_Polytechnic_State_University.png" alt="ZPPSU Logo" class="top-logo" />

    <div class="panel-container">
      <!-- SIDEBAR -->
      <nav class="sidebar">
        <div class="sidebar-header">
          <h3>Librarian Panel</h3>
          <p>User: <?= htmlspecialchars($_SESSION['username']) ?></p>
        </div>
        <ul class="sidebar-links">
          <li><a href="librarian.php">Dashboard</a></li>
          <li><a href="issue_book.php">Issue Books</a></li>
          <li><a href="return_book.php">Return Books</a></li>
          <li><a href="manage_books.php">Manage Books</a></li>
          <li><a href="manage_borrowers.php" class="active">View Borrowers</a></li> <!-- Active -->
          <li class="logout-link"><a href="../../login.php">Log Out</a></li>
        </ul>
      </nav>

      <main class="main-content">
        <header class="main-header">
          <h1>View Borrowers</h1>
        </header>

        <!-- ADD / EDIT FORM -->
        <section class="content-card">
          <h2><?= $isEditing ? "Edit Borrower Details" : "Register New Borrower" ?></h2>
          
          <form id="add-borrower-form" method="post">
            <!-- Message Container -->
            <div class="message-container" style="margin-bottom: 20px; margin-top: 5px; min-height: 20px;">
                <p class="error"><?= $error['all'] ?? ""?></p>
                <p class="error"><?= $error['year'] ?? ""?></p>
                <p class="error"><?= $error['error'] ?? ""?></p>
                <p class="message" style="color: green; margin: 0;"><?= $message ?? "" ?></p>
            </div>
            
            <input type="hidden" name="b_id" value="<?= $borrower['id'] ?? '' ?>">

            <!-- Row 1: Names -->
            <div class="form-row">
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

            <!-- Row 2: Academic Info -->
            <div class="form-row">
              <div class="form-group">
                <label for="course">Course</label>
                <input type="text" id="course" name="course" placeholder="e.g. BSIT" value="<?= htmlspecialchars($borrower['course']) ?>" required />
              </div>
              <div class="form-group">
                <label for="year">Year Level</label>
                <input type="number" id="year" name="year" min="1" max="4" placeholder="1-4" value="<?= htmlspecialchars($borrower['year']) ?>" required />
              </div>
              <div class="form-group" style="flex: 1.5;">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($borrower['email']) ?>" required />
              </div>
            </div>
            
            <button type="submit" class="btn-primary" style="margin-top: 10px;"><?= $isEditing ? "Update Borrower" : "Register Borrower" ?></button>
            <?php if($isEditing): ?>
                <a href="view_borrowers.php" style="margin-left:15px; text-decoration:none; color:#666; font-size: 14px;">Cancel</a>
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
                  <a href="manage_borrowers.php?edit=<?= $b['id'] ?>" 
                     class="btn-secondary" 
                     style="padding: 6px 12px; text-decoration: none; font-size: 13px; border: none;">Edit</a>
                  
                  <a href="manage_borrowers.php?delete=<?= $b['id'] ?>" 
                     onclick="return confirm('Are you sure you want to remove this borrower?');" 
                     class="btn-secondary"
                     style="padding: 6px 12px; text-decoration: none; font-size: 13px; color: #c92a2a; background: #fff5f5; border: 1px solid #ffc9c9;">Delete</a>
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
  </body>
</html>