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

if($statuses == 'Access'){


// read the details of report to be edited
$stmt = $loan->read();
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
    // set response code - 404 Not found
    http_response_code(404);
 
    // tell the user report does not exist
    echo json_encode(array("message" => "No records found."));
}

}?>