<?php

$schema = array (
  "___info___" => array (
    "version"    => '0.0.0' ,
    "createdAt"  => date ( 'Ymd' , time () ) ,
    "updateddAt" => date ( 'Ymd' , time () )
  ) ,
  "___schema___" => array (
    "id" => array (
      "___mandatory___" => false ,
      "___type___" => 'string'
    ) ,
    "quantity" => array (
      "___mandatory___" => true ,
      "___type___" => 'int'
    ) ,
    "title" => array (
      "___mandatory___" => true ,
      "___type___" => 'string'
    ) ,
    "description" => array (
      "___mandatory___" => false ,
      "___type___" => 'string'
    ) ,
    "imageUrl" => array (
      "___mandatory___" => false ,
      "___type___" => 'string'
    ) ,
    "category" => array (
      "___mandatory___" => false ,
      "___type___" => 'string'
    ) ,
    "options" => array (
      "___mandatory___" => false ,
      "___type___" => 'array'
    ) ,
    "model" => array (
      "___mandatory___" => false ,
      "___type___" => 'string'
    ) ,
    "priceInCentsIncVat" => array (
      "___mandatory___" => true ,
      "___type___" => 'int'
    ) ,
    "priceInCentsExcVat" => array (
      "___mandatory___" => true ,
      "___type___" => 'int'
    ) ,
    "vatInCents" => array (
      "___mandatory___" => true ,
      "___type___" => 'int'
    ) ,
    "vatPercentage" => array (
      "___mandatory___" => true ,
      "___type___" => 'int'
    ) ,
    "extraData" => array (
      "___mandatory___" => false ,
      "___type___" => 'string'
    )
  )
) ;
