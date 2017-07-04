<?php

namespace payapi ;

use \payapi\cgi as cgi ;

class model extends helper {

  protected
    $key                           =             false ,
    $arguments                     =             false ,
    $adaptor                       =             false ,
    $framework                     =             false ,
    $archival                      =             false ,
    $status                        =             false ,
    $brand                         =             false ,
    $transaction                   =             false ;

  private
    $crypter                       =             false ,
    $serverSignature               =             false ,
    $token                         =             false ;

  protected function auto () {
    $this -> status = false ;
    $this -> framework = $this -> data -> get ( 'framework' , false ) ;
    $this -> data -> set ( 'framework' , false ) ;
    $this -> crypter = $this -> data -> get ( 'crypter' , false ) ;
    $this -> serverSignature = $this -> crypter -> uniqueServerSignature ( $this -> config ( 'payapi_public_id' ) ) ; //-> static signature
    $this -> token = $this -> crypter -> randomToken () ; //-> session unique
    $this -> key = $this -> crypter -> publicKey ( $this -> config ( 'payapi_public_id' ) ) ; //-> public key
    $this -> adaptor = new adaptor () ;
    //-> @TODO sanitize this in output
    $this -> addInfo ( 'sign' , $this -> serverSignature ) ;
    $this -> addInfo ( 'tk' , $this -> crypter -> encode ( $this -> token , false , true ) ) ;
    $this -> addInfo ( 'pk' , $this -> publicKey () ) ;
    $this -> brand = new branding () ;
    $this -> archival = new archival () ;
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

  public function setArchiveData ( $key , $data  , $type) {
    return $this-> archival -> setArchiveData ( $key , $this -> crypter -> encode ( $data , false ,true ) , $type ) ;
  }

  protected function getArchiveData ( $key , $type ) {
    return $this -> crypter -> decode ( $this -> archival -> getArchiveData ( $key , $type ) , false , true ) ;
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
    $adapted = array ( 'product' ) ;
    if ( in_array ( $schema , $adapted ) ) {
      $adatedData = $this -> adaptor -> $schema ( $data ) ;
      $this -> debug ( 'adapted : ' . json_encode ( $adatedData , true ) ) ;
    } else {
      $adatedData = $data ;
    }
    $this -> debug ( '[schema] ' . $schema ) ;
    return $this -> framework -> validSchema ( $schema , $adatedData ) ;
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
