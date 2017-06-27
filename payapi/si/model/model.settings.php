<?php

namespace payapi ;

final class model_settings extends model {

  public function validateMerchantSettings ( $settings ) {
    if ( $settings [ 'code' ] === 200 ) {
      if ( isset ( $settings [ 'data' ] [ 'partialPayments' ] ) === true && is_array ( $settings [ 'data' ] [ 'partialPayments' ] ) === true ) {
        //-> @TODO if partialPayments === false
        $validator = $this -> validSchema ( 'merchant.settings' , $settings [ 'data' ] [ 'partialPayments' ] ) ;
        if ( is_array ( $validator ) === true ) {
          $this -> status = 200 ;
          $this -> set ( 'MerchantSettings' , $validator ) ;
          return $this -> get ( 'MerchantSettings' ) ;
        } else {
          $this -> status = $this -> error -> errorUnexpectedCurlSchema () ;
        }
      } else {
        $this -> status = $this -> error -> errorNoValidJsonPayload () ;
      }
    } else {
      $this -> status = $settings [ 'code' ] ;
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
