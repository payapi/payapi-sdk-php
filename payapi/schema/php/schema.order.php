<?php

$schema = array (
  "___info___" = array (
    "version" => "0.0.1" ,
    "createdAt" => date ( 'Ymd' , time () ) ,
    "updateddAt" => date ( 'Ymd' , time () )
  ) ,
  "___schema___" = array (
    "sumInCentsIncVat" = array (
      "___mandatory___" => true ,
      "___type___" => "int"
    ) ,
    "sumInCentsExcVat" = array (
      "___mandatory___" => true ,
      "___type___" => "int"
    ) ,
    "vatInCents" = array (
      "___mandatory___" => true ,
      "___type___" => "int"
    ) ,
    "currency" = array (
      "___mandatory___" => true ,
      "___type___" => "string"
    ) ,
    "referenceId" = array (
      "___mandatory___" => true ,
      "___type___" => "string"
    ) ,
    "tosUrl" = array (
      "___mandatory___" => false ,
      "___type___" => "string"
    )
  )
