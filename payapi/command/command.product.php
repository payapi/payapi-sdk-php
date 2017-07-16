<?php

namespace payapi ;

final class commandProduct extends controller {

  public function run () {
    //->
    $product = $this -> adaptor -> product ( $this -> arguments ( 0 ) ) ;
    $validated = $this -> validate -> schema ( $product , $this ->load -> schema ( 'product' ) ) ;
    return $this -> render ( $product ) ;

    if ( is_array ( $validated ) ) {
      return $this -> render ( $validated ) ;
    } else {
      $this -> debug ( 'not valid' , 'schema' ) ;
      return $this -> returnResponse ( $this -> error -> badRequest () ) ;
    }
    return returnResponse ( $this -> error -> notImplemented () ) ;
  }


}
