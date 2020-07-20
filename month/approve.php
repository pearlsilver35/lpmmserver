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
$data = json_decode(file_get_contents("php://input"), true);

$optionalfields = array();
$expectedFields = array( 'LoanID' , 'Initiator' ,  'Status', 'Note' );

// make sure data is not empty

if($statuses == 'Access'){
    $DataMissing =   Utility::ValidateEmpty($data, $expectedFields, $optionalfields);

    if($DataMissing == 200 || $DataMissing == ""){

        $loan->RoleID =$GLOBALS['ROLEID'];
        $PWFName = $loan->getPWFName();
        //$getStatus = $data['Status'] == 'Approved' ? $loan->getStatus() : $data['Status']; 
       // $getStatus = $loan->getStatus();
        
    if($PWFName == ""){
        // set response code - 400 bad request
    http_response_code(400);
 
    // tell the user
    echo json_encode(array("message" => "Unable to ".$data['Status']." Loan, User is unauthoried"));

    }else{

	// set loan property values
    $data['RoleID'] = $GLOBALS['ROLEID'];
    $data['PostedUser'] = $GLOBALS['POSTEDUSER'];
    $data['PWFName'] = $PWFName;
    $data['Status'] = ucwords($data['Status']);



    if ($data['Status'] == 'Rejected' ) {

        $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://infolink.pagemfbank.com:6699/businessLendingServIces/GetDetails",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode([
            'Username'=>$data['Initiator']
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

        $loan->RoleID = $response["RoleID"];
        $PWFName2 = $loan->getPWFName();

        if ($PWFName2 == 'Relationship Manager') {

           $data['SetLevel'] = 0;
        } else {
           $data['SetLevel'] = -1;
        }
        

    }else{
    // set response code - 404 Not found
    http_response_code(503);
 
    $error = array();
        $error = array(

            "message" => "Something went wrong"
        );
        //echo json_encode($err1);
        echo json_encode($error);
    }


        
    } else {
        $data['SetLevel'] = 10;
    }
    

        // delete the loan
    if($loan->approve($data)){

        $Type = $data['Status'];
        $CC = '';
        $Status = $data['PWFName'];
        $Owner = $data['Initiator'];
        $User = $GLOBALS['USER'];
        $Typed = 'Loan';
        $Subject = $data['Status'].' on Loan';
        $Content = '<html><body>'.$data['LoanID'].' Loan was '.$data['Status'].' successfully by '.$data['PWFName'].' , Kindly login to your dashboard to view the application.</body></html>';
 
        $message =   EmailNotifications::Sendemail($CC, $User , $Subject , $Content, $Owner , $Type , $Status , $Typed);
 
        // set response code - 201 deleted
        http_response_code(201);
 
        // tell the user
        echo json_encode(array("message" => "Loan was ".$data['Status']." Successfully."));
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
        //echo json_encode($err1);
        echo json_encode($error);
    }
    }

    
}
 
// tell the user data is incomplete
else{
 
    // set response code - 400 bad request
    http_response_code(400);
 
    // tell the user
    echo json_encode(array("message" => "Unable to ".$data['Status']." Loan ".$DataMissing." Field is Empty."));
}
}
?>