<?php

namespace payapi ;

final class commandLocalize extends engine {

  public function run () {
    $ip = $this -> ip () ;
    $cached = $this -> cache ( 'read' , 'localize' , $ip ) ;
    if ( $cached !== false ) {
      return $this -> render ( $cached ) ;
    } else {
      $endPoint = $this -> route -> endPointLocalization ( $ip ) ;
      $request = $this -> curl ( $endPoint , false , false ) ;
      if ( $request !== false && isset ( $request [ 'code' ] ) === true ) {
        if ( $request [ 'code' ] === 200) {
          if ( $this -> validate -> schema ( $request [ 'data' ] , $this -> load -> schema ( 'localize' ) ) === true ) {
            $this -> debug ( '[localize] valid schema' ) ;
            $adaptedData = $this -> adaptor -> localized ( $request [ 'data' ] ) ;
            $this -> cache ( 'writte' , 'localize' , $ip , $adaptedData ) ;
            return $this -> render ( $this -> cache ( 'read' , 'localize' , $ip ) ) ;
          } else {
            //-> not valid schema from PA
            $this -> error ( 'no valid localization' , 'warning' ) ;
            return $this -> returnResponse ( $this -> error -> notValidSchema () ) ;
          }
        } else {
          return $this -> returnResponse ( $request [ 'code' ] ) ;
        }
      }
    }
    return $this -> returnResponse ( $this -> error -> timeout () ) ;
  }


}
