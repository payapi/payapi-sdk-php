<?php

namespace payapi ;

final class model_callback extends model {

  private
    $received              =   false ,
    $requested             =   false ,
    $decoded               =   false ;


    public function knock () {
      $received = file_get_contents ( "php://input" ) ;
      if ( is_array ( $received ) === true ) {
        $this -> received = $requested ;
        return true ;
      }
      return false ;
    }

  public function validate () {
    $requested = json_decode ( $this -> received , true ) ;
    if ( is_array ( $requested ) === true && isset ( $requested [ 'data' ] ) === true && is_array ( $requested [ 'data' ] ) === true ) {
      $decoded = $this -> crypter -> decode ( $requested [ 'data' ] , $this -> config ( 'payapi_api_key' ) ) ;
      if ( $this -> validate ( 'endpointCallback' , $decoded ) ) {
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
