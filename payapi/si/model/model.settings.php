<?php

namespace payapi ;

final class model_settings extends model {

  public function getMerchantSettings () {
    $this -> curling ( $this -> endpoint () , $this -> payload () ) ;
    if ( $this -> curlResponse [ 'code' ] === 200 ) {
      if ( isset ( $this -> curlResponse [ 'data' ] [ 'partialPayments' ] ) === true && is_array ( $this -> curlResponse [ 'data' ] [ 'partialPayments' ] ) === true ) {
        //-> @TODO if partialPayments === false
        $validated = $this -> validSchema ( 'merchant.settings' , $this -> curlResponse [ 'data' ] [ 'partialPayments' ] ) ;
        if ( is_array ( $validated ) === true ) {
          return $validated ;
        } else {
          $this -> status = $this -> error -> errorUnexpectedCurlSchema () ;
        }
      } else {
        $this -> status = $this -> error -> errorNoValidJsonPayload () ;
      }
    } else {
      $this -> status = $this -> curlResponse [ 'code' ] ;
    }
    return false ;
  }

  public function payload () {
    $payload = array (
      "storeDomain" => getenv ( 'SERVER_NAME' )
    ) ;
    return $this -> encode ( $payload , $this -> decode ( $this -> config ( 'encoded_payapi_api_key' ) , $this -> config ( 'payapi_public_id' )  , true ) ) ;
  }

  public function endpoint () {
    return endpoint :: merchantSettings ( $this -> config ( 'payapi_public_id' ) , $this -> config ( 'production' ) ) ;
  }


}
