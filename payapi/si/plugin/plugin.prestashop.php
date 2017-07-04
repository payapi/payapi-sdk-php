<?php

namespace payapi ;

//-> PrestaShop translator

final class plugin {

  protected
    $version               =  '0.0.0' ;

  private function translateNativeProduct ( $product ) {
    return $product ;
  }

  private function translateNativeOrder ( $order ) {
    return $order ;
  }

  private function translateNativeConsumer ( $consumer ) {
    return $consumer ;
  }

  private function translateNativeAddress ( $address ) {
    return $order ;
  }

  public function product ( $product ) {
    $translatedProduct = $this -> translateNativeProduct ( $product ) ;
    return $translatedProduct ;
  }

  public function order ( $order ) {
    $translatedOrder = $this -> translateNativeOrder ( $order ) ;
    return $translatedOrder ;
  }

  public function consumer ( $consumer ) {
    $translatedConsumer = $this -> translateNativeConsumer ( $consumer ) ;
    return $translatedConsumer ;
  }

  public function address ( $address ) {
    $translatedAddress = $this -> translateNativeAddress ( $address ) ;
    return $translatedAddress ;
  }

  public function __toString () {
    return $this -> version ;
  }


}
