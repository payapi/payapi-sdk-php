<?php
namespace payapi ;

//-> @TODO filter/validate store inputs

final class adaptor extends helper {

  public
    $key                       =      false ;

  private
    $plugin                    =      false ,
    $default                   =  'default' ,
    $transaction               =      false ,
    $consumer                  =      false ;

  protected function auto () {
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

  public function consumer ( $consumer ) {
    return $this -> plugin -> consumer ( $consumer ) ;
  }

  public function address ( $address ) {
    return $this -> plugin -> address ( $address ) ;
  }


}
