<?php

namespace payapi ;

final class sanitizer {

  public static
    $single                     =   false ;

  protected
    $version                  =   '0.0.0' ;

  public function sanitizeFromSchema ( $schema , $data ) {
    $diffs = array_diff_key ( $data , $schema [ '___schema___' ] ) ;
    foreach ( $diffs as $diff => $value ) {
      if ( $diff != 'numberOfInstallments' ) {
        unset ( $data [ $diff ] ) ;
      }
    }
    return $data ;
  }

  public function parseDomain ( $url ) {
    $parsed = parse_url ( $url ) ;
    if ( ! isset ( $parsed [ 'host' ] ) )
      return false ;
    return $parsed [ 'host' ] ;
  }

  public function sanitizeStream ( $stream ) {
    return filter_var ( $url , FILTER_SANITIZE_STRING , FILTER_FLAG_NO_ENCODE_QUOTES ) ;
  }

  public function sanitizedQuotes ( $url ) {
    return filter_var ( $url , FILTER_SANITIZE_MAGIC_QUOTES ) ;
  }

  public function sanitizedSpecialChars ( $string ) {
    return filter_var ( $string , FILTER_SANITIZE_SPECIAL_CHARS ) ;
  }

  public function sanitizedFullSpecialChars ( $string ) {
    return filter_var ( $string , FILTER_SANITIZE_FULL_SPECIAL_CHARS , FILTER_FLAG_NO_ENCODE_QUOTES ) ;
  }

  public function sanitizedInt ( $int ) {
    return filter_var ( $int , FILTER_SANITIZE_NUMBER_INT ) ;
  }
  
  public function __toString () {
    return $this -> version ;
  }

  public static function single () {
    if ( self :: $single === false ) {
      self :: $single = new self () ;
    }
    return self :: $single ;
  }


}
