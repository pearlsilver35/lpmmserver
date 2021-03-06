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

    
    function pageinsider()
    {
        
        // select all query
        $query = "SELECT COUNT(DISTINCT CustomerID) AS 'Active members',
        (SELECT COUNT(DISTINCT CustomerID) FROM PageInsider WHERE New_Score > 0 AND New_Tier = 'Bronze') AS 'Bronze members',
        (SELECT COUNT(DISTINCT CustomerID) FROM PageInsider WHERE New_Score > 0 AND New_Tier = 'Silver') AS 'Silver members',
        (SELECT COUNT(DISTINCT CustomerID) FROM PageInsider WHERE New_Score > 0 AND New_Tier = 'Gold') AS 'Gold members',
        (SELECT COUNT(DISTINCT CustomerID) FROM PageInsider WHERE New_Score > 0 AND New_Tier = 'Platinum') AS 'Platinum members',
        (SELECT IFNULL(SUM(New_Score),'0') FROM PageInsider WHERE COD_DRCR = 'C' AND DATE_FORMAT(DATE(DateRated), '%y-%m') = DATE_FORMAT(DATE(:Monthx), '%y-%m')) AS 'Total Pages earned',
        (SELECT IFNULL(SUM(New_Score),'0') FROM PageInsider WHERE COD_DRCR = 'D' AND DATE_FORMAT(DATE(DateRated), '%y-%m') = DATE_FORMAT(DATE(:Monthx), '%y-%m')) AS 'Total Pages redeemed'
        FROM
            PageInsider
        WHERE
            New_Score > 0 AND New_Tier IN('Bronze', 'Silver', 'Gold', 'Platinum')";
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        
        $this->Month = htmlspecialchars(strip_tags($this->Month));
        
        $stmt->bindParam(':Monthx', $this->Month);

        global $err1;
        $err1 = $stmt->errorInfo();
        // execute query
        $stmt->execute();
        
        return $stmt;
    }

    function memberschange()
    {
        
        // select all query
        $query = "SELECT (SELECT Count(Distinct CustomerID) from PageInsider where New_Score>0 and New_Tier='Bronze' and `Old_Tier`!='Bronze' and date_format(date(DateRated),'%y-%m')=date_format(date(:Monthx),'%y-%m')) as 'Bronze',
        (SELECT Count(Distinct CustomerID) from PageInsider where New_Score>0 and New_Tier='Silver' and `Old_Tier`!='Silver' and date_format(date(DateRated),'%y-%m')=date_format(date(:Monthx),'%y-%m')) as 'Silver',
        (SELECT Count(Distinct CustomerID) from PageInsider where New_Score>0 and New_Tier='Gold' and `Old_Tier`!='Gold' and date_format(date(DateRated),'%y-%m')=date_format(date(:Monthx),'%y-%m')) as 'Gold',
        (SELECT Count(Distinct CustomerID) from PageInsider where New_Score>0 and New_Tier='Platinum' and `Old_Tier`!='Platinum' and date_format(date(DateRated),'%y-%m')=date_format(date(:Monthx),'%y-%m')) as 'Platinum'";
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        
        $this->Month = htmlspecialchars(strip_tags($this->Month));
        
        $stmt->bindParam(':Monthx', $this->Month);

        global $err1;
        $err1 = $stmt->errorInfo();
        // execute query
        $stmt->execute();
        
        return $stmt;
    }

    function pagesearned()
    {
        
        // select all query
        $query = "SELECT 
        ifnull((SELECT sum(New_Score) from PageInsider where New_Score>0 and New_Tier='Bronze' and COD_DRCR='C' and date_format(date(DateRated),'%y-%m')=date_format(date(:Monthx),'%y-%m')),0.0) as 'Bronze',
        ifnull((SELECT sum(New_Score) from PageInsider where New_Score>0 and New_Tier='Silver' and COD_DRCR='C' and date_format(date(DateRated),'%y-%m')=date_format(date(:Monthx),'%y-%m')),0.0) as 'Silver',
        ifnull((SELECT sum(New_Score) from PageInsider where New_Score>0 and New_Tier='Gold' and COD_DRCR='C' and date_format(date(DateRated),'%y-%m')=date_format(date(:Monthx),'%y-%m')),0.0) as 'Gold',
        ifnull((SELECT sum(New_Score) from PageInsider where New_Score>0 and New_Tier='Platinum' and COD_DRCR='C' and date_format(date(DateRated),'%y-%m')=date_format(date(:Monthx),'%y-%m')),0.0) as 'Platinum'";
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        
        $this->Month = htmlspecialchars(strip_tags($this->Month));
        
        $stmt->bindParam(':Monthx', $this->Month);

        global $err1;
        $err1 = $stmt->errorInfo();
        // execute query
        $stmt->execute();
        
        return $stmt;
    }

    function pagesredeemed()
    {
        
        // select all query
        $query = "SELECT
                    e.WeekEnding,
                    IFNULL(a.Total,'0') AS Bronze,
                    IFNULL(b.Total,'0') AS Gold,
                    IFNULL(c.Total,'0') AS Silver,
                    IFNULL(d.Total,'0') AS Platinum
                FROM
                    (SELECT SUM(New_Score) AS 'Total', DATE_ADD(DATE(wo.DateRated),INTERVAL(7 - DAYOFWEEK(wo.DateRated)) DAY ) WeekEnding
                    FROM PageInsider wo
                    WHERE COD_DRCR = 'D' AND DATE_FORMAT(DATE(DateRated),'%y-%m') = DATE_FORMAT(DATE(:Monthx),'%y-%m')
                    GROUP BY WeekEnding) e

                LEFT JOIN( SELECT SUM(New_Score) AS 'Total', DATE_ADD(DATE(wo.DateRated), INTERVAL(7 - DAYOFWEEK(wo.DateRated)) DAY ) WeekEnding
                    FROM PageInsider wo
                    WHERE New_Tier = 'Bronze' AND COD_DRCR = 'D' AND DATE_FORMAT(DATE(DateRated),'%y-%m') = DATE_FORMAT(DATE(:Monthx),'%y-%m')
                    GROUP BY WeekEnding ) a
                ON
                    e.WeekEnding = a.WeekEnding

                LEFT JOIN( SELECT SUM(New_Score) AS 'Total', DATE_ADD(DATE(wo.DateRated), INTERVAL(7 - DAYOFWEEK(wo.DateRated)) DAY ) WeekEnding
                    FROM PageInsider wo
                    WHERE New_Tier = 'Gold' AND COD_DRCR = 'D' AND DATE_FORMAT(DATE(DateRated),'%y-%m') = DATE_FORMAT(DATE(:Monthx),'%y-%m')
                    GROUP BY WeekEnding ) b
                ON
                    e.WeekEnding = b.WeekEnding

                LEFT JOIN( SELECT SUM(New_Score) AS 'Total', DATE_ADD(DATE(wo.DateRated), INTERVAL(7 - DAYOFWEEK(wo.DateRated)) DAY ) WeekEnding
                    FROM PageInsider wo
                    WHERE New_Tier = 'Silver' AND COD_DRCR = 'D' AND DATE_FORMAT(DATE(DateRated),'%y-%m') = DATE_FORMAT(DATE(:Monthx),'%y-%m')
                    GROUP BY WeekEnding ) c
                ON
                    e.WeekEnding = c.WeekEnding
                    
                LEFT JOIN( SELECT SUM(New_Score) AS 'Total', DATE_ADD(DATE(wo.DateRated), INTERVAL(7 - DAYOFWEEK(wo.DateRated)) DAY ) WeekEnding
                    FROM PageInsider wo
                    WHERE New_Tier = 'Platinum' AND COD_DRCR = 'D' AND DATE_FORMAT(DATE(DateRated),'%y-%m') = DATE_FORMAT(DATE(:Monthx),'%y-%m')
                    GROUP BY WeekEnding ) d
                ON
                    e.WeekEnding = d.WeekEnding";
        
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        
        $this->Month = htmlspecialchars(strip_tags($this->Month));
        
        $stmt->bindParam(':Monthx', $this->Month);

        global $err1;
        $err1 = $stmt->errorInfo();
        // execute query
        $stmt->execute();
        
        return $stmt;
    }

   
}

?>