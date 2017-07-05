<?php

namespace payapi ;

final class controller_settings extends controller {

  public function run () {
    $merchantSettings = $this -> curl ( $this -> model -> endpoint () , $this -> model -> payload () ) ;
    if ( isset ( $merchantSettings ) === true ) {
      $validatedMerchantSettings = $this -> model -> validateMerchantSettings ( $merchantSettings ) ;
      if ( $validatedMerchantSettings !== false && is_array ( $validatedMerchantSettings ) !== false ) {
        return $this -> render ( $validatedMerchantSettings , 200 ) ;
      } else {
        return $this -> response ( $this -> error -> errorUnexpectedCurlSchema () ) ;
      }
    } else {
      return $this -> response ( $this -> error -> errorUnexpectedCurlResponse () ) ;
    }
  }


}
