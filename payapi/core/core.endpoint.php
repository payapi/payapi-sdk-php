<?php

namespace payapi ;

final class endpoint {

  private static
    $version                  =    'v1' ;

  public static function merchantSettings ( $publicId , $production = true ) {
    return self :: root ( $production ) . self :: $version . '/' . 'api' . '/' . 'merchantSettings' . '/' . ( string ) $publicId ;
  }

  public static function root ( $production ) {
    $staging = ( $production ) ? null : 'staging-' ;
    return 'https' . ':' . '//' . $staging . 'input' . '.' . 'payapi' . '.' . 'io' . '/' ;
  }
}
