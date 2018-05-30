<?php
require(__DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'sdk' . '.' . 'php');
//$test = $sdk -> localize ( true ) ;
$test = $sdk->localize();

var_dump($test);
