<?php

namespace payapi ;

final class commandBrand extends controller {

  public function run () {
    //->
    $brand = $this -> brand -> info () ;
    if ( isset ( $brand [ 'partnerId' ] ) === true ) {
      return $this -> render ( $brand ) ;
    }
    return $this -> returnResponse ( $this -> error -> notFound  () ) ;
  }


}
