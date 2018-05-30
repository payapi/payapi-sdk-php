<?php
require(__DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'sdk' . '.' . 'php');

//-> testing PA ip endPoint performance
$test = $sdk -> localize(true, '79.159.' . rand(141, 240) . '.' . rand(141, 240));