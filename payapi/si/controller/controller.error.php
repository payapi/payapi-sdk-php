<?php

namespace payapi ;

final class controller_error extends controller {

  public function run () {
    $error = ( ( is_int ( $this -> arguments () ) === true && $this -> code ( $this -> arguments () ) !== false ) ? $this -> arguments () : false ) ;
    return $this -> response ( $error ) ;
  }


}
