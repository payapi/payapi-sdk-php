<?php

$schema = array (
  "___info___" => array (
    "version"    => '0.0.1' ,
    "createdAt"  => date ( 'Ymd' , time () ) ,
    "updateddAt" => date ( 'Ymd' , time () )
  ) ,
  "___schema___" => array (
    "order" => array (
      "___mandatory___" => true ,
      "___type___" => 'array' ,
      "sumInCentsIncVat" => array (
        "___mandatory___" => true ,
        "___type___" => 'int'
      ) ,
      "sumInCentsExcVat" => array (
        "___mandatory___" => true ,
        "___type___" => 'int'
      ) ,
      "vatInCents" => array (
        "___mandatory___" => true ,
        "___type___" => 'int'
      ) ,
      "currency" => array (
        "___mandatory___" => true ,
        "___type___" => 'string'
      ) ,
      "referenceId" => array (
        "___mandatory___" => true ,
        "___type___" => 'string'
      ) ,
      "tosUrl" => array (
        "___mandatory___" => false ,
        "___type___" => 'string'
      )
    ) ,
    "products" => array (
      "___mandatory___" => true ,
      "___type___" => 'array' ,
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
    ) ,
    "shippingAddress" => array (
      "___mandatory___" => false ,
      "___type___" => 'array' ,
      "recipientName" => array (
        "___mandatory___" => false ,
        "___type___" => 'string'
      ) ,
      "co" => array (
        "___mandatory___" => false ,
        "___type___" => 'string'
      ) ,
      "streetAddress" => array (
        "___mandatory___" => false ,
        "___type___" => 'string'
      ) ,
      "streetAddress2" => array (
        "___mandatory___" => false ,
        "___type___" => 'string'
      ) ,
      "postalCode" => array (
        "___mandatory___" => false ,
        "___type___" => 'string'
      ) ,
      "city" => array (
        "___mandatory___" => false ,
        "___type___" => 'string'
      ) ,
      "stateOrProvince" => array (
        "___mandatory___" => false ,
        "___type___" => 'string'
      ) ,
      "countryCode" => array (
        "___mandatory___" => false ,
        "___type___" => 'string'
      )
    ) ,
    "consumer" => array (
      "___mandatory___" => false ,
      "___type___" => 'array' ,
      "consumerId" => array (
        "___mandatory___" => false ,
        "___type___" => 'string'
      ) ,
      "email" => array (
        "___mandatory___" => false ,
        "___type___" => 'string'
      ) ,
      "locale" => array (
        "___mandatory___" => false ,
        "___type___" => 'string'
      ) ,
      "mobilePhoneNumber" => array (
        "___mandatory___" => false ,
        "___type___" => 'string'
      )
    ) ,
    "callbacks" => array (
      "___mandatory___" => false ,
      "___type___" => 'array' ,
      "processing" => array (
        "___mandatory___" => false ,
        "___type___" => 'string'
      ) ,
      "success" => array (
        "___mandatory___" => false ,
        "___type___" => 'string'
      ) ,
      "failed" => array (
        "___mandatory___" => false ,
        "___type___" => 'string'
      ) ,
      "chargeback" => array (
        "___mandatory___" => false ,
        "___type___" => 'string'
      )
    ) ,
    "returnUrls" => array (
      "___mandatory___" => false ,
      "___type___" => 'array' ,
      "success" => array (
        "___mandatory___" => false ,
        "___type___" => 'string'
      ) ,
      "cancel" => array (
        "___mandatory___" => false ,
        "___type___" => 'string'
      ) ,
      "failed" => array (
        "___mandatory___" => false ,
        "___type___" => 'string'
      )
    )
  )
) ;
