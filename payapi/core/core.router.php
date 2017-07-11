<?php

namespace payapi ;

class router {

  public static
    $single                     =   false ;

  private
    $root                       =   false ;

  protected function __construct () {
    $this -> root = $this -> parentDir ( __DIR__ ) . DIRECTORY_SEPARATOR ;
  }

  public function endPointLocalization ( $ip ) {
    $api = $this -> https () . $this -> staging () . 'input' . '.' . 'payapi' . '.' . 'io' . '/' . 'v1' . '/' . 'api' . '/' . 'fraud' . '/' . 'ipdata' . '/' . $ip ;
    return $api ;
  }

  public function endPointSettings ( $publicId ) {
    $api = $this -> https () . $this -> staging () . $this -> api () . 'merchantSettings' . '/' . $publicId ;
    return $api ;
  }

  private function api () {
    return 'input' . '.' . 'payapi' . '.' . 'io' . '/' . 'v1' . '/' . 'api' . '/' ;
  }

  private function https () {
    return 'https' . ':' . '//' ;
  }

  // update STAGING
  private function staging () {
    $route = ( STAGING ) ? 'staging' . '-' : null ;
    return $route ;
  }

  private function parentDir ( $dir ) {
    return str_replace ( DIRECTORY_SEPARATOR . basename ( $dir ) , null , $dir ) ;
  }

  private function root ( $key = false ) {
    if ( is_string ( $key ) === true ) {
      return $this -> root . $key . DIRECTORY_SEPARATOR ;
    }
    return $this -> root ;
  }

  private function routeCheck ( $dir ) {
    if ( is_dir ( $dir ) !== true ) {
      return mkdir ( $dir , '0755' ) ;
    }
    return true ;
  }

  private function routeCore () {
    return $this -> root ( 'core' ) ;
  }

  private function routeCommand () {
    return $this -> root ( 'command' ) ;
  }

  private function routeCache () {
    return $this -> root ( 'cache' ) ;
  }

  private function routeSchema () {
    return $this -> root ( 'schema' ) ;
  }

  public function routeDebug () {
    return $this -> root ( 'debug' ) ;
  }

  private function routePlugin () {
    return $this -> root ( 'plugin' ) ;
  }

  public function plugin ( $key ) {
    $plugin = $this -> routePlugin () . 'plugin' . '.' . $key . '.' . 'php' ;
    if ( is_file ( $plugin ) === true ) {
      return $plugin ;
    }
    return false ;
  }

  public function command ( $key ) {
    $controller = $this -> routeCommand () . 'command' . '.' . $key . '.' . 'php' ;
    if ( is_file ( $controller ) === true ) {
      return $controller ;
    }
    return false ;
  }

  public function schema ( $key ) {
    $schema = $this -> routeSchema () . 'schema' . '.' . $key . '.' . 'json' ;
    if ( is_file ( $schema ) === true ) {
      return $schema ;
    }
    return false ;
  }

  public function cache ( $type , $key ) {
    $cacheFile = $this -> routeCache () . $type . DIRECTORY_SEPARATOR . 'cache' . '.' . $key . '.' . 'data' ;
    return $cacheFile ;
  }

  public static function single () {
    if ( self :: $single === false ) {
      self :: $single = new self () ;
    }
    return self :: $single ;
  }


}
