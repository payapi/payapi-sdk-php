<?php

namespace payapi ;

class error {

  public static
    $single                  =    false ;

  private
    $error                   =    false ,
    $labels                  =    array (
      "fatal" ,
      "warning" ,
      "notice" ,
      "undefined"
    ) ;

  private function construct () {}

  public function add ( $error , $label ) {
    $checkedLabel = ( in_array ( $label , $this -> labels ) === true ) ? $label : 'undefined' ;
    $this -> error [ $checkedLabel ] [] = $error ;
    return true ;
  }

  public function alert () {
    return $this -> error ;
  }

  public function undefined () {
    return 600 ;
  }

  public function notAcceptable () {
    return 406 ;
  }

  public function notImplemented () {
    return 501 ;
  }

  public function badRequest () {
    return 400 ;
  }

  public function notFound () {
    return 404 ;
  }

  public function timeout () {
    return 504 ;
  }

  public function notValidKnock () {
    return 404 ;
  }

  public function unexpectedKnock () {
    return 400 ;
  }

  public function notValidSchema () {
    return 400 ;
  }

  public function notValidMethod () {
    return 405 ;
  }

  public function notValidSsl () {
    return 505 ;
  }

  public function booBoo () {
    return 600 ;
  }

  public static function single () {
    if ( self :: $single === false ) {
      self :: $single = new self () ;
    }
    return self :: $single ;
  }


}
