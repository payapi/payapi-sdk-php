<?php

namespace payapi ;

use \payapi\cgi as cgi ;

class model extends helper {

  public
    $key                           =             false ;

  protected
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

  public function arguments ( $key = 0 ) {
    if ( ! isset ( $this -> arguments [ $key ] ) ) {
      return false ;
    }
    return $this -> arguments [ $key ] ;
  }

  public function publicKey () {
    return strtok ( $this -> key , '.' ) ;
  }

  protected function token () {
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

  protected function curling ( $url , $data = null , $return = 1 , $header = 0 , $ssl = 0 , $fresh = 1 , $noreuse = 1 , $timeout = 15 ) {
    $this -> resetCurl () ;
    $this -> debug ( $url ) ;
    $curlResponse = $this -> curl -> request ( $url , $data , $return , $header , $ssl , $fresh , $noreuse , $timeout ) ;
    //-> @NOTE @CARE merchant settings should use same schema, array ( "code" => "int" , "data" => "no_object" )
    // if ( $this -> validSchema ( 'response' , $curlResponse ) === true  ) {
    $schemaName = 'responseSettings' ;
    $curlSchema = $this -> getSchema ( $schemaName ) ;
    $validated = $this -> validSchema ( $schemaName , $curlResponse ) ;
    if ( is_array ( $validated ) === true ) {
      $this -> curlResponse = $validated ;
    } else {
      $this -> warning ( 'no valid' , 'response' ) ;
      $this -> curlResponse = array (
        "code" => $this -> error -> errorUnexpectedCurlResponse () ,
        "data" => 'curl schema error'
      ) ;
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
    return $this -> handler -> signature ( $this -> data -> get ( $key ) ) ;
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
