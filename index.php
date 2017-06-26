<?php
/**
*  Testing PayApi Payments PHP
*
*  @TODELETE just for DEV
*
*  @debug  /logs
*
**/

ini_set ( 'display_errors' , 1 ) ;
ini_set ( 'display_startup_errors' , 1 ) ;
error_reporting ( E_ALL ) ;

require ( __DIR__ . DIRECTORY_SEPARATOR . 'payapi' . DIRECTORY_SEPARATOR . 'si' . DIRECTORY_SEPARATOR . 'app' . '.' . 'engine' . '.' . 'php' ) ;

$app = new payapi ( array (
  "production"       =>    false , // bool true/false
  "debug"            =>     true , // bool true/false
  "archival"         =>     true , // bool true/false
  "plugin"           =>    false , // string [opencart,magento,prestashop,default] OR bool false
  "mode"             =>   'dump' , // string [json,object,array,dump,string,html] OR bool false
  "headers"          =>    false , // bool true/false
  "branding"         => 'payapi' , // string [payapi] OR bool false
  "payapi_public_id" => 'multimerchantshop' , // *MANDATORY PayApi public id (PayApi backend)
  "payapi_api_key"   => 'qETkgXpgkhNKYeFKfxxqKhgdahcxEFc9'  // *MANDATORY PayApi api key (PayApi backend)
) ) ;

var_dump ( $app -> settings () ) ;
