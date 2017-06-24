<?php

namespace payapi ;

final class controller_settings extends controller {

  public function run () {
    $settings = $this -> model -> getMerchantSettings () ;
    if ( is_array ( $settings ) === true ) {
      return $this -> render ( $settings , 200 ) ;
    }
    return $this -> response ( $this -> model -> status () ) ;
  }


}
