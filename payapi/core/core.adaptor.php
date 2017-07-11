<?php

namespace payapi ;

final class adaptor extends helper {

  private
    $plugin                    =     false ,
    $entity                    =     false ;

  protected
    $log                       =     false ;

  protected function ___autoload ( $entity , $native = false ) {
    $this -> entity = $entity ;
    $this -> plugin ( $native ) ;
    $this -> debug ( '[plugin] ' . $this -> entity -> get ( 'plugin' ) ) ;
    $this -> log = $this -> plugin -> loadLog () ;
  }

  public function log ( $info ) {
    //-> to adaptor
    if ( $this -> log !== false ) {
      return $this -> adaptor -> log ( $info ) ;
    }
    return false ;
  }

  public function config () {
    return $this -> plugin -> config () ;
  }

  public function session () {
    return $this -> plugin -> session () ;
  }

  public function db () {
    return $this -> plugin -> db () ;
  }

  public function customer () {
    return $this -> plugin -> customer () ;
  }

  public function debugging () {
    return $this -> plugin -> debugging () ;
  }

  public function version () {
    return $this -> plugin -> version () ;
  }

  public function settings () {
    return $this -> plugin -> settings () ;
  }

  public function publicId () {
    return $this -> plugin -> publicId () ;
  }

  public function apiKey () {
    return $this -> plugin -> apiKey () ;
  }


  public function localized ( $localized ) {
    return $this -> plugin -> localized ( $localized ) ;
  }

  private function plugin ( $native ) {
    $plugin = ( ( $this -> entity -> has ( 'plugin' ) === true ) ? $this -> plugin = $this -> entity -> get ( 'plugin' ) : $this -> default ) ;
    $pluginRoute = $this -> route -> plugin ( $plugin ) ;
    if ( is_string ( $pluginRoute ) !== true && $plugin != $this -> deafult ) {
      $this -> error ( 'cannot load selected plugin' ) ;
      $this -> entity -> set ( 'plugin' , $this -> default ) ;
      $pluginRoute = $this -> route -> plugin ( $this -> default ) ;
    }
    $object = ( $this -> entity -> get ( 'plugin' ) === $this -> default ) ? $this -> entity : $native ;
    require ( $pluginRoute ) ;
    $this -> plugin = new plugin ( $object ) ;
  }

  public function __toString () {
    return $this -> version () ;
  }


}
