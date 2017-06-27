<?php
namespace payapi ;

final class plugin extends helper {

  protected
    $error                     =   false ;

  private
    $key                       =   false ,
    $adaptor                   =   false ;

  public function auto () {
    /*
    $this -> key = ( is_string ( $this -> config ( 'plugin' ) ) === true ) ? $this -> config ( 'plugin' ) : 'default' ;

    }
    //$this -> key ;
    */
  }

  public function info () {
    return $this -> key ;
  }

  public function product ( $product ) {
    return $this -> adaptor -> product ( $product ) ;
  }

  public function order ( $order ) {
    return $this -> adaptor -> order ( $product ) ;
  }


}
