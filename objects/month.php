<?php
class Loan
{
    
    // database connection and table name
    private $conn;
    private $table_name = "loan";
    
    // object properties
    
    public $ID;
    public $LoanID;
    public $accountID;
    public $LoanName;
    public $LeaveDays;
    public $Status;
    public $Name;
    public $FieldName;
    public $Type;
    public $Formula;
    public $DateCreated;
    
    // constructor with $db as database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }
    
    
    //read pastpo
    function pastporead()
    {
        
        // query to read single record
        
        $query = "SELECT * FROM `pastpo` WHERE LoanID=:LoanID ;";
        // prepare query statement
        $stmt  = $this->conn->prepare($query);
        
        $this->LoanID = htmlspecialchars(strip_tags($this->LoanID));
        
        // bind LoanID of supplier to be selected
        $stmt->bindParam(':LoanID', $this->LoanID);
        
        // execute query
        $stmt->execute();
        
        return $stmt;
        
        
    }
    
    //read account
    function accountread()
    {
        
        // query to read single record
        
        $query = "SELECT * FROM `account` WHERE LoanID=:LoanID ;";
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        
        $this->LoanID = htmlspecialchars(strip_tags($this->LoanID));
        
        // bind LoanID of supplier to be selected
        $stmt->bindParam(':LoanID', $this->LoanID);
        
        // execute query
        $stmt->execute();
        
        return $stmt;
        
        
    }
    // read suppliers
    
    
    function read()
    {
        
        // select all query
        $query = "SELECT

        loan.*,
        company.CompanyName
    
        FROM
            `loan`
        INNER JOIN company ON company.CompanyID = loan.CompanyID WHERE loan.Status != 'Deleted'";
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        
        global $err1;
        $err1 = $stmt->errorInfo();
        // execute query
        $stmt->execute();
        
        return $stmt;
    }
    
    function unprocessedloan()
    {
        
        // select all query
        $query = "SELECT

    loan.*,
    company.CompanyName

    FROM
        `loan`
    INNER JOIN company ON company.CompanyID = loan.CompanyID WHERE loan.Status = 'Deleted' AND loan.Initiator =:Initiatore AND loan.Level = 1 ";
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":Initiatore", $GLOBALS['USER']);
        
        global $err1;
        $err1 = $stmt->errorInfo();
        // execute query
        $stmt->execute();
        
