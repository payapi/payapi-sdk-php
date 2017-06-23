<?php

namespace payapi ;

final class controller_callback extends controller {

  public function run () {
    if ( $this -> model -> validate () === false ) {
      return $this -> response ( 406 ) ;
    } else {
      $request = $this -> model -> decoded () ;
      var_dump ( $request ) ;
      die ( 'callback' ) ;
    }
  }


}
