<?php

namespace payapi ;
//-> SDK config
final class config {

  public static
    $single                     =      false ;

  private
    $settings                   =      false ,
    $instance                   =      false ,
    $plugins                    =      array (
      "native"                               ,
      "opencart2"                            ,
    )                                        ,
    $schema                     =      array (
      "debug"                   =>     false ,
      "staging"                 =>     false ,
      "plugin"                  =>   'native'
    )                                        ;

  private function __construct ( $config ) {
    $this -> instance = instance :: this () ;
    foreach ( $this -> schema as $key => $value ) {
      if ( isset ( $config [ $key ] ) ===  true ) {
        if ( $key === 'plugin' ) {
          if ( is_string ( $config [ $key ] ) === true && in_array ( $config [ $key ] , $this -> plugins ) === true ) {
            $this -> settings [ $key ] = $config [ $key ] ;
          } else {
            $this -> settings [ $key ] == $value ;
          }
        } else {
          if ( $config [ $key ] === true ) {
            $this -> settings [ $key ] = true ;
          } else {
            $this -> settings [ $key ] == $value ;
          }
        }
      }
    }
  }

  public function __call ( $key , $arguments = array () ) {
    if ( $arguments === array () && is_string ( $key ) === true && isset ( $this -> settings [ $key ] ) === true ) {
      return $this -> settings [ $key ] ;
    }
    return null ;
  }

  public static function single ( $config = array () ) {
    if ( self :: $single === false ) {
      self :: $single = new self ( $config ) ;
    }
    return self :: $single ;
  }

  public function __toString () {
    return $this -> data ;
  }

}
