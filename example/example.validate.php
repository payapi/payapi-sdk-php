<?php

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

$app -> validate ( 'product' , $product ) ;
