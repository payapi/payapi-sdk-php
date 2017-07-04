<?php

namespace payapi ;

final class filterer {

  protected
    $version                  =   '0.0.0' ;

  public function schemaKey ( $schemaKey ) {
    return $this -> filtererString ( $schemaKey ) ;
  }

  public function filtererSchemaData ( $unfiltererSchemaData ) {
    return $this -> filtererArray ( $unfiltererSchemaData ) ;
  }

  public function knock () {
    $array = json_decode ( $knock , true ) ;
    // @NOTE-> should be great if data always came encoded
    if ( isset ( $array [ 'data' ] ) && is_object ( $array [ 'data' ] ) === false ) { // is_string ( $array [ 'data' ] ) && substr_count ( $array [ 'data' ] , '.' ) == 2
      return $array [ 'data' ] ;
    }
    return false ;
  }

  public function filteredUrl ( $url ) {
    return filter_var ( $url , FILTER_SANITIZE_URL ) ;
  }

  public function getHostNameFromUrl ( $url ) {
    $parse = parse_url ( $url ) ;
    if ( isset ( $parse [ 'host' ] ) === true ) {
      return $parse [ 'host' ] ;
    }
    return false ;
  }

  public function filtererArray ( $array ) {
    if ( is_array ( $array ) !== false && $this -> noObjectsAndFloats ( $array ) === true ) {
      return $array ;
    }
    return false ;
  }

  public function filtererString ( $string ) {
    if ( is_string ( $string ) === true ) {
      return $string ;
    }
    return false ;
  }

  public function filtererInt ( $unfiltererInt ) {
    if ( is_int ( $unfiltererInt ) === true ) {
      $filtererMaximumInt = $this -> filterMaximumInt ( $unfiltererInt ) ;
      if ( is_int ( $filtererMaximumInt ) ) {
        return $filtererMaximumInt ;
      }
    }
    return false ;
  }

  public function filterMaximumInt ( $int ) {
    if ( $int < pow ( 9 , 250 ) ) {
      return $int ;
    }
    return false ;
  }

  public function filtererBool ( $unfiltererBool ) {
    if ( is_bool ( $unfiltererBool ) === true && $unfiltererBool === true ) {
      return true ;
    }
    return false ;
  }

  private function noObjectsAndFloats ( $unfiltererArray ) {
    return true ;
    //-> @NOTE @CARE @FIXME
    foreach ( $unfiltererArray as $filtering ) {
      if ( is_array ( $filtering ) === true ) {
        if ( $this -> noObjectsAndFloats ( $filtering ) !== true ) {
          return false ;
        }
      } else
      if ( is_string ( $filtering ) === true || is_array ( $filtering ) !== false || is_int ( $filtering ) === true || is_bool ( $filtering ) === true ) {
        return true ;
      }
    }
    return false ;
  }

  public function __toString () {
    return serializer :: toString ( $this -> version ) ;
  }


}
