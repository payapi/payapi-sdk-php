<?php

namespace payapi ;

final class controller_validate extends controller {

  public function run () {
    if ( $this -> validSchema ( $this -> arguments () , $this -> arguments ( 1 ) ) !== false ) {
      $this -> response ( 200 ) ;
    } else {
      $this -> response ( 400 ) ;
    }
  }


}
