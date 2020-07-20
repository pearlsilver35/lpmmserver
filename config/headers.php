<?php
include_once '../config/core.php';
require_once '../validate_token.php';
// require_once '../emailnotification.php';

 if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {

    //header('HTTP/1.1 200 OK');
    
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST , OPTIONS");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With, Authorization, Origin");
    header("Access-Control-Allow-Credentials: true");
    http_response_code(200);
    $statuses = 'NoAccess';
}else{
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST , OPTIONS");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With, Authorization, Origin");
    header("Access-Control-Allow-Credentials: true");
    //Security will update later
    header("X-Frame-Options: deny");
    header("X-Content-Type-Options: nosniff");
    $statuses = 'NoAccess';
    $arr = "";
    $headers = apache_request_headers();

    If(isset($headers['Authorization'])){

    

    $arr = explode(" ", $headers['Authorization']);

    $jwt = $arr[1];
    
    $validate = new Validateclass();
    if($validate->validate($jwt)){
        $statuses = 'Access';
    }else{
         // set response code - 503 service unavailable
    http_response_code(403);
 
    // tell the employee
    //echo json_encode(array("message" => "Unable to update employee."));
    
    echo json_encode(
        array(
            "message" => "Access Denied",
            "Error" =>$error
        )
    );
    }
}else{
    http_response_code(403);
 
    // // tell the employee
    // //echo json_encode(array("message" => "Unable to update employee."));
    
    echo json_encode(
        array(
            "message" => "Access Denied",
            "Error" =>"JWT Empty",
            "Error2" =>$arr
        )
    );
}
}
class Utility {
    public static function ValidateEmpty($datas, $expectedFields , $optionalfields) {
        $DataMissing = 200;
        $DataMissingarray = array(); 
        $sentFields = array(); 
    foreach($datas as $key => $value){
        array_push($sentFields , $key);
        $w = array_search($key, $optionalfields);
        if($value == ""){
            if(empty($w)){

                array_push($DataMissingarray , $key);
                $DataMissing =  implode(",",$DataMissingarray);
            }
            
        }  
    
  }
  $missingFieldarray = array_diff($expectedFields, $sentFields);
  $missingFields =  implode(",",$missingFieldarray);
  array_push($DataMissingarray ,$missingFields);
  $DataMissing =  implode(",",$DataMissingarray);
  return $DataMissing;
}

}
?>
