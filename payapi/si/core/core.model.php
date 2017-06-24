<?php

namespace payapi ;

use \payapi\cgi as cgi ;

class model extends handler {

  public
    $arguments                     =             false ;

  protected
    $info                          =             false ,
    $validator                     =             false ,
    $adaptor                       =             false ,
    $curl                          =             false ,
    $curlResponse                  =             false ,
    $brand                         =             false ;

  private
    $crypter                       =             false ;

  protected function auto () {
    $this -> validator = $this -> data -> get ( 'validator' ) ;
    $this -> crypter = $this -> data -> get ( 'crypter' ) ;
    $this -> adaptor = serializer :: adaptor ( $this -> config ( 'plugin' ) ) ;
    $this -> info = $this -> serializer -> sign ( $this -> get ( 'info' ) ) ;
    $this -> brand = new branding () ;
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

  public function validSchema ( $schema , $data ) {
    if ( ! $schema || ! is_array ( $data ) ) {
      return false ;
    }
    $validated = $this -> validator -> validSchema ( $schema , $data ) ;
    return $validated ;
  }

  public function brand ( $key = false ) {
    if ( $key === false  ) {
      return $this -> brand ;
    }
    if ( isset ( $this -> brand [ $key ] ) ) {
      return $this -> brand [ $key ] ;
    } else {
      return false ;
    }
  }

  protected function curling ( $url , $data = null , $return = 1 , $header = 0 , $ssl = 0 , $fresh = 1 , $noreuse = 1 , $timeout = 15 ) {
    $this -> curlResponse = false ;
    $this -> debug ( $url ) ;
    if ( $this -> curl === false ) {
      $this -> load -> model ( 'curl' ) ;
      $this -> curl = new model_curl () ;
    }
    $this -> curlResponse = $this -> curl -> request ( $url , $data , $return , $header , $ssl , $fresh , $noreuse , $timeout ) ;
    return $this -> curlResponse ;
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
