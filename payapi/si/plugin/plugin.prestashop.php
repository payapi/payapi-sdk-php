<?php

namespace payapi ;

// PrestaShop translator

final class plugin {

  protected
    $version               =  '0.0.0' ;

  public function product ( $product ) {
    return $product ;
  }

  public function order ( $order ) {
    return $order ;
  }

  public function __toString () {
    return $this -> version ;
  }

  public function customer ( $customer ) {
    return $customer ;
  }

  public function address ( $address ) {
    return $address ;
  }


}
