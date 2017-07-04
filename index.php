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
  "production"       =>      false , // bool true/false
  "debug"            =>       true , // bool true/false
  "archival"         =>       true , // bool true/false
  "plugin"           => 'opencart' , // string [opencart,magento,prestashop,default] OR bool false
  "mode"             =>    'json' , // string [json,object,array,dump,string,html] OR bool false
  "headers"          =>       true , // bool true/false
  "branding"         =>   'payapi' , // string [payapi/internetcreatives/nets] OR bool false
  "payapi_public_id" =>   'multimerchantshop' , // PayApi public id (PayApi backend) *MANDATORY
  "payapi_api_key"   =>   'qETkgXpgkhNKYeFKfxxqKhgdahcxEFc9'  // PayApi api key (PayApi backend) *MANDATORY
) ) ;

//var_dump ( $app -> callback () ) ;

//***
$product = array (
  "id"                 => 'id001' ,
  "quantity"           => 1 ,
  "title"              => 'title' ,
  "description"        => 'description' ,
  "imageUrl"           => '' ,
  "category"           => '' ,
  "options"            => array () ,
  "model"              => 'model' ,
  "priceInCentsIncVat" => 10000 ,
  "priceInCentsExcVat" => 10000 ,
  "vatInCents"         => 0 ,
  "vatPercentage"      => 0 ,
  "extraData"          => ''
) ;

var_dump ( $app -> validate ( 'product' , $product ) ) ;
//**/
