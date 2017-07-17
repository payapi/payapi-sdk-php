<?php

namespace payapi ;

class serializer {

  public static
    $single                     =   false ;

  protected
    $instance                   =   false ,
    $version                    = '0.0.1' ;

  protected function __construct () {
    $this -> instance = instance :: this () ;
    $this -> domain = instance :: domain () ;
    $this -> config = config :: single () ;
  }

  public function endPointLocalization ( $ip ) {
    $api = $this -> https () . $this -> staging () . 'input' . '.' . 'payapi' . '.' . 'io' . '/' . 'v1' . '/' . 'api' . '/' . 'fraud' . '/' . 'ipdata' . '/' . $ip ;
    return $api ;
  }

  public function endPointSettings ( $publicId ) {
    $api = $this -> https () . $this -> staging () . $this -> api () . 'merchantSettings' . '/' . $publicId ;
    return $api ;
  }

  public function endPointInstantBuy ( $publicId ) {
    $api = $this -> https () . $this -> staging () . $this -> webshop () . $publicId . '/' ;
    return $api ;
  }

  private function webshop () {
    return 'input' . '.' . 'payapi' . '.' . 'io' . '/' . 'v1' . '/' . 'webshop' . '/' ;
  }

  private function api () {
    return 'input' . '.' . 'payapi' . '.' . 'io' . '/' . 'v1' . '/' . 'api' . '/' ;
  }

  private function https () {
    return 'https' . ':' . '//' ;
  }

  private function staging () {
    $route = ( ( $this -> config -> staging () === true ) ? 'staging' . '-' : null ) ;
    return $route ;
  }

  public function instantPaymentUrlEncode ( $url ) {
    return urlencode ( html_entity_decode ( $url ) ) ;
  }

  private function microstamp () {
    return  microtime ( true ) ;
  }

  public function getDomainFromUrl ( $url ) {
    $parsed = parse_url ( $url ) ;
    if ( isset ( $parsed [ 'host' ] ) === true ) {
      return $parsed [ 'host' ] ;
    }
    return false ;
  }

  public function timestamp () {
    return date ( 'Y-m-d H:i:s T' , time () ) ;
  }

  public function options ( $options ) {
    $optionsSerialized = '' ;
    foreach ( $options as $option => $value ) {
      $optionsSerialized .= ( ( $optionsSerialized !== '' ) ? '&' : null ) . $option . '=' . $value ;
    }
    return $optionsSerialized ;
  }

  public function percentage ( $total , $part ) {
    return round ( ( ( $part * 100 ) / ( $total - $part ) ) , 0 ) ;
  }

  public function arrayToJson ( $array ) {
    $json = json_encode ( $array , true ) ;
    return $json ;
  }

  public function jsonToArray ( $json , $toArray = false ) {
    $array = json_decode ( $json , $toArray ) ;
    return $array ;
  }

  public function lenght ( $value , $lenght ) {
    if ( is_array ( $value ) !== true && is_object ( $value ) !== true ) {
      if ( preg_match ( "/^\d{" . $lenght . "}$/" , $int ) === true ) {
        return true ;
      }
    }
    return false ;
  }

  public static function cleanLogNamespace ( $route ) {
    return str_replace ( array ( 'payapi\\' , 'controller_' , 'model_' ) , null , $route ) ;
  }

  public function undefined () {
    return 'undefined' ;
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
