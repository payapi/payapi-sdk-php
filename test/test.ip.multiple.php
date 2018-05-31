<?php
require(__DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'sdk' . '.' . 'php');
//-> testing PA ip endPoint performance

$qty = 50;
$test = array();

for($cont=0; $cont<$qty; $cont++) {
	$ip = '79.159.' . rand(141, 240) . '.' . rand(22, 240);
	$test[$ip] =  $sdk->localize(true, $ip);
}

return $test;