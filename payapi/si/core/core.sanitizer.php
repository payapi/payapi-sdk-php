<?php

namespace payapi ;

final class sanitizer {

  protected
    $version                  =   '0.0.1' ;

  public function sanitizeFromSchema ( $schema , $data ) {
    $diffs = array_diff_key ( $data , $schema [ '___schema___' ] ) ;
    foreach ( $diffs as $diff => $value ) {
      if ( $diff != 'numberOfInstallments' ) {
        unset ( $data [ $diff ] ) ;
      }
    }
    return $data ;
  }

  public function outputData ( $data ) {
    /*
    if ( isset ( $data [ '___extradata' ] ) === true ) {
      unset ( $data [ '___extradata' ] ) ;
    }*/
    return $data ;
  }

  public function parseDomain ( $url ) {
    $parsed = parse_url ( $url ) ;
    if ( ! isset ( $parsed [ 'host' ] ) )
      return false ;
    return $parsed [ 'host' ] ;
  }

  public function __toString () {
    return serializer :: toString ( $this -> version ) ;
  }


}
