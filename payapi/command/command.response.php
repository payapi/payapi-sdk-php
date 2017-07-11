<?php

namespace payapi ;

final class commandResponse extends engine {

  public function run () {
      return $this -> returnResponse ( $this -> arguments ( 0 ) ) ;
  }


}
