<?php

namespace payapi ;

final class controller_transaction extends controller {

  private
    $id                   =   false ,
    $transaction          =   false ;

  public function run () {
    //$this -> transaction = $this -> load -> library ( 'transaction' ) ;
    if ( ! $this -> arguments () || ! $this -> arguments ( 1 ) ) {
      return $this -> render ( $this -> response ( 404 ) , 404 ) ;
    } else {
      // @TODELETE just for testing
      return $this -> render ( $this -> response ( 200 ) , 200 ) ;
    }
  }

  public function processTransaction () {
    $payload = ( string ) $this -> transaction ;
    return false ;
  }


  public function validate () {

  }

}
