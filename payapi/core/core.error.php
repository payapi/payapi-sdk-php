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
      $this -> error ( 'unvalid <@error>' ) ;
      return false ;
    }
    $this -> data [ $key ] [] = $errors ;
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
