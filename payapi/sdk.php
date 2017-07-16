<?php

/*
*
*  @NOTE update engine to use native plugin
*
*/

require ( "/opt/php-jwt/vendor/autoload.php" ) ;
use \Firebase\JWT\JWT;

use \payapi\engine as engine ;

class payapi {

  private
    $app                       =   false ;

  public function __construct ( $adapt ) {
    $this -> hack  () ;
    require_once ( 'app' . '.' . 'engine' . '.' . 'php' ) ;
    $this -> app = engine :: single ( $adapt ) ;
    return $this -> app ;
  }

  private function hack () {
    //-> cli hack
    if ( is_string ( getenv ( 'SERVER_NAME' ) ) !== true || getenv ( 'SERVER_NAME' ) === 'store.multimerchantshop.dev' ) {
      putenv ( 'SERVER_NAME=store.multimerchantshop.xyz' ) ;
    }
    if ( is_string ( getenv ( 'SERVER_NAME' ) ) !== true ) {
      putenv ( 'REMOTE_ADDR=84.79.234.58' ) ;
    }
  }

  public function __call ( $command , $arguments = array () ) {
    return $this -> app -> $command ( $arguments ) ;
  }


}

$settings = array (
  "debug"    => true ,
  "staging"  => true
) ;

$sdk = new payapi ( $settings ) ;

var_dump ( '__info' , $sdk -> localize ( true ) ) ;

exit () ;
