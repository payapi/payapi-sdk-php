<?php

namespace payapi ;

class transaction {

  private
    $order                =  false ,
    $products             =  false ,
    $consumer             =  false ,
    $shippingAddres       =  false ,
    $callbacks            =  false ,
    $returns              =  false ,
    $transaction          =  false ;

  protected function auto () {
    $this -> products = array () ;
  }

  public function get () {
    $this -> buildOrder () ;
    $this -> buildTransaction () ;
    if ( $this -> validate () === true ) {
      return $this -> transaction ;
    }
    return false ;
  }

  protected function product ( $product ) {

  }

  protected function consumer ( $consumer ) {

  }

  protected function shippingAddress ( $shippingAddress ) {

  }

  protected function callbacks ( $callbacks ) {

  }

  protected function returns ( $returns ) {

  }

  protected function buildOrder () {

  }

  protected function buildTransaction () {

  }

  protected function validate () {}


}
