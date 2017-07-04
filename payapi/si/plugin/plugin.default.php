<?php

namespace payapi ;

//-> Default translator (do not translate just pass variables)

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

  public function consumer ( $consumer ) {
    return $consumer ;
  }

  public function address ( $address ) {
    return $address ;
  }


}
