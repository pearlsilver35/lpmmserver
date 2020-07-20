<?php
// required headers
require_once '../config/headers.php';
 
// get database connection
include_once '../config/database.php';
 
// instantiate generic object
include_once '../objects/generic.php';
 
$database = new Database();
$db = $database->getConnection();
 
$generic = new Generic($db);
 
// get posted data
$data = json_decode(file_get_contents("php://input"));
$datas = json_decode(file_get_contents("php://input") , true);

$generic->TableName ='loan';
$generic->IDField = ucwords($generic->TableName).'ID';
$generic->IDFieldvalue =$data->LoanID;

$optionalfields = array();
$expectedFields = array(
'LoanID',
'Initiator',
'Status' 
);



 
// make sure data is not empty

if($statuses == 'Access'){
    $DataMissing =   Utility::ValidateEmpty($datas, $expectedFields, $optionalfields);

    if($DataMissing == 200 || $DataMissing == ""){


        $generic->RoleID =$GLOBALS['ROLEID'];
        $PWFName = $generic->getPWFName();
        if($PWFName == ""){
            // set response code - 400 bad request
        http_response_code(400);
    
        // tell the user
        echo json_encode(array("message" => "Operation unsuccessful, User is unauthoried"));

        }else{    
        $InsertedLoanID = $data->LoanID;
        $Initiator = $data->Initiator;
        // set generic property values
        unset($data->LoanID);
        unset($data->Initiator);
        $generic->datas =$data;

    
 
    // update the generic
    if($generic->genericupdate()){

        $Type = 'Generated';
        $CC = '';
        $Status = $PWFName;
        $Owner = $Initiator;
        $User = $GLOBALS['USER'];
        $Typed = 'Loan';
        $Subject = 'Loan Proccess Initialization';
        $Content = '<html><body>'.$InsertedLoanID.' Loan process as being submitted successfully.</body></html>';
 
        $message =   EmailNotifications::Sendemail($CC, $User , $Subject , $Content, $Owner , $Type , $Status , $Typed);
 
 
        // set response code - 201 updated
        http_response_code(201);
 
        // tell the user
        echo json_encode(array("message" => ucwords($generic->TableName)." was updated."));
    }
 
    // if unable to update the generic, tell the user
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
    echo json_encode(array("message" => "Unable to update ".ucwords($generic->TableName).", ".$DataMissing." Field is Empty."));
}
}
?>