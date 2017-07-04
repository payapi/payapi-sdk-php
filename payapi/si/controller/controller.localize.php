<?php
namespace payapi ;

final class controller_localize extends controller {

  public function run () {
    return $this -> render ( $this -> model -> getIpLocalization () , 200 ) ;
  }

}
