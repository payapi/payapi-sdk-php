<?php

namespace payapi ;

final class instance {

  public static function this () {
    return self :: encode ( self :: domain () ) ;
  }

  public static function get ( $domain ) {
    return self :: encode ( $domain ) ;
  }

  public static function domain () {
    //-> @NOTE @CARE @TODELETE
    if ( is_string ( getenv ( 'SERVER_NAME' ) ) !== true || getenv ( 'SERVER_NAME' ) == 'store.multimerchantshop.dev' ) {
      putenv ( 'SERVER_NAME=store.multimerchantshop.xyz' ) ;
    }
    return getenv ( 'SERVER_NAME' ) ;
  }

  private static function encode ( $decoded ) {
    if ( is_string ( $decoded ) === true ) {
      return md5 ( $decoded ) ;
    }
    return false ;
  }

}
