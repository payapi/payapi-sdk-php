<?php

namespace payapi ;

final class model_callback extends model {

  private
    $received              =   false ;

  public function decodedCallback ( $encodedPayload ) {
    $decodedJsonData = $this -> decode ( $encodedPayload , $this -> getDecodedApiKey ( $this -> config ( 'encoded_payapi_api_key' ) ) ) ;
    if ( $decodedJsonData !== false && is_string ( $decodedJsonData ) === true ) {
      $decodedData = json_decode ( $decodedJsonData , true ) ;
      if ( $decodedData !== false && is_array ( $decodedData ) !== false  ) {
        return $decodedData ;
      }
    }
    return false ;
  }

  public function validateCallbackSchema ( $callbackData ) {
    if ( $this -> validSchema ( 'endpoint.callback' , $callbackData ) !== false ) {
      return true ;
    } else {
      $this -> error ( '[schema] no valid' ) ;
      return false ;
    }
  }


}
