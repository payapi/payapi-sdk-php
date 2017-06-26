<?php

namespace payapi ;

final class model_callback extends model {

  private
    $received              =   false ,
    $requested             =   false ,
    $decoded               =   false ;


  public function isRequested () {
    $this -> received = $this -> knock () ;
    return $this -> received ;
  }

  public function validate () {
    $requested = json_decode ( $this -> received , true ) ;
    $this -> debug ( 'validating data' ) ;
    if ( is_array ( $requested ) === true && isset ( $requested [ 'data' ] ) === true && is_array ( $requested [ 'data' ] ) === true ) {
      $decoded = $this -> crypter -> decode ( $requested [ 'data' ] , $this -> config ( 'payapi_api_key' ) ) ;
      if ( $this -> validate ( 'endpoint.callback' , $decoded ) ) {
        $this -> requested = $requested ;
        $this -> decoded = $decoded ;
      }
    } else {
      $this -> error ( 'no valid callback' ) ;
    }
    return $this -> decoded ;
  }

  public function decoded () {
    return $this -> decoded ;
  }


}
