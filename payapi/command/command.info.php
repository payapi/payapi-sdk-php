<?php

namespace payapi ;

final class commandInfo extends engine {

  public function run () {
    return $this -> render ( $this -> entity -> get ( '___info' ) ) ;
  }


}
