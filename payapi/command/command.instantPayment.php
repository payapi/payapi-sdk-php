<?php

namespace payapi ;


/*
- handle stockage ?
@TODO extraData
*/

final class commandInstantPayment extends controller {

  protected
    $payment               =   false ;

  public function run () {
    $data = $this -> arguments ( 0 ) ;
    $data [ 'product' ] = $this -> adaptor -> product ( $data [ 'product' ] ) ;
    $error = 0 ;
    $md5 = md5 ( json_encode ( $data , true ) ) ;
    $cache = $this -> cache  ( 'read' , 'product' , $md5 ) ;
    if ( $cache !== false ) {
      return $cache ;
    } else {
      if ( is_array ( $this -> validate -> schema ( $data , $this -> load -> schema ( 'instantPayment' ) ) ) !== false ) {
        $sanitized = array () ;
        foreach ( $data as $key => $value ) {
          $sanitization = $this -> validate -> schema ( $value , $this -> load -> schema ( 'instantPayment.' . $key ) ) ;
          if ( is_array ( $sanitization ) !== true ) {
            $error ++ ;
          } else {
            $sanitized [ $key ] = $sanitization ;
          }
        }
      } else {
        $error = 1 ;
      }
      if ( $error === 0 ) {
        $this -> debug ( '[schema] valid' ) ;
        $this -> payment = array_merge (
          array ( "io" => array ( "payapi.webshop" => $this -> publicId () ) ) ,
          $this -> product ( $sanitized )
        ) ;
        $payloadJson = json_encode ( $this -> payment , true ) ;
        $metaData = $this -> metadata ( $this -> payment ) ;
        $product = array (
          //"product"            => $this -> payment ,
          "payload"            => $this -> encode ( $payloadJson , $this -> publicId () ) ,
          "metadata"           => $metaData ,
          "endPointInstantBuy" => $this -> serialize -> endPointInstantBuy ( $this -> publicId () ) ,
          "endPointProductInstantBuy" => $this -> serialize -> endPointInstantBuy ( $this -> publicId () ) . $this -> serialize -> paymentUrlEncode ( $this -> payment [ 'product' ] [ 'url' ] )
        ) ;
        $this -> cache ( 'writte' , 'product' , $md5 ) ;
        return $this -> render ( $product ) ;
      } else {
        $this -> debug ( 'not valid' , 'schema' ) ;
        return $this -> returnResponse ( $this -> error -> badRequest () ) ;
      }
    }
    return returnResponse ( $this -> error -> notImplemented () ) ;
  }

  private function metadata () {
    //-> @TODO
    $data = $this -> payment ;
    $metaData = null ;
    foreach ( $data as $key => $value ) {
      if ( is_array ( $value ) !== false ) {
        foreach ( $value as $meta => $content ) {
          if ( $meta === 'options' && is_array ( $data [ $key ] [ $meta ] ) !== false ) {
            $contentParsed = $this -> serialize -> options ( $data [ $key ] [ $meta ] ) ;
          } else {
            $contentParsed = $data [ $key ] [ $meta ] ;
          }
          $metaData .= '<meta name="' . $key . '.' . $meta . '" content="' . $contentParsed . '">' . "\r\n" ;
        }
      }
    }
    return $metaData ;
  }

  private function product ( $data ) {
    $data [ 'product' ] [ 'vatInCents' ] = $data [ 'product' ] [ 'priceInCentsIncVat' ] - $data [ 'product' ] [ 'priceInCentsExcVat' ] ;
    $data [ 'product' ] [ 'vatPercentage' ] = $this -> serialize -> percentage ( $data [ 'product' ] [ 'priceInCentsIncVat' ] , $data [ 'product' ] [ 'vatInCents' ] ) ;
    return $data ;
  }

  private function cacheKey ( $md5 ) {
    $cacheKey = date ( 'YmdHis' , time () ) .'-' . $md5 ;
    return $cacheKey ;
  }


}
