<?php
require_once "database.php";

class BorrowTransaction extends Database {

    // --- Helper: Get Librarian ID from Session Username ---
    public function getLibrarianId($username) {
        $sql = "SELECT l.id FROM librarian l 
                JOIN users u ON l.user_id = u.id 
                WHERE u.user_name = :username";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":username", $username);
        
        if($query->execute()){
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['id'] : null;
        }
        return null;
    }

    // --- ACTION: Issue a Book (Borrow) ---
    public function borrowBook($librarian_id, $borrower_id, $book_id, $return_date) {
        $sql = "INSERT INTO borrow_transaction (librarian_id, borrower_id, book_id, return_date, status) 
                VALUES (:librarian_id, :borrower_id, :book_id, :return_date, 'borrowed')";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":librarian_id", $librarian_id);
        $query->bindParam(":borrower_id", $borrower_id);
        $query->bindParam(":book_id", $book_id);
        $query->bindParam(":return_date", $return_date);

        return $query->execute();
    }

    // --- ACTION: Return a Book (Update Status) ---
    public function returnBook($transaction_id) {
        $sql = "UPDATE borrow_transaction SET status = 'returned' WHERE id = :id";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":id", $transaction_id);
        return $query->execute();
    }

    // --- READ: Get Active Loans (Books not yet returned) ---
    public function getActiveBorrows() {
        $sql = "SELECT 
                    bt.id AS transaction_id,
                    bt.borrowed_date,
                    bt.return_date,
                    b.title,
                    b.author,
                    bor.first_name,
                    bor.last_name,
                    bor.course,
                    bor.year
                FROM borrow_transaction bt
                JOIN books b ON bt.book_id = b.id
                JOIN borrowers bor ON bt.borrower_id = bor.id
                WHERE bt.status = 'borrowed'
                ORDER BY bt.return_date ASC"; 
        
        $query = $this->connect()->prepare($sql);
        
        if($query->execute()){
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }
        return [];
    }

    // --- READ: Get Recent Issued Books (For Dashboard) ---
    public function getRecentIssued() {
        $sql = "SELECT 
                    b.title,
                    bor.first_name,
                    bor.last_name,
                    bt.borrowed_date,
                    bt.return_date
                FROM borrow_transaction bt
                JOIN books b ON bt.book_id = b.id
                JOIN borrowers bor ON bt.borrower_id = bor.id
                WHERE bt.status = 'borrowed'
                ORDER BY bt.borrowed_date DESC
                LIMIT 5";

        $query = $this->connect()->prepare($sql);
        if($query->execute()){
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }
        return [];
    }

    public function getCountBorrowed(){
        $sql = "SELECT 
                    COUNT(*) AS borrowed_count,
                    b.title,
                    bor.first_name,
                    bor.last_name,
                    bt.borrowed_date,
                    bt.return_date
                FROM borrow_transaction bt
                JOIN books b ON bt.book_id = b.id
                JOIN borrowers bor ON bt.borrower_id = bor.id
                WHERE bt.status = 'borrowed'
                ORDER BY bt.borrowed_date DESC";
        $query = $this->connect()->prepare($sql);

        if($query->execute()){
            return $query->fetch(PDO::FETCH_ASSOC);
        }else{
            return null;
        }  
    }

    public function countOverdue(){
        $sql = "SELECT COUNT(*) AS count from borrow_transaction WHERE NOW() > return_date AND status = 'borrowed' ";
        $query = $this->connect()->prepare($sql);

        if($query->execute()){
            return $query->fetch(PDO::FETCH_ASSOC);
        }else{
            return null;
        }
    }

    // --- Helper: Get Lists for Dropdowns ---
    public function getBorrowersList() {
        $sql = "SELECT id, first_name, last_name, course, year FROM borrowers ORDER BY last_name ASC";
        $query = $this->connect()->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBooksList() {
        $sql = "SELECT id, title, author FROM books ORDER BY title ASC";
        $query = $this->connect()->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>