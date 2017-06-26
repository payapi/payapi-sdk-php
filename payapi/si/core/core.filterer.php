<?php

namespace payapi ;

final class filterer {

  protected
    $version                  =   '0.0.1' ;

  public function schemaKey ( $schemaKey ) {
    return $this -> filteredString ( $schemaKey ) ;
  }

  public function filteredSchemaData ( $unfilteredSchemaData ) {
    return $this -> filteredArray ( $unfilteredSchemaData ) ;
  }

  public function knock () {
    $array = json_decode ( $knock , true ) ;
    // @NOTE-> should be great if data always came encoded
    if ( isset ( $array [ 'data' ] ) && is_object ( $array [ 'data' ] ) === false ) { // is_string ( $array [ 'data' ] ) && substr_count ( $array [ 'data' ] , '.' ) == 2
      return $array [ 'data' ] ;
    }
    return false ;
  }

  public function filteredArray ( $array ) {
    if ( is_array ( $array ) === true && $this -> noObjectsAndFloats ( $array ) === true ) {
      return $array ;
    }
    return false ;
  }

  public function filteredString ( $string ) {
    if ( is_string ( $string ) === true ) {
      return $string ;
    }
    return false ;
  }

  public function filteredInt ( $unfilteredInt ) {
    if ( is_int ( $unfilteredInt ) === true ) {
      $filteredMaximumInt = $this -> filterMaximumInt ( $unfilteredInt ) ;
      if ( is_int ( $filteredMaximumInt ) ) {
        return $filteredMaximumInt ;
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

  public function filteredBool ( $unfilteredBool ) {
    if ( is_bool ( $unfilteredBool ) === true && $unfilteredBool === true ) {
      return true ;
    }
    return false ;
  }

  private function noObjectsAndFloats ( $unfilteredArray ) {
    foreach ( $unfilteredArray as $filtering ) {
      if ( is_array ( $filtering ) === true ) {
        if ( $this -> noObjectsAndFloats ( $filtering ) !== true ) {
          return false ;
        }
      }
      if ( is_string ( $filtering ) === true || is_int ( $filtering ) === true || is_bool ( $filtering ) ) {
        return true ;
      }
    }
    return false ;
  }

  public function __toString () {
    return serializer :: toString ( $this -> version ) ;
  }


}
