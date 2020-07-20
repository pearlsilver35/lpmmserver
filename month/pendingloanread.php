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

    if ($GLOBALS['PWFName'] == "Relationship Manager") {
            
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://infolink.pagemfbank.com:6699/businessLendingServices/GetReportingLine3",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                'Username'=>$GLOBALS['USER']
                ]),
            CURLOPT_HTTPHEADER => [
            //"authorization: Bearer sk_live_20bcb5fa9900ac2013280a3c8e3f238a01dacb61",
            "content-type: application/json",
            "cache-control: no-cache"
            ],
        ));
        $response1 = curl_exec($curl);
        $err       = curl_error($curl);
        $response  = json_decode($response1, true);
        array(
            $response
        );
        if ($response["ResponseCode"] === "00") {
            //$loan->ReportingLine =  implode(', ', array_column($response["SalesOficer"], 'UserID'));
            $loan->ReportingLine = trim($response["SalesOficer"], ",");
            
            $loan->xy = 1;
            
        } else {
            // set response code - 404 Not found
            http_response_code(404);
            
            // tell the user report does not exist
            echo json_encode(array(
                "message" => "No Record Found."
            ));
        }
        
    }elseif ($GLOBALS['PWFName'] == "Sales Officer") {
        $loan->xy = 2;
    } else {
        $loan->xy = 3;
    }

$stmt = $loan->pendingloanread();    
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