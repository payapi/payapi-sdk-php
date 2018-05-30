<?php
require(__DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'sdk' . '.' . 'php');
//$test = $sdk -> settings ( 'your_public_id' , 'your_api_key' , true ) ;
//$test = $sdk -> settings ( false, false , true ) ;
$test = $sdk -> settings();

var_dump($test);
