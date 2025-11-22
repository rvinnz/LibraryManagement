<?php 
require_once "database.php";

class Logs extends Database {
    
    // --- READ: Get all transaction logs ---
    public function displayLogs() {
        // We need to join 5 tables here:
        // 1. borrow_transaction (Main table)
        // 2. books (To get book title)
        // 3. borrowers (To get student name)
        // 4. librarian (To get the librarian ID)
        // 5. users (To get the librarian's actual name from the user table)
        
        $sql = "SELECT 
                    bt.id,
                    bt.borrowed_date,
                    bt.return_date,
                    bt.status,
                    b.title AS book_title,
                    CONCAT(bor.first_name, ' ', bor.last_name) AS borrower_name,
                    bor.course,
                    bor.year,
                    CONCAT(u.first_name, ' ', u.last_name) AS librarian_name
                FROM borrow_transaction bt
                JOIN books b ON bt.book_id = b.id
                JOIN borrowers bor ON bt.borrower_id = bor.id
                JOIN librarian l ON bt.librarian_id = l.id
                JOIN users u ON l.user_id = u.id
                ORDER BY bt.borrowed_date DESC";

        $query = $this->connect()->prepare($sql);

        if($query->execute()){
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }
        return null;
    }
}
?>