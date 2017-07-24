<?php

namespace payapi ;

class commandPayment extends controller {

  private
    $payment              =   false ;

  public function run () {
    $data = $this -> arguments ( 0 ) ;
    $data = $this -> adaptor -> payment ( $data ) ;
    $error = 0 ;
    $md5 = md5 ( json_encode ( $data , true ) ) ;
    $cache = $this -> cache  ( 'read' , 'payment' , $md5 ) ;
    if ( $cache !== false && 1 === 2 ) {
      return $cache ;
    } else {
      if ( is_array ( $this -> validate -> schema ( $data , $this -> load -> schema ( 'payment' ) ) ) !== false ) {
        $sanitized = array () ;
        foreach ( $data as $key => $value ) {
          if ( $key !== 'product' ) {
            $sanitization = $this -> validate -> schema ( $value , $this -> load -> schema ( 'payment.' . $key ) ) ;
            if ( is_array ( $sanitization ) !== true ) {
              $error ++ ;
            } else {
              $sanitized [ $key ] = $sanitization ;
            }
          } else {
            foreach ( $value as $key => $product ) {
              $sanitization = $this -> validate -> schema ( $product , $this -> load -> schema ( 'payment' . '.' . 'product' ) ) ;
              if ( is_array ( $sanitization ) !== true ) {
                $error ++ ;
              } else {
                $sanitized [ 'products' ] [] = $sanitization ;
              }
            }
          }
        }
      } else {
        $error = 1 ;
      }
      if ( $error === 0 ) {
        //->
        $this -> payment = $sanitized ;
        $this -> debug ( '[schema] valid' ) ;
        $this -> order () ;
        $payloadJson = json_encode ( $this -> payment , true ) ;
        $payloadJwt = $this -> encode ( $payloadJson , $this -> publicId () ) ;
        $payment = array (
          "payment"            => $this -> payment ,
          "payload"            => $payloadJwt ,
          //"decoded"            => $this -> decode ( $payloadJwt , $this -> publicId () ) ,
          "endPointPayment"    => $this -> serialize -> endPointPayment ( $this -> publicId () )
        ) ;
        $this -> cache ( 'writte' , 'payment' , $md5 ) ;
        return $this -> render ( $payment ) ;
      } else {
        $this -> debug ( 'not valid' , 'schema' ) ;
        return $this -> returnResponse ( $this -> error -> badRequest () ) ;
      }
    }
    return returnResponse ( $this -> error -> notImplemented () ) ;
  }

  private function product ( $data ) {
    foreach ( $data [ 'products' ] as $key => $product ) {
      $data [ 'products' ] [ $key ] [ 'vatInCents' ] = $data [ 'products' ] [ $key ] [ 'priceInCentsIncVat' ] - $data [ 'products' ] [ $key ] [ 'priceInCentsExcVat' ] ;
      $data [ 'products' ] [ $key ] [ 'vatPercentage' ] = $this -> serialize -> percentage ( $data [ 'products' ] [ $key ] [ 'priceInCentsIncVat' ] , $data [ 'products' ] [ $key ] [ 'vatInCents' ] ) ;
    }
    return $data ;
  }

  private function order () {
    //-> @TODO handle shipping&handling ( add shipping to product array )
    //-> $this -> payment [ 'order' ] [ 'shippingHandlingFeeInCentsIncVat' ]
    //-> $this -> payment [ 'order' ] [ 'shippingHandlingFeeInCentsExcVat' ]
    $sumInCentsIncVat = 0 ;
    $sumInCentsExcVat = 0 ;
    //-> @TODO @CARE to move to adaptor
    foreach ( $this -> payment [ 'products' ] as $key => $product ) {
      $sumInCentsIncVat += ( $product [ 'priceInCentsIncVat' ] * $product [ 'quantity' ] ) ;
      $sumInCentsExcVat += ( $product [ 'priceInCentsExcVat' ] * $product [ 'quantity' ] ) ;
    }
    $vatInCents = $sumInCentsIncVat - $sumInCentsExcVat ;
    $order = array (
      "sumInCentsIncVat" => $sumInCentsIncVat                           ,
      "sumInCentsExcVat" => $sumInCentsExcVat                           ,
      "vatInCents"       => $vatInCents                                 ,
      "currency"         => $this -> payment [ 'order' ] [ 'currency' ] ,
      "referenceId"      => $this -> payment [ 'order' ] [ 'referenceId' ]                                      ,
    ) ;
    if ( isset ( $this -> payment [ 'order' ] [ 'tosUrl' ] ) === true ) {
      $order [ 'tosUrl' ] = $this -> payment [ 'order' ] [ 'tosUrl' ] ;
    }
    $this -> payment [ 'order' ] = $order ;
    return $this -> payment [ 'order' ] ;
  }

  private function cacheKey ( $md5 ) {
    $cacheKey = date ( 'YmdHis' , time () ) .'-' . $md5 ;
    return $cacheKey ;
  }


}
