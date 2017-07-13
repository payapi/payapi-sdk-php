<?php

namespace payapi ;

final class adaptor extends helper {

  private
    $plugin                    =     false ,
    $adapt                     =     false ,
    $entity                    =     false ,
    $adaptors                  =    array (
      "native"                             ,
      "opencart2"                          ,
    )                                      ;

  protected
    $log                       =     false ;

  protected function ___autoload ( $native , $plugin ) {
    //->
    if ( in_array ( $plugin , $this -> adaptors ) !== false ) {
      $this -> plugin = $plugin ;
    } else {
      $this -> error ( '[adaptor] not available' , 'warning' ) ;
      $this -> plugin = 'native' ;
    }
    $this -> debug ( '[plugin] ' . $this -> plugin ) ;
    $this -> plugin ( $native ) ;
  }

  public function log ( $info ) {
    return $this -> adapt -> log ( $info ) ;
  }

  public function session () {
    return $this -> adapt -> session () ;
  }

  public function db () {
    return $this -> adapt -> db () ;
  }

  public function customer () {
    return $this -> adapt -> customer () ;
  }

  public function debugging () {
    return $this -> adapt -> debugging () ;
  }

  public function version () {
    return $this -> adapt -> version () ;
  }

  public function settings () {
    return $this -> adapt -> settings () ;
  }

  public function publicId () {
    return $this -> adapt -> publicId () ;
  }

  public function apiKey () {
    return $this -> adapt -> apiKey () ;
  }

  public function localized ( $localized ) {
    return $this -> adapt -> localized ( $localized ) ;
  }

  private function plugin ( $native ) {
    $pluginRoute = $this -> route -> plugin ( $this -> plugin ) ;
    if ( is_string ( $pluginRoute ) === true ) {
      require_once ( $pluginRoute ) ;
      $this -> adapt = new plugin ( $native ) ;
    } else {
      $this -> error ( 'cannot load plugin' , 'fatal' ) ;
    }
  }

  public function __toString () {
    return $this -> version () ;
  }


}
