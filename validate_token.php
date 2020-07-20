<?php
// include file to decode jwt

include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;
class Validateclass{
// required headers


    function validate($data){
        include 'config/core.php';
 
// get jwt
$jwt=isset($data) ? $data : "";


// if jwt is not empty
if($jwt){
 
    // if decode succeed, show user details
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $decodeddata = $decoded->data;
        $GLOBALS['EMAIL'] = $decodeddata->Email;
        $GLOBALS['POSTEDUSER'] = $decodeddata->Username;
        $GLOBALS['ROLEID'] = $decodeddata->RoleID;
        $GLOBALS['PWFName'] = $decodeddata->PWFName;
        $GLOBALS['USER'] = $decodeddata->User;
        return true;

 
    }
 
    // if decode fails, it means jwt is invalid
catch (Exception $e){
    global  $error;
    $error = $e->getMessage();
    return false;
}
}
 
// show error message if jwt is empty
else{
 
 return false;
}
}

}
?>