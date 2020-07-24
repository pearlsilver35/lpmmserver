<?php
class Database{
 
    // specify your own database credentials
    //private $host = 'localhost';
    private $host = '66.70.202.147:3306';
    private $db_name = "lpmm";
    //private $db_name = "livebusinessloan";
    private $username = "root";
    private $password = "KoleraKollege_12!";
    //private $port = 3306;
    public $conn;
    
    // get the database connection
    public function getConnection(){
 
        $this->conn = null;
 
        try{
            $this->conn = new PDO("mysql:host=" . $this->host . "; dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
 
        return $this->conn;
    }
}
?>