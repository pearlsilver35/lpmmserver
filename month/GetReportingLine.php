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

    
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://infolink.pagemfbank.com:6699/businessLendingServices/GetReportingLine",
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
        //$cam->ReportingLine =  implode(', ', array_column($response["SalesOficer"], 'UserID'));
        //$ReportingLine = trim($response["SalesOficer"], ",");
        $ReportingLine = implode(',', array_column($response["SalesOficer"], 'UserID'));
        $ReportingLine = explode(',' , $ReportingLine);
        
        $record_arr=array();
        $record_arr["records"]=array();
        array_push($record_arr["records"], $ReportingLine);
        
       
    // set response code - 200 OK
    http_response_code(200);
 
    // make it json format
    
    echo json_encode($record_arr);
}
    else{
    // set response code - 404 Not found
    http_response_code(404);
 
    // tell the user report does not exist
    echo json_encode(array("message" => "No Record Found."));
}

    
}?>