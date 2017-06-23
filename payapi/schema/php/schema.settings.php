<?php

$schema = array (
  "___info___" => array (
    "version"    => '0.0.1' ,
    "createdAt"  => date ( 'Ymd' , time () ) ,
    "updateddAt" => date ( 'Ymd' , time () )
  ) ,
  "___schema___" => array (
    "preselectedPartialPayment" => array (
      "___mandatory___" => true ,
      "___type___" => 'string'
    ) ,
    "minimumAmountAllowed" => array (
      "___mandatory___" => true ,
      "___type___" => 'int'
    ),
    "invoiceFee" => array (
      "___mandatory___" => true ,
      "___type___" => 'int'
    ),
    "invoiceFeeCurrency" => array (
      "___mandatory___" => true ,
      "___type___" => 'string'
    ),
    "nominalAnnualInterestRate" => array (
      "___mandatory___" => true ,
      "___type___" => 'int'
    ),
    "whitelistedCountries" => array (
      "___mandatory___" => false ,
      "___type___" => 'array'
    )
  )
) ;
