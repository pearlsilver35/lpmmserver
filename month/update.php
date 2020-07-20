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

$optionalfields = array('','ExceptionalApproval','ProfitabilityAnalysis','ApprovalFileUpload','ApprovalComment', 'PastPoPaymentDate','PFI', 'BadCreditReport',
'BadCreditReportFile',
'ApprovalFileUploadRisk',
'ApprovalCommentRisk',
'pastpo', 
'Po');
$expectedFields = array(
    'CompanyID', 
    'LoanID', 
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
    'BadCreditReport',
    'ApprovalFileUpload',
    'ApprovalComment',
    'ApprovalFileUploadRisk',
    'ApprovalCommentRisk',
    'BadCreditReportFile',
    'ProfitabilityAnalysis',
    'ExceptionalApproval',
    'ProposedSource'

);
 
// make sure data is not empty

if($statuses == 'Access'){
    $DataMissing =   Utility::ValidateEmpty($datas, $expectedFields, $optionalfields);

    if($DataMissing == 200 || $DataMissing == ""){
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
    

    
 
    // update the loan
    if($loan->update($datas)){
 
        // set response code - 201 updated
        http_response_code(201);
 
        // tell the user
        echo json_encode(array("message" => "Loan was updated."));
    }
 
    // if unable to update the loan, tell the user
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
    echo json_encode(array("message" => "Unable to update Loan ".$DataMissing." Field is Empty."));
}
}
?>