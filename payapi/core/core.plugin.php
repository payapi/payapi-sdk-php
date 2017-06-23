<?php
namespace payapi ;

abstract class plugin extends engine {

  protected
    $error                     =   false ;

  public function auto () {
    //$this -> key ;
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
