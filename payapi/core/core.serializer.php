<?php

namespace payapi ;

class serializer {

  public static
    $single                     =   false ;

  protected
    $instance                   =   false ,
    $version                    = '0.0.1' ;

  protected function __construct () {
    $this -> instance = instance :: this () ;
  }

  //-> to core instance
  public function instance () {
    return md5 ( $this -> domain () ) ;
  }
  //-> to core instance
  public function domain () {
    //->
    return getenv ( 'SERVER_NAME' ) ;
  }

  public function getDomainFromUrl ( $url ) {
    $parsed = parse_url ( $url ) ;
    if ( isset ( $parsed [ 'host' ] ) === true ) {
      return $parsed [ 'host' ] ;
    }
    return false ;
  }

  public function timestamp () {
    return date ( 'Y-m-d H:i:s T' , time () ) ;
  }

  public function arrayToJson ( $array ) {
    $json = json_encode ( $array , true ) ;
    return $json ;
  }

  public function jsonToArray ( $json , $toArray = false ) {
    $array = json_decode ( $json , $toArray ) ;
    return $array ;
  }

  public function lenght ( $value , $lenght ) {
    if ( is_array ( $value ) !== true && is_object ( $value ) !== true ) {
      if ( preg_match ( "/^\d{" . $lenght . "}$/" , $int ) === true ) {
        return true ;
      }
    }
    return false ;
  }

  public static function cleanLogNamespace ( $route ) {
    return str_replace ( array ( 'payapi\\' , 'controller_' , 'model_' ) , null , $route ) ;
  }

  public function undefined () {
    return 'undefined' ;
  }

  public function __toString () {
    return $this -> version ;
  }

  public static function single () {
    if ( self :: $single === false ) {
      self :: $single = new self () ;
    }
    return self :: $single ;
  }


}
