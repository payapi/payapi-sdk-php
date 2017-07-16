<?php

namespace payapi ;

final class commandLocalize extends controller {

  public function run () {
    if ( $this -> validate -> ip ( $this -> arguments ( 1 ) ) === true ) {
      $ip = $this -> arguments ( 1 ) ;
    } else {
      $ip = $this -> ip () ;
    }
    $this -> debug ( '[check] ' . $ip ) ;
    $cached = $this -> cache ( 'read' , 'localize' , $ip ) ;
    if ( $this -> arguments ( 0 ) !== true && $cached !== false ) {
      return $this -> render ( $cached ) ;
    } else {
      $endPoint = $this -> endPointLocalization ( $ip ) ;
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
            return $this -> returnResponse ( $this -> error -> notValidLocalizationSchema () ) ;
          }
        } else {
          return $this -> returnResponse ( $request [ 'code' ] ) ;
        }
      }
    }
    return $this -> returnResponse ( $this -> error -> timeout () ) ;
  }


}
