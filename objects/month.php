<?php
class Month
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
    
    
    // //read pastpo
    // function pastporead()
    // {
        
    //     // query to read single record
        
    //     $query = "SELECT * FROM `pastpo` WHERE LoanID=:LoanID ;";
    //     // prepare query statement
    //     $stmt  = $this->conn->prepare($query);
        
    //     $this->LoanID = htmlspecialchars(strip_tags($this->LoanID));
        
    //     // bind LoanID of supplier to be selected
    //     $stmt->bindParam(':LoanID', $this->LoanID);
        
    //     // execute query
    //     $stmt->execute();
        
    //     return $stmt;
        
        
    // }

    
    function pageinsider()
    {
        
        // select all query
        $query = "SELECT COUNT(DISTINCT CustomerID) AS 'Active members',
        (SELECT COUNT(DISTINCT CustomerID) FROM PageInsider WHERE New_Score > 0 AND New_Tier = 'Bronze') AS 'Bronze members',
        (SELECT COUNT(DISTINCT CustomerID) FROM PageInsider WHERE New_Score > 0 AND New_Tier = 'Silver') AS 'Silver members',
        (SELECT COUNT(DISTINCT CustomerID) FROM PageInsider WHERE New_Score > 0 AND New_Tier = 'Gold') AS 'Gold members',
        (SELECT COUNT(DISTINCT CustomerID) FROM PageInsider WHERE New_Score > 0 AND New_Tier = 'Platinum') AS 'Platinum members',
        (SELECT FORMAT(SUM(New_Score), 2) FROM PageInsider WHERE COD_DRCR = 'C' AND DATE_FORMAT(DATE(DateRated), '%y-%m') = DATE_FORMAT(DATE('2020-06-01'), '%y-%m')) AS 'Total Pages earned',
        (SELECT FORMAT(SUM(New_Score), 2) FROM PageInsider WHERE COD_DRCR = 'D' AND DATE_FORMAT(DATE(DateRated), '%y-%m') = DATE_FORMAT(DATE('2020-06-01'), '%y-%m')) AS 'Total Pages redeemed'
        FROM
            PageInsider
        WHERE
            New_Score > 0 AND New_Tier IN('Bronze', 'Silver', 'Gold', 'Platinum')";
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        
        global $err1;
        $err1 = $stmt->errorInfo();
        // execute query
        $stmt->execute();
        
        return $stmt;
    }

    function memberschange()
    {
        
        // select all query
        $query = "SELECT (SELECT Count(Distinct CustomerID) from PageInsider where New_Score>0 and New_Tier='Bronze' and `Old_Tier`!='Bronze' and date_format(date(DateRated),'%y-%m')=date_format(date('2020-06-01'),'%y-%m')) as 'Bronze',
        (SELECT Count(Distinct CustomerID) from PageInsider where New_Score>0 and New_Tier='Silver' and `Old_Tier`!='Silver' and date_format(date(DateRated),'%y-%m')=date_format(date('2020-06-01'),'%y-%m')) as 'Silver',
        (SELECT Count(Distinct CustomerID) from PageInsider where New_Score>0 and New_Tier='Gold' and `Old_Tier`!='Gold' and date_format(date(DateRated),'%y-%m')=date_format(date('2020-06-01'),'%y-%m')) as 'Gold',
        (SELECT Count(Distinct CustomerID) from PageInsider where New_Score>0 and New_Tier='Platinum' and `Old_Tier`!='Platinum' and date_format(date(DateRated),'%y-%m')=date_format(date('2020-06-01'),'%y-%m')) as 'Platinum'";
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        
        global $err1;
        $err1 = $stmt->errorInfo();
        // execute query
        $stmt->execute();
        
        return $stmt;
    }

    function pagesearned()
    {
        
        // select all query
        $query = "SELECT (SELECT sum(New_Score) from PageInsider where New_Score>0 and New_Tier='Bronze' and COD_DRCR='C' and date_format(date(DateRated),'%y-%m')=date_format(date('2020-06-01'),'%y-%m')) as 'Bronze',
        ifnull((SELECT sum(New_Score) from PageInsider where New_Score>0 and New_Tier='Silver' and COD_DRCR='C' and date_format(date(DateRated),'%y-%m')=date_format(date('2020-06-01'),'%y-%m')),0.0) as 'Silver',
        ifnull((SELECT sum(New_Score) from PageInsider where New_Score>0 and New_Tier='Gold' and COD_DRCR='C' and date_format(date(DateRated),'%y-%m')=date_format(date('2020-06-01'),'%y-%m')),0.0) as 'Gold',
        ifnull((SELECT sum(New_Score) from PageInsider where New_Score>0 and New_Tier='Platinum' and COD_DRCR='C' and date_format(date(DateRated),'%y-%m')=date_format(date('2020-06-01'),'%y-%m')),0.0) as 'Platinum'";
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        
        global $err1;
        $err1 = $stmt->errorInfo();
        // execute query
        $stmt->execute();
        
        return $stmt;
    }
   
}

?>