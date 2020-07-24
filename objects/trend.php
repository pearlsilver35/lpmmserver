<?php
class Trend
{
    
    // database connection and table name
    private $conn;
    private $table_name = "PageInsider";
    
    // object properties
            
        public $ID;
        public $Date_Post;
        public $CustomerID;
        public $DateRated;
        public $Amount;
        public $Present_Score;
        public $AccruedPoint;
        public $New_Score;
        public $Product_Type;
        public $Old_Tier;
        public $New_Tier;
        public $COD_DRCR;
        public $Narration;
  
    // constructor with $db as database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }
    
    function memberschange()
    {
        
        // select all query
        $query = "SELECT
        COUNT(DISTINCT CustomerID) AS 'Total',
        DATE_ADD(DATE(wo.DateRated), INTERVAL(7 - DAYOFWEEK(wo.DateRated)) DAY) WeekEnding
        FROM
            PageInsider wo
        WHERE
            New_Score > 0 
            AND New_Tier = :Tier 
            AND `Old_Tier` != :Tier 
            AND DATE_FORMAT(DATE(DateRated),'%y-%m') = DATE_FORMAT(DATE(:Monthx),'%y-%m')
        GROUP BY
            WeekEnding;";
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        
        $this->Month = htmlspecialchars(strip_tags($this->Month));
        $this->Tier = htmlspecialchars(strip_tags($this->Tier));
        
        $stmt->bindParam(':Monthx', $this->Month);
        $stmt->bindParam(':Tier', $this->Tier);

        global $err1;
        $err1 = $stmt->errorInfo();
        // execute query
        $stmt->execute();
        
        return $stmt;
    }

    function pagesearned()
    {
        
        // select all query
        $query = "SELECT IFNULL(SUM(New_Score),'0') AS 'Total',
        DATE_ADD(DATE(wo.DateRated), INTERVAL(7 - DAYOFWEEK(wo.DateRated)) DAY) WeekEnding
        FROM
            PageInsider wo
        WHERE
            New_Score > 0 
            AND New_Tier = :Tier 
            AND COD_DRCR='C'
            AND DATE_FORMAT(DATE(DateRated),'%y-%m') = DATE_FORMAT(DATE(:Monthx),'%y-%m')
        GROUP BY
            WeekEnding";
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        
        $this->Month = htmlspecialchars(strip_tags($this->Month));
        $this->Tier = htmlspecialchars(strip_tags($this->Tier));
        
        $stmt->bindParam(':Monthx', $this->Month);
        $stmt->bindParam(':Tier', $this->Tier);

        global $err1;
        $err1 = $stmt->errorInfo();
        // execute query
        $stmt->execute();
        
        return $stmt;
    }

    function pagesredeemed()
    {
        
        // select all query
        $query = "SELECT IFNULL(SUM(New_Score),'0') AS 'Total',
        DATE_ADD(DATE(wo.DateRated), INTERVAL(7 - DAYOFWEEK(wo.DateRated)) DAY) WeekEnding
        FROM
            PageInsider wo
        WHERE
            New_Tier = :Tier AND COD_DRCR = 'D' AND DATE_FORMAT(DATE(DateRated), '%y-%m') = DATE_FORMAT(DATE(:Monthx),'%y-%m')
        GROUP BY
            WeekEnding";

        // prepare query statement
        $stmt = $this->conn->prepare($query);
        
        $this->Month = htmlspecialchars(strip_tags($this->Month));
        $this->Tier = htmlspecialchars(strip_tags($this->Tier));
        
        $stmt->bindParam(':Monthx', $this->Month);
        $stmt->bindParam(':Tier', $this->Tier);

        global $err1;
        $err1 = $stmt->errorInfo();
        // execute query
        $stmt->execute();
        
        return $stmt;
    }
   
}

?>