<?php

namespace payapi ;

final class controller_transaction extends controller {

  public function run () {
    if ( ! $this -> arguments () || ! $this -> arguments ( 1 ) ) {
      return $this -> render ( $this -> response ( 404 ) , 404 ) ;
    } else {
      // @TODELETE just for testing
      return $this -> render ( $this -> response ( 200 ) , 200 ) ;
    }
  }

  public function validate () {

  }

}
