<?php

namespace payapi ;

final class plugin {

  public
    $version                   =        '0.0.0' ;

  private
    $native                    =          false ,
    $config                    =          false ,
    $db                        =          false ;

  public function __construct ( $config ) {
    $this -> config = $config ;
  }

  public function loadLog () {
    //->
    return false ;
  }

  public function log ( $info ) {
    return true ;
  }

  public function config () {
    return $this -> config ;
  }

  public function session () {
    return false ;
  }

  public function db () {
    return false ;
  }

  public function customer () {
    return false ;
  }

  public function debugging () {
    if ( isset ( $this -> config [ 'debug' ] ) === true && $this -> config [ 'debug' ] === true ) {
      return true ;
    }
    return false ;
  }

  public function nativeVersion () {
    return $this -> version ;
  }

  public function version () {
    return $this -> version ;
  }

  public function staging () {
    if ( isset ( $this -> config [ 'staging' ] ) === true && $this -> config [ 'staging' ] === true ) {
      return true ;
    }
    return false ;
  }

  public function localized ( $localized ) {
    return $localized ;
  }

  public function __toString () {
    return $this -> version ;
  }


}
