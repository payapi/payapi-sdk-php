<?php

namespace payapi ;

final class adaptor extends plugin {

  protected
    $key                   =  'default' ;

  public function product ( $product ) {
    return $product ;
  }

  public function order ( $order ) {
    return $order ;
  }


}
