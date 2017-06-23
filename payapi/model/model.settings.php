<?php

namespace payapi ;

final class model_settings extends model {

  private
    $publicId               =   false ,
    $encKey                 =   false ;

  public function validSettings () {
    if ( is_string ( $this -> config ( 'payapi_public_id' ) ) && is_string ( $this -> config ( 'payapi_api_key' ) ) ) {
      $this -> publicId = $this -> config ( 'payapi_public_id' ) ;
      $this -> encKey = $this -> config ( 'payapi_api_key' ) ;
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
