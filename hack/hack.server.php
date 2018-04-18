<?php

/*
*
*  @NOTE
*        hacks enviroment variables to run SDK in server mode
*
*/

$hack_domain = 'payapi.io';
$hack_ip = '146.148.30.253';

if (is_string(getenv('SERVER_NAME')) !== true) {
  putenv('SERVER_NAME=' . $domain);
}
if (is_string(getenv('SERVER_HOST')) !== true) {
  putenv('SERVER_HOST=' . $domain);
}
if (is_string(getenv('REMOTE_ADDR')) !== true) {
  putenv('REMOTE_ADDR=' . $ip);
}