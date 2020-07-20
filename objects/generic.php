<?php
class Generic{
 
    // database connection and table name
    private $conn;
 
    // object properties
    public $ID;
    public $Status;
    public $DateCreated;

 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }
    //Generic Read function
    function genericread()
    {
        
        // query to read single record
        $query = "SELECT * FROM ".$this->TableName." WHERE Status != 'Deleted'";
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // execute query
        $stmt->execute();
        return $stmt;
        
        
    }

    function getitemlabel($TableName,$IDField,$IDFieldvalue,$ReturnValue)
    {
       
// $TableName is the table you want to select from 
// $IDField is the colunm of the primary key
// $IDFieldvalue is the primary key ID to filter with 
// $ReturnValue is the New colunm you wan to select

        $table_filter = " where ".$IDField."='".$IDFieldvalue."'";

        $query = "SELECT ".$ReturnValue." FROM ".$TableName.$table_filter;
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // execute query
        $stmt->execute();
        $num = $stmt->rowCount();
    
        // if LabelID exists, assign values to object properties for easy access and use for php sessions
        if ($num > 0) {
            
            // get record details / values
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // return true because LabelID exists in the database
            return $row[$ReturnValue];
            //return true;
        }
        
        // return false if LabelID  does not exist in the database
        return false;
        
        
        
    }
function genericcreate(){
        try{
       
        $this->conn->beginTransaction();
        $datas = $this->datas;
        // query to insert record
        $query = "INSERT INTO " . $this->TableName . " SET ";
        foreach($datas as $key => $value){
            $key=htmlspecialchars(strip_tags($key));
            $value=htmlspecialchars(strip_tags($value));
            
            $query .= $key."='".$value."', ";
        }
        $query = rtrim($query,", ");
        $query .=';';
        
     
        // prepare query
        $stmt = $this->conn->prepare($query);
    
    
        $query2 = "SELECT " . $this->IDField . " FROM " . $this->TableName . " ORDER BY `ID` DESC LIMIT 1";
     
        // prepare query
        $stmt2 = $this->conn->prepare($query2);
    
         
       
      global  $err1;
      global  $InsertedID;
      
       if($stmt->execute() === false){
        $err1 =$stmt->errorInfo();
        throw new PDOException(); 
       } 
       
       if($stmt2->execute() === false){
        $err1 =$stmt2->errorInfo();
        throw new PDOException(); 
       }else{
        $stmt2->setFetchMode(PDO::FETCH_ASSOC);
        $row = $stmt2->fetch();
        $InsertedID = $row[$this->IDField];
       }

      
     //Commit the above Transactions since auto commit was disabled
       $this->conn->commit(); 

        }catch(PDOException $exception){
    //Rollback/undo transaction if any error occur
            $this->conn->rollBack();   
            
            return false;
        }
    
            return true;
    }
//update function
function genericupdate(){
    try{
   
    $this->conn->beginTransaction();
    $datas = $this->datas;
    // query to insert record
    $query = "UPDATE " . $this->TableName . " SET ";
    foreach($datas as $key => $value){
        $key=htmlspecialchars(strip_tags($key));
        $value=htmlspecialchars(strip_tags($value));
        $query .= $key."='".$value."', ";
    }
    $query = rtrim($query,", ");
    $query .=" WHERE ".$this->IDField."='".$this->IDFieldvalue."'";
    $query .=';';
    
    // prepare query
    $stmt = $this->conn->prepare($query);

  global  $err1;
  
   if($stmt->execute() === false){
    $err1 =$stmt->errorInfo();
    throw new PDOException(); 
   } 

  
 //Commit the above Transactions since auto commit was disabled
   $this->conn->commit(); 

    }catch(PDOException $exception){
//Rollback/undo transaction if any error occur
        $this->conn->rollBack();   
        
        return false;
    }

        return true;
}

    // used when filling up the update asset form
function genericreadOne(){
 

    // query to read single record
    $query = "SELECT * FROM " . $this->TableName . " WHERE ".$this->IDField."=:IDFieldvalue AND Status != 'Deleted'";
 
    
    // prepare query statement
    $stmt = $this->conn->prepare( $query );
 
    $this->IDFieldvalue=htmlspecialchars(strip_tags($this->IDFieldvalue));
 
    // bind AccountID of account to be updated
    $stmt->bindParam(':IDFieldvalue', $this->IDFieldvalue);
    // execute query
    $stmt->execute();

    return $stmt;


}

 // used when filling up the delete record form
 function genericrealdelete(){
 
    // query to read single record
    $query = "DELETE FROM " . $this->TableName . " WHERE ".$this->IDField."=:IDFieldvalue";
 
    // prepare query statement
    $stmt = $this->conn->prepare( $query );
    $this->IDFieldvalue=htmlspecialchars(strip_tags($this->IDFieldvalue));
    $stmt->bindParam(':IDFieldvalue', $this->IDFieldvalue);
    // execute query
    $stmt->execute();

    return $stmt;


}
 
function getPWFName()
    {
        
        // query to check if email exists
        $query = "SELECT PWFName FROM `workflowsetup` WHERE Status != 'Deleted' AND RoleID = ? LIMIT 0,1";
        
        // prepare the query
        $stmt = $this->conn->prepare($query);
        
        // sanitize
        $this->RoleID = htmlspecialchars(strip_tags($this->RoleID));
        
        // bind given RoleID value
        $stmt->bindParam(1, $this->RoleID);
        
        // execute the query
        $stmt->execute();
        
        // get number of rows
        $num = $stmt->rowCount();
        
        // if RoleID exists, assign values to object properties for easy access and use for php sessions
        if ($num > 0) {
            
            // get record details / values
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // assign values to object properties
            $this->PWFName = $row['PWFName'];
            
            // return true because RoleID exists in the database
            return $row['PWFName'];
            //return true;
        }
        
        // return false if RoleID does not exist in the database
        return false;
    }
    // delete the asset
    function genericdelete(){
        try{
       
        $this->conn->beginTransaction();
      
        // query to insert record
        $query = "UPDATE
                    " . $this->TableName . "
    
                SET
                 
                   Status='Deleted'
                WHERE
                " . $this->IDField . "=:IDFieldvalue";
     
        // prepare query
        $stmt = $this->conn->prepare($query);
        $this->IDFieldvalue=htmlspecialchars(strip_tags($this->IDFieldvalue));
        $stmt->bindParam(':IDFieldvalue', $this->IDFieldvalue);
        global  $err1;

            if($stmt->execute() === false){
                $err1 =$stmt->errorInfo();
                throw new PDOException(); 
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