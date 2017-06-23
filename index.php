<?php
/**
*  Testing PayApi Payments PHP
**/

// @TODELETE just for DEV
ini_set ( 'display_errors' , 1 ) ;
ini_set ( 'display_startup_errors' , 1 ) ;
error_reporting ( E_ALL ) ;

require ( __DIR__ . DIRECTORY_SEPARATOR . 'payapi' . DIRECTORY_SEPARATOR . 'autoload' . '.' . 'php' ) ;

function example ( $key ) {
  // examples
  $examples = array (
    "error" ,
    "info" ,
    "validate" ,
    "settings" ,
    "tostring"
  ) ;
  // app config
  $config = array (
    "production"     =>    false , // bool true/false
    "debug"          =>     true , // bool true/false
    "archival"       =>     true , // bool true/false
    "plugin"         =>    false , // string [opencart,magento,prestashop,default] OR bool false
    // @FIXME array/object render is duplicated?
    "mode"           => 'string' , // string [json,object,array,string,html] OR bool false
    "headers"        =>     true ,
    "branding"       => 'payapi'   // string [payapi] OR bool false
  ) ;
  $app = new payapi ( $config ) ;
  if ( in_array ( $key , $examples ) ) {
    require ( __DIR__ . DIRECTORY_SEPARATOR . 'example' . DIRECTORY_SEPARATOR . 'example' . '.' . $key . '.' . 'php' ) ;
  } else {
    $app -> error ( 404 ) ;
  }
}
// test
$foo = example ( 'settings' ) ;
