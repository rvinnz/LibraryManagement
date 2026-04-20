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
        // Insert the transaction record
        $sql = "INSERT INTO borrow_transaction (librarian_id, borrower_id, book_id, return_date, status) 
                VALUES (:librarian_id, :borrower_id, :book_id, :return_date, 'borrowed')";
        
        // Update the book status to unavailable (0)
        $sqlBook = "UPDATE `books` SET `is_available` = '0' WHERE `books`.`id` = :book_id";

        $conn = $this->connect(); // Get connection instance to use for both queries
        
        $query = $conn->prepare($sql);
        $query->bindParam(":librarian_id", $librarian_id);
        $query->bindParam(":borrower_id", $borrower_id);
        $query->bindParam(":book_id", $book_id);
        $query->bindParam(":return_date", $return_date);

        $queryBook = $conn->prepare($sqlBook);
        $queryBook->bindParam(":book_id", $book_id);

        // Execute both queries
        if($query->execute() && $queryBook->execute()) {
            return true;
        }
        return false;
    }

    // --- ACTION: Return a Book (Update Status & Make Available) ---
    public function returnBook($transaction_id) {
        // First, find the book_id associated with this transaction
        $sqlGetBook = "SELECT book_id FROM borrow_transaction WHERE id = :id";
        $queryGetBook = $this->connect()->prepare($sqlGetBook);
        $queryGetBook->bindParam(":id", $transaction_id);
        
        if($queryGetBook->execute()) {
            $result = $queryGetBook->fetch(PDO::FETCH_ASSOC);
            if($result) {
                $book_id = $result['book_id'];

                // Update the transaction status to 'returned'
                $sql = "UPDATE borrow_transaction SET status = 'returned' WHERE id = :id";
                $query = $this->connect()->prepare($sql);
                $query->bindParam(":id", $transaction_id);
                
                // Update the book's availability to '1' (True)
                $sqlBook = "UPDATE books SET is_available = 1 WHERE id = :book_id";
                $queryBook = $this->connect()->prepare($sqlBook);
                $queryBook->bindParam(":book_id", $book_id);

                // Execute both updates
                if($query->execute() && $queryBook->execute()) {
                    return true;
                }
            }
        }
        return false;
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
                    bor.year,
                    b.id AS book_id
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
                    COUNT(*) AS borrowed_count
                FROM borrow_transaction bt
                WHERE bt.status = 'borrowed'";
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

    // --- FIXED: Only get books that are AVAILABLE (is_available = 1) ---
    public function getBooksList() {
        $sql = "SELECT id, title, author FROM books WHERE is_available = 1 ORDER BY title ASC";
        $query = $this->connect()->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>