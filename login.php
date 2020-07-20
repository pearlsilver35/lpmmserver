<?php
// required headers
// get database connection
include_once 'config/database.php';

// instantiate loan object
include_once 'objects/loan.php';

// generate json web token
    include_once 'config/core.php';
    include_once 'libs/php-jwt-master/src/BeforeValidException.php';
    include_once 'libs/php-jwt-master/src/ExpiredException.php';
    include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
    include_once 'libs/php-jwt-master/src/JWT.php';
    use \Firebase\JWT\JWT;
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    //header('HTTP/1.1 200 OK');
    http_response_code(200);
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST , OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Origin");
}else{
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST , OPTIONS");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With, Authorization, Origin");
 
$database = new Database();
$db = $database->getConnection();
 
$loan = new Loan($db);
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// make sure data is not empty
$DataMissing = 200;
if(empty($data->Username)) 
{
    $DataMissing = 'Username';
}elseif(empty($data->Password))
{
    $DataMissing = 'Password';
}
if($DataMissing == 200){
 
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://infolink.pagemfbank.com:6699/businessLendingServices/Authenticate",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode([
            'user'=>$data->Username,
            'pass'=>$data->Password
        ]),
        CURLOPT_HTTPHEADER => [
            //"authorization: Bearer sk_live_20bcb5fa9900ac2013280a3c8e3f238a01dacb61",
            "content-type: application/json",
            "cache-control: no-cache"
        ],
    ));
    $response1 = curl_exec($curl);
    $err = curl_error($curl);
    $response = json_decode($response1, true);
    array($response);
    if($response["ResponseCode"] === "00"){

            $token = array(
                "iss" => $iss,
                "aud" => $aud,
                "iat" => $iat,
                "nbf" => $nbf,
                "exp" => $exp,
                "data" => array(
                    "Username" => $response['Response']['Username'],
                    "RoleID" => $response['Response']['RoleID'],
                    "User" => $data->Username,
                    "Email" => $response['Response']['Email']
                )
             );
          
             // set response code
             http_response_code(200);
          
             // generate jwt
             $jwt = JWT::encode($token, $key);
             echo json_encode(
                     array(
                         "message" => "Successful login.",
                         "Token" => $jwt,
                         
                     )
                 );
     
    }elseif($response == NULL){
        // set response code
     http_response_code(401);
 
     // tell the user login failed
     echo json_encode(array("message" => "Login failed",
                            "Error" => "No Response from External Server"));
 }else{
    // set response code
 http_response_code(401);
 // tell the user login failed
 echo json_encode(array("message" => "Login failed",
                        "Error" => $response["ResponseDescription"]));
}
    
}else{
 
    // set response code - 400 bad request
    http_response_code(400);
 
    // tell the user
    echo json_encode(array("message" => "Error, ".$DataMissing." Field cannot be Empty."));
}
}
?>