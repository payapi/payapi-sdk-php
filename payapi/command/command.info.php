<?php

namespace payapi ;

final class commandInfo extends controller {

  public function run () {
    return $this -> render ( $this -> entity -> get ( '___info' ) ) ;
  }


}
