<?php

namespace payapi ;

final class commandResponse extends controller {

  public function run () {
      return $this -> returnResponse ( $this -> arguments ( 0 ) ) ;
  }


}
