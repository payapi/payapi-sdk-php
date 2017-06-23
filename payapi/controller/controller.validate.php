<?php

namespace payapi ;

final class controller_validate extends controller {

  public function run () {
    if ( $this -> valid ( $this -> arguments () , $this -> arguments ( 1 ) ) === true ) {
      $this -> response ( 200 ) ;
    } else {
      $this -> response ( 400 ) ;
    }
  }


}
