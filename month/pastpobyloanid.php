<?php
// required headers
require_once '../config/headers.php';
 
// include database and object files
include_once '../config/database.php';
include_once '../objects/loan.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// prepare report object
$loan = new Loan($db);

$data = json_decode(file_get_contents("php://input"));
//Preparing Data to send to Utility
$dataarray = json_decode(file_get_contents("php://input") , true);
$optionalfields = array();
$expectedFields = array('LoanID');

if($statuses == 'Access'){

    $DataMissing =   Utility::ValidateEmpty($dataarray, $expectedFields, $optionalfields);

    if($DataMissing == 200 || $DataMissing == ""){
        
        $loan->LoanID = $data->LoanID;

// read the details of report to be edited
$stmt = $loan->pastporead();
$num = $stmt->rowCount();

if($num>0){

    $record_arr=array();
    $record_arr["records"]=array();
    // create array
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
        $record_item = $row;

    array_push($record_arr["records"], $record_item);
}
    // set response code - 200 OK
    http_response_code(200);
 
    // make it json format
    
    echo json_encode($record_arr);
}
 
else{
    
        //As requested by Eniola Ajani to change the error code
        http_response_code(200);
 
        // tell the user report does not exist
        echo json_encode(array("records" => []));
}
// tell the user data is incomplete
}
else{
 
    // set response code - 400 bad request
    http_response_code(400);
 
    // tell the user
    echo json_encode(array("message" => "Unable to Display Report, ".$DataMissing." Field is Empty."));
    //var_dump($DataMissing);

    }
}?>