<?php
namespace payapi ;

final class controller_brand extends controller {

  public function run () {
     return $this -> render ( $this -> model -> brand () , 200 ) ;
  }

}
