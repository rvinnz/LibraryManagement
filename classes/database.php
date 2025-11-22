<?php 
class Database{
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "library_management";

    public function connect(){
        $this->conn = new PDO(
            "mysql:host=$this->servername;dbname=$this->dbname",
            $this->username,
            $this->password
        );
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $this->conn;
    }
}