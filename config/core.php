<?php
// show error reporting
error_reporting(E_ALL);
 
// set your default time-zone
date_default_timezone_set('Africa/Lagos');
 
// variables used for jwt
$key = "iJKV1QiLCJhbGci";
$iss = "http://Businessloanserver.org";
$aud = "http://businessloan.com";
$iat = time();
$nbf = $iat;
$exp = $nbf + 14400;

//Passwordhash Variable
$pepper = "iOiJKV1QiLCJhbGciOiJIUzI1NiJ9";
?>