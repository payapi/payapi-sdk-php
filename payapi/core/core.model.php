<?php

namespace payapi ;

use \payapi\cgi as cgi ;

class model extends engine {

  public
    $arguments                     =             false ;

  protected
    $info                          =             false ,
    $adaptor                       =             false ;

  private
    $crypter                       =             false ;

  public function auto () {
    $this -> adaptor = serializer :: adaptor ( $this -> config ( 'plugin' ) ) ;
    $this -> crypter = new crypter () ;
    $this -> info = $this -> serializer -> sign ( $this -> get ( 'info' ) ) ;
    $this -> arguments = $this -> get ( 'arguments' ) ;
  }

  public function arguments ( $key = 0 ) {
    if ( ! isset ( $this -> arguments [ $key ] ) ) {
      return false ;
    }
    return $this -> arguments [ $key ] ;
  }

  public function config ( $key = false ) {
    if ( $key === false  ) {
      return $this -> config ;
    }
    if ( isset ( $this -> config [ $key ] ) ) {
      return $this -> config [ $key ] ;
    } else {
      return false ;
    }
  }

  public function get ( $key = false ) {
    return $this -> data -> get ( $key ) ;
  }

  public function has ( $key ) {
    return $this -> data -> has ( $key ) ;
  }

  public function set ( $key , $value = false ) {
    return $this -> data -> set ( $key , $value ) ;
  }

  public function info () {
    return $this -> info ;
  }

  public function encode ( $decoded , $hash = false , $sanitized = false ) {
    return $this -> crypter -> encode ( $decoded , $hash , $sanitized ) ;
  }

  public function decode ( $encoded , $hash = false , $sanitized = false ) {
    return $this -> crypter -> decode ( $encoded , $hash , $sanitized ) ;
  }

  public function __destruct () {}


}
