<?php

namespace payapi ;

final class controller_callback extends controller {

  public function run () {
    if ( $this -> model -> isRequested () !== false ) {
      if ( $this -> model -> validateRequested () !== false ) {
        $request = $this -> model -> decoded () ;
        $this -> debug ( 'OOOOOOOOOOOOOOOOOOOOOK' ) ;
        var_dump ( $request ) ;
        die ( 'callback' ) ;


      } else {
        return $this -> response ( $this -> error -> errorUnexpectedCurlSchema () ) ;
        $this -> debug ( 'has unexpected data' ) ;
      }
    } else {
      $this -> debug ( 'has no request data' ) ;
      return $this -> response ( $this -> error -> errorCallbackNoRequest () ) ;
    }
  }

}
