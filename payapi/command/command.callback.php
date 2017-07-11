<?php

namespace payapi ;

final class commandCallback extends engine {

  public function run () {
    //-> returnResponse should display a json response and send headers
    //-> enable api headers?
    $knock = $this -> knock () ;
    if ( is_string ( $knock ) === true ) {
      $knockDecoded = $this -> decode ( $knock , $this -> apiKey () ) ;
      if ( is_string ( $knockDecoded ) === true ) {
        $knockData = json_decode ( $knockDecoded , true ) ;
        if ( is_array ( $knockData ) !== false && $this -> validate -> schema ( $knockData , $this -> load -> schema ( 'callback' ) ) === true ) {
          $error = 0 ;
          foreach ( $knockData as $schema => $data ) {
            if ( $schema === 'products' ) {
              foreach ( $data as $product ) {
                if ( $this -> validate -> schema ( $product , $this -> load -> schema ( 'callback' . '.' . $schema ) ) !== true ) {
                  $error ++ ;
                }
              }
            } else
            if ( $data !== false &&  $this -> validate -> schema ( $data , $this -> load -> schema ( 'callback' . '.' . $schema ) ) !== true ) {
              $this -> warning ( $schema , 'schema' ) ;
              $error ++ ;
            }
          }
          if ( $error === 0 ) {
            if ( $this -> cache ( 'read' , 'transaction' , $knockData [ 'payment' ] [ 'status' ] . $knockData [ 'order' ] [ 'referenceId' ] ) === false ){
              $this -> cache ( 'writte' , 'transaction' , $knockData [ 'payment' ] [ 'status' ] . $knockData [ 'order' ] [ 'referenceId' ] , $knockData ) ;
              return $this -> render ( $knockData ) ;
            } else {
              return $this -> returnResponse ( $this -> error -> transactionDuplicated () ) ;
            }
          } else {
            return $this -> returnResponse ( $this -> error -> notValidSchema () ) ;
          }
        } else {
          return $this -> returnResponse ( $this -> error -> notValidSchema () ) ;
        }
      }
      return $this -> returnResponse ( $this -> error -> knockUnexpectedSignature () ) ;
    }
    return $this -> returnResponse ( $this -> error -> knockNotValid () ) ;
  }


}
