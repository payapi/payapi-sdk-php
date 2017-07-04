<?php

namespace payapi ;

//-> OpenCart translator

final class plugin {

  protected
    $version               =  '0.0.0' ;

  private
    $order                     =      false ,
    $products                  =      false ,
    $shippingAddress           =      false ,
    $consumer                  =      false ;


  private function translateNativeProduct ( $product ) {
    //-> this shoudl be done in store side (tax, options, qty, ...) -> better to pass just shopping car order for creating a transaction
    return $product ;
  }

  private function productOptions ( $nativeOptions ) {
    //->
    return $nativeOptions ;
  }

  private function productSpecial ( $nativePrice , $nativeSpecial ) {
    $price = ( @round ( ( ( isset ( $nativeSpecial ) ) ? $nativeSpecial : $nativePrice ) , 2 ) * 100 ) ;
    return $price ;
  }

  private function vatInCents ( $taxes ) {
    return ( $taxes * 100 ) ;
  }

  private function translateNativeOrder ( $nativeOrder ) { // shopping cart order
    $order = array (
      "sumInCentsIncVat" => null ,
      "sumInCentsExcVat" => null ,
      "vatInCents" => "" ,
      "currency" => "" ,
      "referenceId" => "" ,
      "tosUrl" => ""
    ) ;
    return $nativeOrder ;
  }

  private function translateNativeConsumer ( $consumer ) {
    return $consumer ;
  }

  private function translateNativeAddress ( $address ) {
    return $address ;
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
