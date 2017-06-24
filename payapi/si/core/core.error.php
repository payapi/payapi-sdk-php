<?php

namespace payapi ;

final class error {

  public static
    $single                   =   false ;

  private
    $data                     =   array (
      "error"                 =>  false ,
      "fatal"                 =>  false
    ) ;

  protected function __contruct () {}

  public function get () {
    return $this -> data [ $key ] ;
  }

  public function has ( $key ) {
    return isset ( $this -> data [ $key ] ) ;
  }

  public function set ( $errors , $key = 'error' ) {
    if ( is_array ( $errors ) ) {
      foreach ( $errors as $error ) {
        $this -> set ( $error , $value ) ;
      }
    }
    if ( ! preg_match ( '~^[0-9a-z]+$~i' , $key ) || ! in_array ( $key , array ( 'error' , 'fatal' ) ) ) {
      $this -> set ( 'unvalid <@error>' ) ;
      return false ;
    }
    $this -> data [ $key ] [] = $errors ;
  }

  public function errorNoValidAccount () {
    return 403 ;
  }

  public function errorUnexpectedCurlResponse () {
    return 406 ; // not acceptable
  }

  public function errorUnexpectedCurlSchema () {
    return 412 ; // precondition failed
  }

  public function errorNoValidJsonPayload () {
    return 415 ; // unsupported media type
  }

  public function errorAppSchemaNoValid () {
    return 406 ;
  }

  public function errorCallbackNoRequest () {
    return 404 ;
  }

  public function errorAccessNoAuthorized () {
    return 403 ;
  }

  public function errorAppNoValidCommands () {
    return 400 ;
  }

  public function errorMaintenance () {
    return 503 ;
  }

  public function appError () {
    return 600 ;
  }

  public function __toString () {
    if ( ! is_array ( $this -> data ) )
      return 'null' ;
    return json_encode ( $this -> data , true ) ;
  }

  public static function single () {
    if ( ! self :: $single ) {
      self :: $single = new self ;
    }
    return self :: $single ;
  }


}
