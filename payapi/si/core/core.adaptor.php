<?php
namespace payapi ;

final class adaptor extends helper {

  public
    $key                       =     false ;

  private
    $plugin                    =     false ,
    $default                   = 'default' ;

  public function auto () {
    if ( is_string ( $this -> config ( 'plugin' ) ) === true && $this -> config ( 'plugin' ) != $this -> default ) {
      if ( router :: adaptorPlugin ( $this -> config ( 'plugin' ) ) === true ) {
        $this -> key = $this -> config ( 'plugin' ) ;
      } else {
        $this -> warning ( 'defaulted' , 'plugin' ) ;
      }
    } else
    if ( $this -> key === false && router :: adaptorPlugin ( $this -> default ) === true ) {
      $this -> key = $this -> default ;
    }
    $this -> plugin = new plugin () ;
    $this -> addInfo ( 'plugin_v' , ( string ) $this -> plugin ) ;
    $this -> addInfo ( 'plugin' , $this -> key ) ;
    $this -> debug ( '[adaptor] ' . $this -> key ) ;
  }

  public function getKey () {
    return $this -> key ;
  }

  public function product ( $product ) {
    return $this -> plugin -> product ( $product ) ;
  }

  public function order ( $order ) {
    return $this -> plugin -> order ( $product ) ;
  }

  public function customer ( $customer ) {
    return $this -> plugin -> customer ( $customer ) ;
  }

  public function address ( $address ) {
    return $this -> plugin -> address ( $address ) ;
  }


}
