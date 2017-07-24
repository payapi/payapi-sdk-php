<?php

namespace payapi ;

final class commandBrand extends controller {

  public function run () {
    if ( is_array ( $this -> brand () ) !== false ) {
      return $this -> render ( $this -> brand () ) ;
    }
    return $this -> returnResponse ( $this -> error -> notFound () ) ;
  }


}
