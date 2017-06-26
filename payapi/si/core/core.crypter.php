<?php
namespace payapi ;

use \Firebase\JWT\JWT;

final class crypter {

  protected
    $version                   =  '0.0.1' ,
    $error                     =    false ,
    $publicKey                 =    false ;

  private
    $mode                      =  'HS256' ,
    $hash                      =    false ,
    $prefix                    =    false ;

  public function __construct ( $hash , $mode = false ) {
    if ( is_string ( $hash ) === true ) $this -> hash = $hash ;
    if ( is_string ( $mode ) === true ) $this -> mode = $mode ;
    try {
      $this -> prefix = strtok ( JWT :: encode ( ' ' , $this -> hash ) , '.' ) ;
    } catch ( Exception $e ) {
      $this -> error ( 'JWT is not loaded' ) ;
    }
  }

  public function decode ( $encoded , $hash = false , $crypted = false ) {
    $this -> sanitized = ( $crypted !== false ) ? true : false ;
    $hash_update = ( is_string ( $hash ) === true ) ? $hash : $this -> hash ;
    $build = $this -> build ( $encoded ) ;
    try {
      $decoded = JWT :: decode ( $build , $hash_update , array ( $this -> mode ) ) ;
    } catch ( Exception $e ) {
      $this -> error ( 'cannot decode payload : ' . json_encode ( $e -> getMessage () ) ) ;
      $decoded = false ;
    }
    return $decoded ;
  }

  public function encode ( $decoded , $hash = false , $crypted = false ) {
    $this -> sanitized = ( $crypted !== false ) ? true : false ;
    $hash_update = ( is_string ( $hash ) === true ) ? $hash : $this -> hash ;
    try {
      $encoded = $this -> clean ( JWT :: encode ( $decoded , $hash_update , $this -> mode ) ) ;
    } catch ( Exception $e ) {
      $this -> error ( 'cannot encode payload' ) ;
      $encoded = false ;
    }
    return $encoded ;
  }

  public function sanitize ( $status = true ) {
    if ( $status !== true ) $this -> sanitize = false ;
  }

  protected function clean ( $data ) {
    $payload = ( $this -> sanitized !== true ) ? $data : str_replace ( $this -> prefix . '.' , null , $data ) ;
    return $payload ;
  }

  protected function build ( $data ) {
    $jwt = ( $this -> sanitized !== true ) ? $data : $this -> prefix . '.' . $data ;
    return $jwt ;
  }

  public function decodejsonized ( $encodejsonized ) {
    $decodejsonized = json_decode ( $this -> decode ( $encodejsonized ) ) ;
    return $decodejsonized ;
  }

  public function encodejsonized ( $decodejsonized ) {
    $encodejsonized = $this -> encode ( json_encode ( $decodejsonized ) ) ;
    return $encodejsonized ;
  }

  public function uniqueServerToken () {
    return $this -> hashed ( getenv ( 'SERVER_SIGNATURE' ) , getenv ( 'SERVER_NAME' ) ) ;
  }

  public function uniqueAccessToken () {
    //->
    $signature = '...' ;
    return $this -> hashed ( $signature , 'access' ) ;
  }

  public function randomToken () {
    return bin2hex ( mcrypt_create_iv ( 22, MCRYPT_DEV_URANDOM ) ) ;
  }
  //->
  private function getModelToken ( $token , $hash ) {
    return $this -> decode ( $token , $this -> hashed ( $token , md5 ( $hash ) ) , true ) ;
  }

  public function publicKey ( $public ) {
    if ( $this -> publicKey === false ) {
      $this -> publicKey = $this -> encode ( $this -> hashed ( $public , $public . md5 ( $public ) ) , false , true ) ;
    }
    return $this -> publicKey ;
  }

  public function privateHash ( $hash ) {
    return $this -> hashed ( $hash . md5 ( $hash ) , $hash ) ;
  }

  private function hashed ( $token , $hash ) {
    return hash ( 'haval256,5' , $token . md5 ( $token . md5 ( $hash ) ) ) ;
  }

  private function hashedRandom ( $token , $hash ) {
    return $this -> hashed ( $token , $hash ) . '.' . $this -> hashed ( $this -> randomToken () ) . $this -> hashed ( $this -> randomToken () ) . '.' . $this -> publicKey ( $token ) ;
  }
  //-> debug errors?
  public function error (  $errors = false ) {
    if ( $errors === false ) {
      return $this -> error ;
    } else
    if ( is_array ( $errors ) === true ) {
      foreach ( $erros as $error ) {
        $this -> error ( $error ) ;
      }
    }
    $this -> error [] = ( string ) $errors ;
  }

  public function __toString () {
    return serializer :: toString ( $this -> version ) ;
  }


}
