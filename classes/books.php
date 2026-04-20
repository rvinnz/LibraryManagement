<?php 
require_once "database.php";

class Books extends Database {
    public $title = "";
    public $author = "";
    public $published_date = "";
    public $genre = "";

    public function countBooks(){
        $slq = "SELECT COUNT(*) AS count from books";

        $query = $this->connect()->prepare($slq);
        if( $query->execute() ){
            return $query->fetch(PDO::FETCH_ASSOC);
        }else{  
            return null;
        }
    }

    // Create
    public function insertBook(){
        $sql = "INSERT INTO books(title, author, published_date, genre)
                VALUES(:title, :author, :published_date, :genre)";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":title", $this->title);
        $query->bindParam(":author", $this->author);
        $query->bindParam(":published_date", $this->published_date);
        $query->bindParam(":genre", $this->genre);

        if($query->execute()){
            return true;
        }
    }

    // Read (All) - FIXED: Added 'id' to selection so we can edit/delete specific rows
    public function displayBooks(){
        $sql = "SELECT id, title, author, published_date, genre FROM books"; 
        $query = $this->connect()->prepare($sql);

        if( $query->execute() ){
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }else{  
            return null;
        }
    }

    // Read (Single) - NEW: Needed to populate the form when editing
    public function fetchBook($id){
        $sql = "SELECT * FROM books WHERE id = :id";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":id", $id);
        
        if($query->execute()){
            return $query->fetch(PDO::FETCH_ASSOC);
        }
        return null;
    }

    // Update - NEW
    public function updateBook($id){
        $sql = "UPDATE books SET title=:title, author=:author, published_date=:published_date, genre=:genre WHERE id=:id";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":title", $this->title);
        $query->bindParam(":author", $this->author);
        $query->bindParam(":published_date", $this->published_date);
        $query->bindParam(":genre", $this->genre);
        $query->bindParam(":id", $id);

        if($query->execute()){
            return true;
        }
    }

    // Delete - NEW
    public function deleteBook($id){
        $sql = "DELETE FROM books WHERE id = :id";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(":id", $id);
        
        if($query->execute()){
            return true;
        }
    }
}