<?php
namespace payapi ;

use \Firebase\JWT\JWT;

final class crypter {

  protected
    $crypted                   =    false ;

  private
    $mode                      =  'HS256' ,
    $hash                      =    false ,
    $prefix                    =    false ;

  public function __construct ( $hash , $mode = false ) {
    if ( is_string ( $hash ) ) $this -> hash = $hash ;
    if ( is_string ( $mode ) ) $this -> mode = $mode ;
    try {
      $this -> prefix = strtok ( JWT :: encode ( ' ' , $this -> hash ) , '.' ) ;
    } catch ( Exception $e ) {
      $this -> error ( 'JWT is not loaded' ) ;
    }
  }

  public function decode ( $encoded , $hash = false , $crypted = false ) {
    $hash_update = ( $hash != false ) ? $hash : $this -> hash ;
    $this -> sanitized = ( $crypted !== false ) ? true : false ;
    $build = $this -> build ( $encoded ) ;
    try {
      $decoded = JWT :: decode ( $build , $hash_update , array ( $this -> mode ) ) ;
    } catch ( Exception $e ) {
      $this -> error ( 'cannot decode provided data : ' . json_encode ( $e -> getMessage () ) ) ;
      $decoded = false ;
    }
    return $decoded ;
  }

  public function encode ( $decoded , $hash = false , $crypted = false ) {
    $this -> sanitized = ( $crypted !== false ) ? true : false ;
    $hash_update = ( $hash != false ) ? $hash : $this -> hash ;
    try {
      $encoded = $this -> clean ( JWT :: encode ( $decoded , $hash_update , $this -> mode ) ) ;
    } catch ( Exception $e ) {
      $this -> error ( 'cannot encode provided data' ) ;
      $encoded = false ;
    }
    return $encoded ;
  }

  public function sanitize ( $status = true ) {
    if ( $status != true ) $this -> sanitize = false ;
  }

  protected function clean ( $data ) {
    $payload = ( ! $this -> sanitized ) ? $data : str_replace ( $this -> prefix . '.' , null , $data ) ;
    return $payload ;
  }

  protected function build ( $data ) {
    $jwt = ( ! $this -> sanitized ) ? $data : $this -> prefix . '.' . $data ;
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

  private function error ( $error ) {
    // @TODO
    return true ;
  }


}
