<?php

namespace payapi ;

final class plugin {

  public
    $version                   =        '0.0.1' ;

  private
    $native                    =          false ,
    $config                    =          false ,
    $db                        =          false ;

  public function __construct ( $native ) {
    $this -> native = $native ;
    $this -> db = $this -> native -> get ( 'db' ) ;
    $this -> config = $this -> native -> get ( 'config' ) ;
    $this -> loadLog () ;
  }

  public function loadLog () {
    if ( $this -> debugging () === true ) {
      return new \Log ( strtolower ( __NAMESPACE__ ) . '.' . date ( 'YmdHis' , time () ) . '.' . $this -> default . '.' . 'log' ) ;
    }
    return false ;
  }

  public function log ( $info ) {
    return $this -> log ( $info ) ;
  }

  public function config () {
    return $this -> native -> get ( 'config' ) ;
  }

  public function session () {
    return $this -> native -> get ( 'session' ) ;
  }

  public function db () {
    return $this -> native -> get ( 'db' ) ;
  }

  public function customer () {
    return $this -> native -> get ( 'customer' ) ;
  }

  public function debugging () {
    return DEBUGGING ;
  }

  public function nativeVersion () {
    return VERSION ;
  }

  public function staging () {
    return STAGING ;
  }

  public function version () {
    return $this -> version ;
  }

  public function localized ( $localized ) {
    $resultCountry = $this -> db -> query ( "SELECT `country_id` FROM `" . DB_PREFIX . "country` WHERE `iso_code_2` = '" . $localized [ 'countryCode' ] . "'  LIMIT 1" ) ;
    if ( isset ( $resultCountry ) === true && $resultCountry -> num_rows > 0 ) {
      $resultZone = $this -> db -> query ( "SELECT `zone_id` FROM `" . DB_PREFIX . "zone` WHERE `country_id` = '" . $resultCountry -> row [ 'country_id' ] . "' LIMIT 1" ) ;
      if ( isset ( $resultZone ) === true && $resultZone -> num_rows > 0 ) {
        return array_merge (
          $localized ,
          array (
            'contry_id' => $resultCountry -> row [ 'country_id' ] ,
            'zone_id'   => $resultZone -> row [ 'zone_id' ]
          )
        ) ;
      }
    }
    return $localized ;
  }

  public function __toString () {
    return $this -> version ;
  }

}
