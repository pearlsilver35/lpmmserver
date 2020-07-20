<?php
// required headers
require_once '../config/headers.php';
 
// get database connection
include_once '../config/database.php';
 
// instantiate loan object
include_once '../objects/loan.php';
 
$database = new Database();
$db = $database->getConnection();
 
$loan = new Loan($db);
 
// get posted data
$data = json_decode(file_get_contents("php://input"));
$datas = json_decode(file_get_contents("php://input") , true);

$optionalfields = array( '','ExceptionalApproval', 'PFI','ProfitabilityAnalysis', 'pastpo', 'Po' , 'BadCreditReport', 'BadCreditReportFile');
$expectedFields = array(
'CompanyID', 
'account', 
'LoanRequestAmount', 
'LoanType', 
'Po', 
'pastpo', 
'PFI', 
'LoanPurpose', 
'ProposedCollateral', 
'ProposedTenor', 
'CreditReport',
'Initiator',
'BadCreditReport',
'BadCreditReportFile',
'ProfitabilityAnalysis',
'ExceptionalApproval',
'Status',
'ProposedSource'
);
 
// make sure data is not empty

if($statuses == 'Access'){
    $DataMissing =   Utility::ValidateEmpty($datas, $expectedFields, $optionalfields);

    if($DataMissing == 200 || $DataMissing == ""){

    // set loan property values
            $loan->RoleID =$GLOBALS['ROLEID'];
            $PWFName = $loan->getPWFName();
        if($PWFName == ""){
            // set response code - 400 bad request
        http_response_code(400);
     
        // tell the user
        echo json_encode(array("message" => "Operation unsuccessful, User is unauthoried"));
    
        }else{    
    $datas['PostedUser'] = $GLOBALS['POSTEDUSER'];
    $datas['PWFName'] = $PWFName;
    
    
 
    // create the loan
    if($loan->create($datas)){
        $Type = 'Generated';
        $CC = '';
        $Status = $datas['PWFName'];
        $Owner = $datas['Initiator'];
        $User = $GLOBALS['USER'];
        $Typed = 'Loan';
        $Subject = 'Loan Proccess Initialization';
        $Content = '<html><body>'.$InsertedLoanID.' Loan application has being initiated. Kindly complete all requiered fields and submit your application to proceed.</body></html>';
 
        $message =   EmailNotifications::Sendemail($CC, $User , $Subject , $Content, $Owner , $Type , $Status , $Typed);
 
        // set response code - 201 created
        http_response_code(201);
 
        // tell the user
        echo json_encode(array("message" => "Loan was created.", "InsertedID" => $InsertedLoanID));
    }
 
    // if unable to create the loan, tell the user
    else{
 
        // set response code - 503 service unavailable
        http_response_code(503);
 
        // tell the user
        $error = array();
        $error = array(

            "message" => $err1[2] == NULL ? "Something Went wrong" : $err1[2]
        );
        
        echo json_encode($error);
    }
}
}
// tell the user data is incomplete
else{
 
    // set response code - 400 bad request
    http_response_code(400);
 
    // tell the user
    echo json_encode(array("message" => "Unable to create Loan ".$DataMissing." Field is Empty."));
}
}
?>