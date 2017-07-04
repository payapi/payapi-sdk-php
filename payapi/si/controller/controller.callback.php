<?php

namespace payapi ;

final class controller_callback extends controller {

  public function run () {
    $knock = $this -> knock () ;
    if ( $knock !== false ) {
      $decodedData = $this -> model -> decodedCallback ( $knock ) ;
      if ( $decodedData !== false ) {
        if ( $this -> model -> validateCallbackSchema ( $decodedData ) === true ) {
          //-> archive data
          //$key = null ;
          $this -> model -> setArchiveData ( $key , $decodedData , 'transaction'  ) ;
          //-> @TODO check transaction was created previously, validate and include id in response? (internal id)
          return $this -> render ( $decodedData , 200 ) ;
        } else {
          $this -> debug ( '[callback] undecoded' ) ;
          return $this -> response ( $this -> error -> errorUnexpectedCallbackData () ) ;
        }
      } else {
        $this -> debug ( '[callback] unexpected data' ) ;
        return $this -> response ( $this -> error -> errorCallbackNoValidJwtPayload () ) ;
      }
    } else {
      $this -> debug ( '[callback] no request' ) ;
      return $this -> response ( $this -> error -> errorCallbackNoRequest () ) ;
    }
  }


}
