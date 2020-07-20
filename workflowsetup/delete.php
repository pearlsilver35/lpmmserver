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

$generic->TableName ='workflowsetup';
$generic->IDField = ucwords($generic->TableName).'ID';


$optionalfields = array();
$expectedFields = array( 'WorkflowSetupID' );

// make sure data is not empty

if($statuses == 'Access'){
    $DataMissing =   Utility::ValidateEmpty($datas, $expectedFields, $optionalfields);

    if($DataMissing == 200 || $DataMissing == ""){

	// set generic property values
    $generic->IDFieldvalue = $data->WorkflowSetupID;

    // delete the generic
    if($generic->genericdelete()){
 
        // set response code - 201 deleted
        http_response_code(201);
 
        // tell the user
        echo json_encode(array("message" => ucwords($generic->TableName)." was Deleted Successfully."));
    }
 
    // if unable to delete the generic, tell the user
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
    echo json_encode(array("message" => "Unable to Delete ".ucwords($generic->TableName).", ".$DataMissing." Field is Empty."));
}
}
?>