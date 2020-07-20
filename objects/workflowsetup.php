<?php
class WorkflowSetup{
 
    // database connection and table name
    private $conn;
    private $table_name = "workflowsetup";
 
    // object properties
 
    public $ID;
    public $SalarySlipID;
    public $SalaryComponentID;
    public $SalarySlipName;
    public $Description;
    public $Status;
    public $Name;
    public $FieldName;
    public $Type;
    public $Formula;
    public $DateCreated;
    
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }




function readone(){
 
    // query to read single record
    $query = "SELECT * FROM  `salaryslipdetails` WHERE SalarySlipID=:SalarySlipID";
 
    // prepare query statement
    $stmt = $this->conn->prepare( $query );

   $this->SalarySlipID=htmlspecialchars(strip_tags($this->SalarySlipID));


    // bind Month 
    
    $stmt->bindParam(":SalarySlipID" , $this->SalarySlipID);
    
 
    // execute query
    $stmt->execute();
    return $stmt;


}



function create($data){
    try{
   
        $this->conn->beginTransaction();

        $query4 = "DELETE FROM `workflowsetup` WHERE 1";
     
        // prepare query
        $stmt4 = $this->conn->prepare($query4);

        if($stmt4->execute() === false){
            $err1 =$stmt4->errorInfo();
            throw new PDOException(); 
           } 
       
        for ($x = 0; $x <= count($data)-1; $x++) { 
        // query to insert record
        $query = "INSERT INTO
                    `workflowsetup`
               SET
                PostedUser=:PostedUser,  
                PWFName=:PWFName,
                RoleID=:RoleID,
                level=:level,
                Level2=:Level2,
                Status=:Status";
     
        // prepare query
        $stmt = $this->conn->prepare($query);

        
        
       // bind values
     
        global  $err1;
        
        $stmt->bindParam(":PostedUser" , $GLOBALS['POSTEDUSER']);
        $stmt->bindParam(":PWFName" , $data[$x]['PWFName']);
        $stmt->bindParam(":RoleID" , $data[$x]['RoleID']);
        $stmt->bindParam(":Level2" , $data[$x]['Level2']);
        $stmt->bindParam(":level" , $data[$x]['level']);
        $stmt->bindParam(":Status" , $data[$x]['Status']);
                   
                    
            
       if($stmt->execute() === false){
        $err1 =$stmt->errorInfo();
        throw new PDOException(); 
       } 
       
}
       $this->conn->commit();   
        }catch(PDOException $exception){
            $this->conn->rollBack();   
            
           // $err1 = $exception->getMessage();
            return false;
        }
            return true;
}

}
?>