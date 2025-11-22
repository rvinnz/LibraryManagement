<?php
require_once "database.php";

class DashboardStats extends Database {
    
    // ... existing counter functions ...
    public function getLibrarianCount() {
        $sql = "SELECT COUNT(*) as total FROM users u JOIN librarian l ON u.id = l.user_id WHERE u.is_admin = 0";
        $query = $this->connect()->prepare($sql);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getBorrowerCount() {
        $sql = "SELECT COUNT(*) as total FROM borrowers";
        $query = $this->connect()->prepare($sql);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getBookCount() {
        $sql = "SELECT COUNT(*) as total FROM books";
        $query = $this->connect()->prepare($sql);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getBorrowedCount() {
        $sql = "SELECT COUNT(*) as total FROM borrow_transaction WHERE status = 'borrowed'";
        $query = $this->connect()->prepare($sql);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // --- NEW: Get Recent Activity (Last 5 Logs) ---
    public function getRecentActivity() {
        $sql = "SELECT 
                    bt.status,
                    bt.borrowed_date,
                    bt.return_date,
                    b.title,
                    CONCAT(bor.first_name, ' ', bor.last_name) AS borrower_name
                FROM borrow_transaction bt
                JOIN books b ON bt.book_id = b.id
                JOIN borrowers bor ON bt.borrower_id = bor.id
                ORDER BY bt.borrowed_date DESC
                LIMIT 5";
        
        $query = $this->connect()->prepare($sql);
        if($query->execute()){
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }
        return [];
    }
}
?>