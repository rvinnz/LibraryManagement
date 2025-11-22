<?php
require_once "database.php";

class User extends Database{
    // Properties
    public $user_name = "";
    public $first_name = "";
    public $last_name = "";
    public $id_number = ""; 
    public $password = "";
    public $is_active = 1; // Default to active

    // --- CREATE: Register a new Librarian ---
    public function registerLibrarian(){
        $this->conn = $this->connect(); // Connect first
        
        // 1. Insert into users table
        $sql = "INSERT INTO users (user_name, first_name, last_name, id_number, password, is_active) 
                VALUES (:user_name, :first_name, :last_name, :id_number, :password, :is_active)";
        
        // Adjusted to use $query
        $query = $this->conn->prepare($sql);
        $query->bindParam(":user_name", $this->user_name);
        $query->bindParam(":first_name", $this->first_name);
        $query->bindParam(":last_name", $this->last_name);
        $query->bindParam(":id_number", $this->id_number);
        $query->bindParam(":password", $this->password);
        $query->bindParam(":is_active", $this->is_active);

        if($query->execute()){
            // 2. Link to librarian table
            $last_id = $this->conn->lastInsertId();
            $sql_lib = "INSERT INTO librarian (user_id) VALUES (:user_id)";
            
            // Adjusted to use $query
            $query_lib = $this->conn->prepare($sql_lib);
            $query_lib->bindParam(":user_id", $last_id);
            return $query_lib->execute();
        }
        return false;
    }

    // --- READ: Get all librarians ---
    // --- READ: Get all librarians ---
    public function displayLibrarians(){
        // Added: WHERE u.is_admin = 0 to hide admins
        $sql = "SELECT u.id, u.first_name, u.last_name, u.user_name, u.id_number, u.is_active, u.created_at 
                FROM users u 
                JOIN librarian l ON u.id = l.user_id 
                WHERE u.is_admin = 0 
                ORDER BY u.created_at DESC";
                
        $query = $this->connect()->prepare($sql);

        if($query->execute()){
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }
        return null;
    }

    // --- READ (Single): For editing ---
    public function fetchLibrarian($id){
        $sql = "SELECT * FROM users WHERE id = :id";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":id", $id);
        
        if($query->execute()){
            return $query->fetch(PDO::FETCH_ASSOC);
        }
        return null;
    }

    // --- UPDATE: Edit librarian details ---
    public function updateLibrarian($id){
        $sql = "UPDATE users SET 
                first_name = :first_name, 
                last_name = :last_name, 
                user_name = :user_name, 
                id_number = :id_number,
                password = :password
                WHERE id = :id";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":first_name", $this->first_name);
        $query->bindParam(":last_name", $this->last_name);
        $query->bindParam(":user_name", $this->user_name);
        $query->bindParam(":id_number", $this->id_number);
        $query->bindParam(":password", $this->password);
        $query->bindParam(":id", $id);

        return $query->execute();
    }

    // --- DELETE: Remove librarian ---
    public function deleteLibrarian($id){
        // Deleting from users will cascade to librarian table automatically
        $sql = "DELETE FROM users WHERE id = :id";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":id", $id);
        
        return $query->execute();
    }

    // Login Logic
    public function login($user_name, $password) {
        $sql = "SELECT user_name, password, is_admin FROM users WHERE user_name = :user_name AND password = :password LIMIT 1";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":user_name", $user_name);
        $query->bindParam(":password", $password);

        if($query->execute()) {
            return $query->fetch(PDO::FETCH_ASSOC);
        }
        return null;
    }
}
?>