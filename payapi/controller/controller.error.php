<?php

namespace payapi ;

final class controller_error extends controller {

  public function run () {
    $error = ( ( is_int ( $this -> arguments () ) && $this -> code ( $this -> arguments () ) ) ? $this -> arguments () : false ) ;
    return $this -> response ( $error ) ;
  }


}
