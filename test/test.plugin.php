<?php
require(__DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'sdk' . '.' . 'php');

$test = $sdk->plugin('somepay');

var_dump($test);
