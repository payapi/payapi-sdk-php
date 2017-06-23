<?php

namespace payapi ;

final class controller_settings extends controller {

  private
    $settings             =   false ;

  public function run () {
    if ( ! $this -> model -> validSettings () ) {
      return $this -> response ( 400 ) ;
    }
    $this -> curling ( $this -> model -> endpoint () , $this -> model -> payload () ) ;
    $code = ( ! isset ( $this -> response [ 'code' ] ) || ! isset ( $this -> response [ 'data' ] ) ) ? 408 : $this -> response [ 'code' ] ;
    if ( $code == 200 ) {
      $this -> settings = json_decode ( $this -> response [ 'data' ] , true ) ;
      if ( $this -> valid ( 'settings' , $this -> settings [ 'partialPayments' ] ) ) {
        $this -> render ( $this -> settings () , 200 ) ;
      } else {
        $this -> error ( 'no valid merchant settings' ) ;
        $this -> response ( 417 ) ; // expectation failed
      }
    }
    return $this -> response ( $code ) ;
  }

  public function settings () {
    return $this -> settings ;
  }

}
