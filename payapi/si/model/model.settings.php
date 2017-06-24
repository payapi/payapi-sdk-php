<?php

namespace payapi ;

final class model_settings extends model {

  public function validSettings () {
    if ( $this -> validator -> isString ( $this -> config ( 'payapi_public_id' ) ) && $this -> validator -> isPayload ( $this -> config ( 'payapi_api_key' ) , true ) ) {
      $this -> curling ( $this -> endpoint () , $this -> payload () ) ;
      $this -> responseCode = ( is_array ( $this -> curlResponse ) && isset ( $this -> curlResponse [ 'data' ] ) && isset ( $this -> curlResponse [ 'code' ] ) ) ? $this -> curlResponse [ 'code' ] : 408 ;
      if ( $this -> responseCode == 200 ) {
        $settings = json_decode ( $this -> crypter -> decode ( $this -> curlResponse [ 'data' ] , $this -> config ( 'payapi_public_id' ) , true ) , true ) ;
        if ( is_array ( $settings ) && isset ( $settings [ 'partialPayments' ] ) ) {
          $validated = $this -> validSchema ( 'settings' , $settings [ 'partialPayments' ] ) ;
          if ( is_array ( $validated ) ) {
            return $validated ;
          }
        }
      }
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
    return endpoint :: merchantSettings ( $this -> config ( 'payapi_public_id' ) , $this -> config ( 'production' ) ) ;
  }


}
