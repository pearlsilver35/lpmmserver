<?php
// required headers
require_once '../config/headers.php';
 
// get database connection
include_once '../config/database.php';
 
// instantiate workflowsetup object
include_once '../objects/workflowsetup.php';
 
$database = new Database();
$db = $database->getConnection();
 
$workflowsetup = new WorkflowSetup($db);
 
// get posted data
$data = json_decode(file_get_contents("php://input") , true);
 
// make sure data is not empty

if($statuses == 'Access'){
   
    // create the workflowsetup
    if($workflowsetup->create($data)){
 
        // set response code - 201 created
        http_response_code(201);
 
        // tell the user
        echo json_encode(array("message" => "Workflow setup was Updated Successfully."));
    }
 
    // if unable to create the workflowsetup, tell the user
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
?>