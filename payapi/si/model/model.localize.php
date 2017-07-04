<?php

namespace payapi ;

class model_localize extends model {

  protected function auto () {}

  public function getIpLocalization () {
    $localizator = $this -> load -> library ( 'localize' ) ;
    $localized = json_decode ( ( string ) $localizator , true ) ;
    return $localized ;
  }


}
