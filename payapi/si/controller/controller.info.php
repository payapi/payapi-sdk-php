<?php
namespace payapi ;

final class controller_info extends controller {

  public function run () {
     return $this -> render ( $this -> data -> extradata () , 200 ) ;
  }

}
