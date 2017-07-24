<?php

namespace payapi ;

final class adaptor {

  public static
    $single                    =     false ;

  private
    $plugin                    =     false ,
    $adapt                     =     false ,
    $error                     =     false ,
    $route                     =     false ,
    $config                    =     false ,
    $debug                     =     false ,
    $adaptors                  =    array (
      "native"                             ,
      "opencart2"                          ,
    )                                      ;

  protected
    $log                       =     false ;

  private function __construct ( $adapt , $plugin ) {
    if ( self :: $single !== false ) {
      return self :: $single ;
    }
    $this -> error = error :: single () ;
    $this -> route = router :: single () ;
    //->
    if ( in_array ( $plugin , $this -> adaptors ) !== false ) {
      $this -> plugin = $plugin ;
    } else {
      $this -> error ( '[adaptor] not available' , 'warning' ) ;
      $this -> plugin = 'native' ;
    }
    $pluginRoute = $this -> route -> plugin ( $this -> plugin ) ;
    if ( is_string ( $pluginRoute ) === true ) {
      require ( $pluginRoute ) ;
      $this -> adapt = plugin :: single ( $adapt ) ;
      $this -> config = config :: single ( $this -> config () ) ;
      $this -> debug = debug :: single ( $this -> debug () ) ;
    } else {
      $this -> debug -> add ( '[plugin] 404' , 'error' ) ;
    }
  }

  private function validated () {
    return $this -> adapt -> validated () ;
  }

  public function product ( $product ) {
    return $this -> adapt -> product ( $product ) ;
  }

  public function payment ( $payment ) {
    return $this -> adapt -> payment ( $payment ) ;
  }

  public function instantPayment ( $payment ) {
    return $this -> adapt -> instantPayment ( $payment ) ;
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

  public function debug () {
    return $this -> adapt -> debug () ;
  }

  public function staging () {
    return $this -> adapt -> staging () ;
  }

  public function version () {
    return $this -> adapt -> version () ;
  }

  private function config () {
    $config = array (
      "debug"    => $this -> debug () ,
      "staging"  => $this -> staging ()
    ) ;
    return $config ;
  }

  public function localized ( $localized ) {
    return $this -> adapt -> localized ( $localized ) ;
  }

  public static function single ( $adapt , $plugin ) {
    if ( self :: $single === false ) {
      self :: $single = new self ( $adapt , $plugin ) ;
    }
    return self :: $single ;
  }

  public function __toString () {
    return ( string ) $this -> adapt ;
  }


}
