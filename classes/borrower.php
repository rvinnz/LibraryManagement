<?php
require_once "database.php";

class Borrower extends Database {
    // Properties matching the database table 'borrowers'
    public $first_name = "";
    public $middle_name = "";
    public $last_name = "";
    public $course = "";
    public $year = "";
    public $email = "";

    // --- CREATE: Add a new Borrower ---
    public function addBorrower() {
        $sql = "INSERT INTO borrowers (first_name, middle_name, last_name, course, year, email)
                VALUES (:first_name, :middle_name, :last_name, :course, :year, :email)";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":first_name", $this->first_name);
        $query->bindParam(":middle_name", $this->middle_name);
        $query->bindParam(":last_name", $this->last_name);
        $query->bindParam(":course", $this->course);
        $query->bindParam(":year", $this->year);
        $query->bindParam(":email", $this->email);

        return $query->execute();
    }

    // --- READ: Get all Borrowers ---
    public function displayBorrowers() {
        $sql = "SELECT * FROM borrowers ORDER BY created_at DESC";
        $query = $this->connect()->prepare($sql);

        if($query->execute()){
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }
        return null;
    }

    // --- READ (Single): For editing ---
    public function fetchBorrower($id) {
        $sql = "SELECT * FROM borrowers WHERE id = :id";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":id", $id);
        
        if($query->execute()){
            return $query->fetch(PDO::FETCH_ASSOC);
        }
        return null;
    }

    // --- UPDATE: Edit Borrower details ---
    public function updateBorrower($id) {
        $sql = "UPDATE borrowers SET 
                first_name = :first_name, 
                middle_name = :middle_name, 
                last_name = :last_name, 
                course = :course,
                year = :year,
                email = :email
                WHERE id = :id";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":first_name", $this->first_name);
        $query->bindParam(":middle_name", $this->middle_name);
        $query->bindParam(":last_name", $this->last_name);
        $query->bindParam(":course", $this->course);
        $query->bindParam(":year", $this->year);
        $query->bindParam(":email", $this->email);
        $query->bindParam(":id", $id);

        return $query->execute();
    }

    // --- DELETE: Remove Borrower ---
    public function deleteBorrower($id) {
        $sql = "DELETE FROM borrowers WHERE id = :id";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":id", $id);
        
        return $query->execute();
    }
}
?>