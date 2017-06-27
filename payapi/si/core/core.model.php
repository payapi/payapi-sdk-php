<?php

namespace payapi ;

use \payapi\cgi as cgi ;

class model extends helper {

  protected
    $key                           =             false ,
    $arguments                     =             false ,
    $adaptor                       =             false ,
    $framework                     =             false ,
    $curl                          =             false ,
    $curlResponse                  =             false ,
    $status                        =             false ,
    $brand                         =             false ;

  private
    $crypter                       =             false ,
    $token                         =             false ;

  protected function auto () {
    $this -> status = false ;
    $this -> framework = $this -> data -> get ( 'framework' , false ) ;
    $this -> data -> set ( 'framework' , false ) ;
    $this -> crypter = $this -> data -> get ( 'crypter' , false ) ;
    $this -> token = $this -> crypter -> randomToken () ; //-> unique
    $this -> key = $this -> crypter -> publicKey ( $this -> config ( 'payapi_public_id' ) ) ;
    //$this -> adaptor = serializer :: adaptor ( $this -> config ( 'plugin' ) ) ;
    $this -> adaptor = new plugin () ;
    $this -> addInfo ( 'pk' , $this -> key ) ;
    $this -> brand = new branding () ;
    $this -> addInfo ( 'brand' , $this -> brand -> getBrandKey () ) ;
    $this -> arguments = $this -> get ( 'arguments' ) ;
    // check valid
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
    return $this -> framework -> validSchema ( $schema , $data ) ;
  }

  public function getSchema ( $schema ) {
    return $this -> framework -> getSchema ( $schema ) ;
  }

  public function brand ( $key = false ) {
    return $this -> get ( 'brand' ) ;
  }

  public function get ( $key = false , $signed = true ) {
    if ( $signed !== false ) {
      return $this -> signOutput ( $this -> data -> get ( $key ) ) ;
    } else {
      return $this -> data -> get ( $key ) ;
    }
  }
  //-> @NOTE @FIXME this shoudl not to be public
  public function signOutput ( $outputData ) {
      return $this -> framework -> signature ( $outputData ) ;
  }

  protected function getDecodedApiKey ( $encodedApiKey ) {
    return $this -> decode ( $encodedApiKey , $this -> config ( 'payapi_public_id' ) , true ) ;
  }

  public function has ( $key ) {
    return $this -> data -> has ( $key ) ;
  }

  public function set ( $key , $value = false ) {
    return $this -> data -> set ( $key , $value ) ;
  }

  protected function encode ( $decoded , $hash = false , $sanitizer = false ) {
    return $this -> crypter -> encode ( $decoded , $hash , $sanitizer ) ;
  }

  protected function decode ( $encoded , $hash = false , $sanitizer = false ) {
    return $this -> crypter -> decode ( $encoded , $hash , $sanitizer ) ;
  }

  public function __destruct () {}


}
