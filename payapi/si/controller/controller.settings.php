<?php

namespace payapi ;

final class controller_settings extends controller {

  private
    $settings             =   false ;

  public function run () {
    if ( $this -> model -> validSettings () === true ) {

    } else {
      return $this -> response ( 400 ) ;
    }
    $settings = json_decode ( $this -> response [ 'data' ] , true ) ;
    if ( $settings [ 'partialPayments' ] === false ) {
      $this -> settings = array ( 'status' => 'disabled' ) ;
      return $this -> render ( $this -> settings () , 200 ) ;
    } else if ( is_array ( $settings ) ) {
      $this -> settings = $this -> validSchema ( 'settings' , $this -> settings [ 'partialPayments' ] ) ;
      if ( is_array ( $this -> settings ) ) {
        return $this -> render ( $this -> settings () , 200 ) ;
      }
      $code = 412 ;
    } else {
      $this -> error ( 'no valid merchant settings' ) ;
      $code = 417 ;
    }
    return $this -> response ( $code ) ;
  }

  public function settings () {
    return $this -> settings ;
  }

}