        return $stmt;
    }
    
    function create($data)
    {
        try {
            
            $this->conn->beginTransaction();
            
            // query to insert record
            $query = "INSERT INTO
                " . $this->table_name . "

           SET

                PostedUser=:PostedUser,
                CompanyID=:CompanyID, 
                LoanRequestAmount=:LoanRequestAmount, 
                LoanType=:LoanType, 
                Po=:Po, 
                PFI=:PFI,
                Status=:Status,
                ProfitabilityAnalysis=:ProfitabilityAnalysis,
                LoanPurpose=:LoanPurpose, 
                ProposedCollateral=:ProposedCollateral, 
                ProposedTenor=:ProposedTenor, 
                ExceptionalApproval=:ExceptionalApproval,
                Level= '1',
                CreditReport=:CreditReport,
                BadCreditReport=:BadCreditReport,
                BadCreditReportFile=:BadCreditReportFile,
                Initiator=:Initiator, 
                ProposedSource=:ProposedSource";
            
            // prepare query
            $stmt = $this->conn->prepare($query);
            
            
            $query2 = "SELECT LoanID FROM " . $this->table_name . " ORDER BY `ID` DESC LIMIT 1";
            
            // prepare query
            $stmt2 = $this->conn->prepare($query2);
            
            
            $query3 = "INSERT INTO `account` 
    
   SET
            PostedUser=:PostedUser,
            LoanID=:LoanID, 
            UploadStatementPDF=:UploadStatementPDF, 
            BankName=:BankName,
            Password=:Password, 
            AccountNo=:AccountNo, 
            UploadStatement=:UploadStatement";
            
            // prepare query
            $stmt3 = $this->conn->prepare($query3);
            
            $query33 = "INSERT INTO `pastpo` 
    
   SET
            PostedUser=:PostedUser,
            LoanID=:LoanID, 
            File=:File, 
            Date=:Date";
            
            // prepare query
            $stmt33 = $this->conn->prepare($query33);
            
            $query4 = "INSERT INTO
                    `timeline`
               SET
                PostedUser=:PostedUser,  
                LoanID=:LoanID, 
                RoleID=:RoleID,
                Typed='Loan', 
                PWFName=:PWFName, 
                Note='Loan Process Initailiazation', 
                Status= 'Generated'";
            
            // prepare query
            $stmt4 = $this->conn->prepare($query4);
            
            array(
                $data
            );
            
            // bind values
            
            
            global $err1;
            global $InsertedLoanID;
            
            
            $stmt->bindParam(":PostedUser", $data['PostedUser']);
            $stmt->bindParam(":Initiator", $data['Initiator']);
            $stmt->bindParam(":CreditReport", $data['CreditReport']);
            $stmt->bindParam(":BadCreditReport", $data['BadCreditReport']);
            $stmt->bindParam(":BadCreditReportFile", $data['BadCreditReportFile']);
            $stmt->bindParam(":CompanyID", $data['CompanyID']);
            $stmt->bindParam(":LoanRequestAmount", $data['LoanRequestAmount']);
            $stmt->bindParam(":LoanType", $data['LoanType']);
            $stmt->bindParam(":Po", $data['Po']);
            $stmt->bindParam(":PFI", $data['PFI']);
            $stmt->bindParam(":Status", $data['Status']);
            $stmt->bindParam(":LoanPurpose", $data['LoanPurpose']);
            $stmt->bindParam(":ProposedCollateral", $data['ProposedCollateral']);
            $stmt->bindParam(":ProposedTenor", $data['ProposedTenor']);
            $stmt->bindParam(":ExceptionalApproval", $data['ExceptionalApproval']);
            $stmt->bindParam(":ProposedSource", $data['ProposedSource']);
            $stmt->bindParam(":ProfitabilityAnalysis", $data['ProfitabilityAnalysis']);
            $stmt4->bindParam(":PostedUser", $GLOBALS['POSTEDUSER']);
            $stmt4->bindParam(":LoanID", $InsertedLoanID);
            $stmt4->bindParam(":RoleID", $GLOBALS['ROLEID']);
            $stmt4->bindParam(":PWFName", $data['PWFName']);
            
            
            if ($stmt->execute() === false) {
                $err1 = $stmt->errorInfo();
                throw new PDOException();
            }
            
            if ($stmt2->execute() === false) {
                $err1 = $stmt2->errorInfo();
                throw new PDOException();
            } else {
                $stmt2->setFetchMode(PDO::FETCH_ASSOC);
                $row            = $stmt2->fetch();
                $InsertedLoanID = $row['LoanID'];
                
                
                
                for ($x = 0; $x <= count($data['account']) - 1; $x++) {
                    
                    $stmt3->bindParam(":PostedUser", $data['PostedUser']);
                    $stmt3->bindParam(":LoanID", $InsertedLoanID);
                    $stmt3->bindParam(":UploadStatementPDF", $data['account'][$x]['UploadStatementPDF']);
                    $stmt3->bindParam(":BankName", $data['account'][$x]['BankName']);
                    $stmt3->bindParam(":AccountNo", $data['account'][$x]['AccountNo']);
                    $stmt3->bindParam(":Password", $data['account'][$x]['Password']);
                    $stmt3->bindParam(":UploadStatement", $data['account'][$x]['UploadStatement']);
                    
                    //insert
                    if ($stmt3->execute() === false) {
                        $err1 = $stmt3->errorInfo();
                        throw new PDOException();
                    }
                }
                if ($data['pastpo'] != "") {
                    for ($t = 0; $t <= count($data['pastpo']) - 1; $t++) {
                        
                        $stmt33->bindParam(":PostedUser", $data['PostedUser']);
                        $stmt33->bindParam(":LoanID", $InsertedLoanID);
                        $stmt33->bindParam(":File", $data['pastpo'][$t]['File']);
                        $stmt33->bindParam(":Date", $data['pastpo'][$t]['Date']);
                        
                        //insert
                        if ($stmt33->execute() === false) {
                            $err1 = $stmt33->errorInfo();
                            throw new PDOException();
                        }
                    }
                }
                if ($stmt4->execute() === false) {
                    $err1 = $stmt4->errorInfo();
                    throw new PDOException();
                }
                
            }
            
            $this->conn->commit();
        }
        catch (PDOException $exception) {
            $this->conn->rollBack();
            
            // $err1 = $exception->getMessage();
            return false;
        }
        
        return true;
    }
    
    function readOne()
    {
        
        // query to read single record
        $query = "SELECT * FROM " . $this->table_name . "  WHERE LoanID = :LoanID";
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        
        $this->LoanID = htmlspecialchars(strip_tags($this->LoanID));
        
        // bind LoanID of Customer to be updated
        $stmt->bindParam(":LoanID", $this->LoanID);
        
        
        // execute query
        $stmt->execute();
        return $stmt;
        
        
    }
    function update($data)
    {
        try {
            
            $this->conn->beginTransaction();
            
            // query to insert record
            $query = "UPDATE
                    " . $this->table_name . "
    
               SET
    
                  
               PostedUser=:PostedUser,
               CompanyID=:CompanyID, 
               LoanRequestAmount=:LoanRequestAmount, 
               LoanType=:LoanType, 
               Po=:Po, 
               PFI=:PFI,
               LoanPurpose=:LoanPurpose, 
               ProposedCollateral=:ProposedCollateral, 
               ProposedTenor=:ProposedTenor,
               ExceptionalApproval=:ExceptionalApproval, 
               ProposedSource=:ProposedSource,
               ProfitabilityAnalysis=:ProfitabilityAnalysis,
               CreditReport=:CreditReport,
               BadCreditReport=:BadCreditReport,
               BadCreditReportFile=:BadCreditReportFile,
               ApprovalComment=:ApprovalComment,
               ApprovalFileUpload=:ApprovalFileUpload,
               ApprovalCommentRisk=:ApprovalCommentRisk,
               ApprovalFileUploadRisk=:ApprovalFileUploadRisk
                  
                   WHERE LoanID = :LoanID";
            
            // prepare query
            $stmt = $this->conn->prepare($query);
            
            
            $query2 = "DELETE FROM  `account` WHERE LoanID = :LoanID ";
            
            // prepare query
            $stmt2 = $this->conn->prepare($query2);
            
            $query22 = "DELETE FROM  `pastpo` WHERE LoanID = :LoanID ";
            
            // prepare query
            $stmt22 = $this->conn->prepare($query22);
            
            
            $query3 = "INSERT INTO `account` 
        
       SET
            PostedUser=:PostedUser,
            LoanID=:LoanID, 
            UploadStatementPDF=:UploadStatementPDF, 
            BankName=:BankName, 
            AccountNo=:AccountNo,
            Password=:Password, 
            UploadStatement=:UploadStatement";
            
            // prepare query
            $stmt3 = $this->conn->prepare($query3);
            
            $query33 = "INSERT INTO `pastpo` 
    
   SET
            PostedUser=:PostedUser,
            LoanID=:LoanID, 
            File=:File, 
            Date=:Date";
            
            // prepare query
            $stmt33 = $this->conn->prepare($query33);
            
            
            $query4 = "INSERT INTO
                    `timeline`
            SET
                PostedUser=:PostedUser,  
                LoanID=:LoanID, 
                RoleID=:RoleID,
                Typed='Loan', 
                PWFName=:PWFName, 
                Note='Update operation', 
                Status= 'Updated'";
            
            // prepare query
            $stmt4 = $this->conn->prepare($query4);
            
            array(
                $data
            );
            
            // bind values
            
            
            global $err1;
            global $InsertedLoanID;
            
            $stmt->bindParam(":CreditReport", $data['CreditReport']);
            $stmt->bindParam(":BadCreditReport", $data['BadCreditReport']);
            $stmt->bindParam(":BadCreditReportFile", $data['BadCreditReportFile']);
            $stmt->bindParam(":PostedUser", $data['PostedUser']);
            $stmt->bindParam(":CompanyID", $data['CompanyID']);
            $stmt->bindParam(":LoanRequestAmount", $data['LoanRequestAmount']);
            $stmt->bindParam(":LoanType", $data['LoanType']);
            $stmt->bindParam(":Po", $data['Po']);
            $stmt->bindParam(":PFI", $data['PFI']);
            $stmt->bindParam(":LoanPurpose", $data['LoanPurpose']);
            $stmt->bindParam(":ProposedCollateral", $data['ProposedCollateral']);
            $stmt->bindParam(":ProposedTenor", $data['ProposedTenor']);
            $stmt->bindParam(":ExceptionalApproval", $data['ExceptionalApproval']);
            $stmt->bindParam(":ProposedSource", $data['ProposedSource']);
            $stmt->bindParam(":ApprovalCommentRisk", $data['ApprovalCommentRisk']);
            $stmt->bindParam(":ApprovalFileUploadRisk", $data['ApprovalFileUploadRisk']);
            $stmt->bindParam(":ApprovalComment", $data['ApprovalComment']);
            $stmt->bindParam(":ApprovalFileUpload", $data['ApprovalFileUpload']);
            $stmt->bindParam(":ProfitabilityAnalysis", $data['ProfitabilityAnalysis']);
            $stmt->bindParam(":LoanID", $data['LoanID']);
            $stmt4->bindParam(":PostedUser", $GLOBALS['POSTEDUSER']);
            $stmt4->bindParam(":LoanID", $data['LoanID']);
            $stmt4->bindParam(":RoleID", $GLOBALS['ROLEID']);
            $stmt4->bindParam(":PWFName", $data['PWFName']);
            
            
            if ($stmt->execute() === false) {
                $err1 = $stmt->errorInfo();
                throw new PDOException();
            }
            
            $stmt2->bindParam(":LoanID", $data['LoanID']);
            $stmt22->bindParam(":LoanID", $data['LoanID']);
            
            if ($stmt2->execute() === false) {
                $err1 = $stmt2->errorInfo();
                throw new PDOException();
            } else {
                
                for ($x = 0; $x <= count($data['account']) - 1; $x++) {
                    
                    $stmt3->bindParam(":LoanID", $data['LoanID']);
                    $stmt3->bindParam(":PostedUser", $data['PostedUser']);
                    $stmt3->bindParam(":UploadStatementPDF", $data['account'][$x]['UploadStatementPDF']);
                    $stmt3->bindParam(":BankName", $data['account'][$x]['BankName']);
                    $stmt3->bindParam(":AccountNo", $data['account'][$x]['AccountNo']);
                    $stmt3->bindParam(":Password", $data['account'][$x]['Password']);
                    $stmt3->bindParam(":UploadStatement", $data['account'][$x]['UploadStatement']);
                    
                    //insert
                    if ($stmt3->execute() === false) {
                        $err1 = $stmt3->errorInfo();
                        throw new PDOException();
                    }
                }
                
                if ($stmt22->execute() === false) {
                    $err1 = $stmt22->errorInfo();
                    throw new PDOException();
                } elseif ($data['pastpo'] != "") {
                    for ($tt = 0; $tt <= count($data['pastpo']) - 1; $tt++) {
                        
                        $stmt33->bindParam(":PostedUser", $data['PostedUser']);
                        $stmt33->bindParam(":LoanID", $data['LoanID']);
                        $stmt33->bindParam(":File", $data['pastpo'][$tt]['File']);
                        $stmt33->bindParam(":Date", $data['pastpo'][$tt]['Date']);
                        
                        //insert
                        if ($stmt33->execute() === false) {
                            $err1 = $stmt33->errorInfo();
                            throw new PDOException();
                        }
                    }
                }
                
                
                
            }
            
            $this->conn->commit();
        }
        catch (PDOException $exception) {
            $this->conn->rollBack();
            
            // $err1 = $exception->getMessage();
            return false;
        }
        
        return true;
    }
    
    function approve($data)
    {
        try {
            
            $this->conn->beginTransaction();
            
            
            // query to insert record
            $query = "INSERT INTO
                    `timeline`
            SET
                PostedUser=:PostedUser,  
                LoanID=:LoanID, 
                RoleID=:RoleID,
                Typed='Loan', 
                PWFName=:PWFName, 
                Note=:Note,
                Status=:Status";
            
            // prepare query
            $stmt = $this->conn->prepare($query);
            
            if ($data['Status'] != 'Rejected') {
                $query2 = "UPDATE `loan`
             SET 
                Status=:Status,
                loan.Level= loan.Level + 1
            WHERE LoanID=:LoanID;";
            } else {
                
                $query2 = "UPDATE `loan`
             SET 
                Status=:Status,
                loan.Level=:SetLevel
            WHERE LoanID=:LoanID;";
            }
            
            // prepare query
            $stmt2 = $this->conn->prepare($query2);
            
            
            // bind values
            
            global $err1;
            
            $stmt->bindParam(":PostedUser", $data['PostedUser']);
            $stmt->bindParam(":LoanID", $data['LoanID']);
            $stmt->bindParam(":RoleID", $data['RoleID']);
            $stmt->bindParam(":PWFName", $data['PWFName']);
            $stmt->bindParam(":Note", $data['Note']);
            $stmt->bindParam(":Status", $data['Status']);
            
            if ($data['Status'] == 'Rejected') {
                $stmt2->bindParam(":SetLevel", $data['SetLevel']);
            }
            $stmt2->bindParam(":Status", $data['Status']);
            $stmt2->bindParam(":LoanID", $data['LoanID']);
            
            
            
            if ($stmt->execute() === false) {
                $err1 = $stmt->errorInfo();
                throw new PDOException();
            }
            if ($stmt2->execute() === false) {
                $err1 = $stmt2->errorInfo();
                throw new PDOException();
            }
            
            $this->conn->commit();
        }
        catch (PDOException $exception) {
            $this->conn->rollBack();
            
            // $err1 = $exception->getMessage();
            return false;
        }
        return true;
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
    
    function getLevel()
    {
        
        // query to check if email exists
        $query = "SELECT `Level` FROM `workflowsetup` WHERE Status != 'Deleted' AND RoleID = ? LIMIT 0,1";
        
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
            $this->Level = $row['Level'];
            
            // return true because RoleID exists in the database
            return $row['Level'];
            //return true;
        }
        
        // return false if RoleID does not exist in the database
        return false;
    }
    
    function populateapprove()
    {
        
        if ($this->xy == 1) {
            
            $query = "SELECT

                loan.*,
                company.CompanyName,
            
          
          
          (
        SELECT `PWFName`
    FROM `timeline` 
    WHERE timeline.LoanID = loan.LoanID ORDER BY `ID` DESC LIMIT 1
        )AS 'PWFName',
        
        (
        SELECT `Note`
    FROM `timeline` 
    WHERE timeline.LoanID = loan.LoanID ORDER BY `ID` DESC LIMIT 1
        )AS 'Note',
        
        (
        SELECT `Status`
    FROM `timeline` 
    WHERE timeline.LoanID = loan.LoanID ORDER BY `ID` DESC LIMIT 1
        )AS 'Status'
          
                FROM
                    `loan`
                INNER JOIN company ON company.CompanyID = loan.CompanyID
        
                WHERE loan.Status != 'Deleted' AND loan.level=:Level AND loan.Initiator IN (" . $this->ReportingLine . ")";
            
        } elseif ($this->xy == 2) {
            
            $query = "SELECT

                loan.*,
                company.CompanyName,
            
          
          
          (
        SELECT `PWFName`
    FROM `timeline` 
    WHERE timeline.LoanID = loan.LoanID ORDER BY `ID` DESC LIMIT 1
        )AS 'PWFName',
        
        (
        SELECT `Note`
    FROM `timeline` 
    WHERE timeline.LoanID = loan.LoanID ORDER BY `ID` DESC LIMIT 1
        )AS 'Note',
        
        (
        SELECT `Status`
    FROM `timeline` 
    WHERE timeline.LoanID = loan.LoanID ORDER BY `ID` DESC LIMIT 1
        )AS 'Status'
          
                FROM
                    `loan`
                INNER JOIN company ON company.CompanyID = loan.CompanyID
        
                WHERE loan.Status != 'Deleted' AND loan.level=:Level AND loan.Initiator=:Initiatore";
            
        } else {
            
            $query = "SELECT

                loan.*,
                company.CompanyName,
            
          
          
          (
        SELECT `PWFName`
    FROM `timeline` 
    WHERE timeline.LoanID = loan.LoanID ORDER BY `ID` DESC LIMIT 1
        )AS 'PWFName',
        
        (
        SELECT `Note`
    FROM `timeline` 
    WHERE timeline.LoanID = loan.LoanID ORDER BY `ID` DESC LIMIT 1
        )AS 'Note',
        
        (
        SELECT `Status`
    FROM `timeline` 
    WHERE timeline.LoanID = loan.LoanID ORDER BY `ID` DESC LIMIT 1
        )AS 'Status'
          
                FROM
                    `loan`
                INNER JOIN company ON company.CompanyID = loan.CompanyID
        
                WHERE loan.Status != 'Deleted' AND loan.level=:Level";
            
        }
        
        
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        
        if ($this->xy == 2) {
            
            $stmt->bindParam(":Initiatore", $GLOBALS['USER']);
            
        }
        $levelnum = $this->Level - 1;
        $stmt->bindParam(":Level", $levelnum);
        // execute query
        $stmt->execute();
        return $stmt;
        
    }
    
    
    function pendingloanread()
    {
        
        if ($this->xy == 1) {
            
            $query = "SELECT
            loan.*,
            company.CompanyName,
            workflowsetup.PWFName
        FROM `loan`
        INNER JOIN company ON company.CompanyID = loan.CompanyID 
        INNER JOIN workflowsetup ON workflowsetup.Level = loan.Level+1
        WHERE loan.Status != 'Deleted' AND `Initiator` IN (" . $this->ReportingLine . ")";
            
        } elseif ($this->xy == 2) {
            
            $query = "SELECT
                loan.*,
                company.CompanyName,
                workflowsetup.PWFName
            FROM `loan`
            INNER JOIN company ON company.CompanyID = loan.CompanyID 
            INNER JOIN workflowsetup ON workflowsetup.Level = loan.Level+1 WHERE loan.Status != 'Deleted' AND loan.Initiator=:Initiatore";
            
        } else {
            
            
            $query = "SELECT
        loan.*,
        company.CompanyName,
        workflowsetup.PWFName
    FROM `loan`
    INNER JOIN company ON company.CompanyID = loan.CompanyID 
    INNER JOIN workflowsetup ON workflowsetup.Level = loan.Level+1 WHERE loan.Status != 'Deleted'";
            
        }
        
        
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        
        if ($this->xy == 2) {
            
            $stmt->bindParam(":Initiatore", $GLOBALS['USER']);
            
        }
        
        // execute query
        $stmt->execute();
        return $stmt;
        
    }
    
    
    function timelineread()
    {
        
        // query to read single record
        $query = "SELECT * FROM `timeline`  WHERE LoanID = :LoanID AND `Typed` = 'Loan' AND `Status` != 'Updated' ";
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        
        $this->LoanID = htmlspecialchars(strip_tags($this->LoanID));
        
        // bind LoanID of Customer to be updated
        $stmt->bindParam(":LoanID", $this->LoanID);
        
        
        // execute query
        $stmt->execute();
        return $stmt;
        
        
    }
}

?>