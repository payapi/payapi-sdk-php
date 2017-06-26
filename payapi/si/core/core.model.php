<?php

namespace payapi ;

use \payapi\cgi as cgi ;

class model extends helper {

  protected
    $key                           =             false ,
    $arguments                     =             false ,
    $info                          =             false ,
    $adaptor                       =             false ,
    $handler                       =             false ,
    $curl                          =             false ,
    $curlResponse                  =             false ,
    $status                        =             false ,
    $brand                         =             false ;

  private
    $crypter                       =             false ,
    $token                         =             false ;

  protected function auto () {
    $this -> status = false ;
    $this -> handler = $this -> data -> get ( 'handler' ) ;
    $this -> data -> set ( 'handler' , false ) ;
    $this -> crypter = $this -> data -> get ( 'crypter' ) ;
    $this -> token = $this -> crypter -> randomToken () ; //-> unique
    $this -> key = $this -> crypter -> publicKey ( $this -> config ( 'payapi_public_id' ) ) ;
    $this -> info = array_merge (
      $this -> get ( 'info' ) ,
      array (
        "___pk" => $this -> publicKey ()
      )
    ) ;
    $this -> set ( 'info' , $this -> info ) ;
    $this -> adaptor = serializer :: adaptor ( $this -> config ( 'plugin' ) ) ;
    $this -> info = $this -> handler -> signature ( $this -> get ( 'info' ) ) ;
    $this -> brand = new branding () ;
    $this -> arguments = $this -> get ( 'arguments' ) ;
    // check valid
  }

  protected function arguments ( $key = 0 ) {
    return $this -> handler -> arguments ( $key ) ;
  }

  private function publicKey () {
    return strtok ( $this -> key , '.' ) ;
  }

  protected function validPublicId ( $publicKey ) {
    if ( is_string ( $publicKey ) === true && $publicKey === $this -> publicKey () ) {
      return true ;
    }
    return false ;
  }

  private function token () {
    return $this -> token ;
  }

  public function status () {
    return $this -> status ;
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

  public function validSchema ( $schema = array () , $data ) {
    return $this -> handler -> validSchema ( $schema , $data ) ;
  }

  public function getSchema ( $schema ) {
    return $this -> handler -> getSchema ( $schema ) ;
  }

  public function brand ( $key = false ) {
    return $this -> get ( 'brand' ) ;
  }

  protected function knock () {
    return $this -> handler -> knock () ;
  }

  protected function curling ( $url , $data = null , $return = 1 , $header = 0 , $ssl = 0 , $fresh = 1 , $noreuse = 1 , $timeout = 15 ) {
    $this -> resetCurl () ;
    $curlResponse = $this -> curl -> request ( $url , $data , $return , $header , $ssl , $fresh , $noreuse , $timeout ) ;
    $validated = $this -> validSchema ( 'response.standard' , $curlResponse ) ;
    if ( is_array ( $validated ) === true ) {
      $this -> curlResponse = $validated ;
    } else {
      $this -> warning ( 'no valid' , 'response' ) ;
      $this -> curlResponse = $this -> curl -> curlErrorUnexpectedCurlResponse () ;
    }
    return $this -> curlResponse ;
  }

  private function resetCurl () {
    if ( $this -> curl === false ) {
      $this -> curl = new curling () ;
    } else {
      $this -> curlResponse = false ;
    }
  }

  public function get ( $key = false ) {
    return $this -> signOutput ( $this -> data -> get ( $key ) ) ;
  }

  protected function signOutput ( $outputData ) {
    return $this -> handler -> signature ( $outputData ) ;
  }

  public function has ( $key ) {
    return $this -> data -> has ( $key ) ;
  }

  public function set ( $key , $value = false ) {
    return $this -> data -> set ( $key , $value ) ;
  }

  public function encode ( $decoded , $hash = false , $sanitized = false ) {
    return $this -> crypter -> encode ( $decoded , $hash , $sanitized ) ;
  }

  public function decode ( $encoded , $hash = false , $sanitized = false ) {
    return $this -> crypter -> decode ( $encoded , $hash , $sanitized ) ;
  }

  public function __destruct () {}


}
