<?php

namespace payapi ;

final class model_callback extends model {

  private
    $requested             =   false ,
    $decoded               =   false ;

  public function validate () {
    $requested = json_decode ( file_get_contents ( "php://input" ) , true ) ;
    if ( is_array ( $requested ) && isset ( $requested [ 'data' ] ) && is_array ( $requested [ 'data' ] ) ) {
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
