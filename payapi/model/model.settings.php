<?php

namespace payapi ;

final class model_settings extends model {

  private
    $publicId               =   false ,
    $encKey                 =   false ;

  public function validSettings () {
    if ( is_string ( $this -> arguments () ) && is_string ( $this -> arguments ( 1 ) ) ) {
      $this -> publicId = $this -> arguments () ;
      $this -> encKey = $this -> arguments ( 1 ) ;
      return true ;
    }
    return false ;
  }

  public function payload () {
    $payload = array (
      "storeDomain" => getenv ( 'SERVER_NAME' )
    ) ;
    return $this -> encode ( $payload , $this -> encKey ) ;
  }

  public function endpoint () {
    return endpoint :: merchantSettings ( $this -> publicId , $this -> config ( 'production' ) ) ;
  }


}
