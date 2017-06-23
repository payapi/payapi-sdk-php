<?php

// config sample
$config = array (
  "production"     =>    false , // bool true/false
  "debug"          =>     true , // bool true/false
  "archival"       =>     true , // bool true/false
  "plugin"         =>    false , // string [opencart,magento,prestashop,default] OR bool false
  // @FIXME array/object render is duplicated
  "mode"           => 'string' , // string [json,object,array,string,html] OR bool false
  "headers"        =>     true ,
  "branding"       => 'payapi'   // string [payapi] OR bool false
) ;
