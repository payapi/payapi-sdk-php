<?php
/*
*
*  fast TEST/DEMO script
*
*  @NOTE to debug, in payapi folder:
*        $ tail -f -n300 debug/debug.payapi.log | perl colored.pl
*                                                      ( colored.pl is optimized for iTerm2 )
*
*/

require ( 'sdk' . '.' . 'php' ) ;

//-> SDK config
$config = array (
  "debug"    => true ,
  "staging"  => true
) ;

//-> @NOTE check 'test' folder out
test ( 'info' , $config ) ;
/*
$sdk = new payapiSdk ( $config ) ;
$test = $sdk -> migrate () ;
var_dump ( $test ) ;
//->*/
//->
exit () ;
