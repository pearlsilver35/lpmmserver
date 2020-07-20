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

$optionalfields = array();
$expectedFields = array( 'LoanID' );

// make sure data is not empty

if($statuses == 'Access'){
    $DataMissing =   Utility::ValidateEmpty($datas, $expectedFields, $optionalfields);

    if($DataMissing == 200 || $DataMissing == ""){

	// set loan property values
    $loan->LoanID = $data->LoanID;

    // delete the loan
    if($loan->delete()){
 
        // set response code - 201 deleted
        http_response_code(201);
 
        // tell the user
        echo json_encode(array("message" => "Loan was Deleted Successfully."));
    }
 
    // if unable to delete the loan, tell the user
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
 
// tell the user data is incomplete
else{
 
    // set response code - 400 bad request
    http_response_code(400);
 
    // tell the user
    echo json_encode(array("message" => "Unable to Delete Loan ".$DataMissing." Field is Empty."));
}
}
?>