<?php

namespace payapi ;

class model_trasaction extends model {

  protected function auto () {

  }

  public function saveTransaction ( $key ) {
    $dir = router :: archivalTransaction ( $key ) ;
    //-> public function archivalTransaction ( $key ) {}
    return false ;
  }



}
