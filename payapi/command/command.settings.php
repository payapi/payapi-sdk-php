<?php

namespace payapi ;

final class commandSettings extends controller {

  public function run () {
    if ( $this -> validate -> publicId ( $this -> arguments ( 0 ) ) === true && $this -> validate -> apiKey ( $this -> arguments ( 1 ) ) === true ) {
      $publicId = $this -> arguments ( 0 ) ;
      $apiKey = $this -> arguments ( 1 ) ;
    } else {
      $publicId = $this -> publicId () ;
      $apiKey = $this -> apiKey () ;
    }
    if ( $this -> validate -> publicId ( $publicId ) === true && $this -> validate -> apiKey ( $apiKey ) === true ) {
      $cached = $this -> cache ( 'read' , 'settings' , $this -> instance () ) ;
      if ( $this -> arguments ( 2 ) !== true && $cached !== false ) {
        return $this -> render ( $cached ) ;
      } else {
        $endPoint = $this -> serialize -> endPointSettings ( $publicId ) ;
        $request = $this -> curl ( $endPoint , $this -> payload ( $apiKey ) , true ) ;
        if ( $request !== false && isset ( $request [ 'code' ] ) === true ) {
          if ( $request [ 'code' ] === 200 ) {
            $decodedData = json_decode ( $this -> decode ( $request [ 'data' ] , $apiKey ) , true ) ;
            $validated = $this -> validate -> schema ( $decodedData , $this -> load -> schema ( 'settings' ) ) ;
            if ( is_array ( $validated ) !== false ) {
              $this -> debug ( '[settings] valid schema' ) ;
              if ( $decodedData [ 'partialPayments' ] !== false ) {
                $partialValidated = $this -> validate -> schema ( $decodedData [ 'partialPayments' ] , $this -> load -> schema ( 'partialPayments' ) ) ;
                if ( is_array ( $partialValidated ) !== false ) {
                  $this -> debug ( '[partialPayments] valid schema' ) ;
                  $validated [ 'partialPayments' ] = $partialValidated ;
                } else {
                  $validated [ 'partialPayments' ] = false ;
                  $this -> error ( '[partialPayments] no valid schema' , 'warning' ) ;
                }
              }
              $this -> cache ( 'writte' , 'account' , $this -> instance () , array (
                "publicId" => $publicId ,
                "apiKey"   => $this -> encode ( $apiKey , false , true )
              ) ) ;
              $this -> cache ( 'writte' , 'settings' , $this -> instance () , $validated ) ;
              return $this -> render ( $this -> cache ( 'read' , 'settings' , $this -> instance () ) ) ;
            } else {
              //-> not valid schema from PA
              $this -> error ( 'no valid settings' , 'warning' ) ;
              return $this -> returnResponse ( $this -> error -> notValidSchema () ) ;
            }
          } else {
            return $this -> returnResponse ( $request [ 'code' ] ) ;
          }
        } else {
          return $this -> returnResponse ( $this -> error -> unexpectedResponse () ) ;
        }
      }
    } else {
      return $this -> returnResponse ( $this -> error -> badRequest () ) ;
    }
    return $this -> returnResponse ( $this -> error -> timeout () ) ;
  }

  private function payload ( $apiKey ) {
    $payload = array (
      //=>
      "storeDomain" => getenv ( 'SERVER_NAME' )
    ) ;
    //var_dump ( $payload , $this -> apiKey () , $this -> encode ( $payload , $this -> apiKey () ) ) ; exit ;
    return $this -> encode ( $payload , $apiKey ) ;
  }


}
