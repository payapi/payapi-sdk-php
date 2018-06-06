<?php

/*
*
*  @NOTE
*        hacks enviroment variables to run SDK in server mode
*
*/

$hack_domain = 'payapi.io';
$hack_ip = '146.148.30.222';

if (isset($_SERVER) !== true) {
	$_SERVER = array();
}

if (is_string(getenv('SERVER_NAME')) !== true) {
	$_SERVER['SERVER_NAME'] = $hack_domain;
	putenv('SERVER_NAME=' . $hack_domain);
}
if (is_string(getenv('SERVER_HOST')) !== true) {
	$_SERVER['HTTP_HOST'] = $hack_domain;
 	putenv('SERVER_HOST=' . $hack_domain);
}
if (is_string(getenv('REMOTE_ADDR')) !== true) {
	$_SERVER['REMOTE_ADDR'] = $hack_ip;
	putenv('REMOTE_ADDR=' . $hack_ip);
}