<?php

namespace payapi ;

final class handler extends helper {

  public
    $filtered                  =     false ,
    $serialized                =     false ,
    $sanitized                 =     false ,
    $validated                 =     false ;

  private
    $info                      =     false ,
    $loaded                    =     false ;

  protected function auto () {
    $this -> filtered = new filterer () ;
    $this -> serialized = new serializer () ;
    $this -> sanitized = new sanitizer () ;
    $this -> validated = new validator () ;
    $this -> info = array_merge (
      $this -> data -> get ( 'info' ) ,
      array (
      "___ip"             => $this -> serialized -> getIp () ,
      "_____filter_v"     => json_decode ( ( string ) $this -> filtered , true ) ,
      "_____serializer_v" => json_decode ( ( string ) $this -> serialized , true ) ,
      "_____sanitizer_v"  => json_decode ( ( string ) $this -> sanitized , true ) ,
      "_____validator_v"  => json_decode ( ( string ) $this -> validated , true ) ,
      "___stamp"          => date ( 'Y-m-d H:i:s T' , time () )
    ) ) ;
    $this -> set ( 'info' , $this -> info ) ;
  }

  public function autoloaded () {
    if ( is_object ( $this -> filtered ) === true && is_object ( $this -> serialized ) === true && is_object ( $this -> sanitized ) === true && is_object ( $this -> validated ) === true ) {
      return true ;
    }
    return false ;
  }

  public function signature ( $populate ) {
    if ( is_array ( $populate ) ) {
      return array_merge ( $this -> info , $populate ) ;
    } else {
      return $info ;
    }

    return $this -> serialized -> sign ( $this -> get ( 'info' ) ) ;
  }

  public function getSchema ( $schemaKey ) {
    return $this -> validated -> getSchema ( $schemaKey ) ;
  }

  public function validSchema ( $unknownSchemaKey , $unvalidatedData ) {
    $filteredSchemaKey = $this -> filtered -> schemaKey ( $unknownSchemaKey ) ;
    if ( $this -> validated -> isString ( $filteredSchemaKey ) === true ) {
      $schemaData = $this -> getSchema ( $filteredSchemaKey ) ;
      if ( $this -> validated -> isArray ( $schemaData ) === true && $this -> validated -> isArray ( $unvalidatedData ) === true ) {
        $filteredData = $this -> filtered -> filteredSchemaData ( $unvalidatedData ) ;
        if ( $this -> validated -> isArray ( $filteredData ) === true ) {
          $validatedData = $this -> validated -> schema ( $filteredSchemaKey , $filteredData ) ;
          if ( $this -> validated -> isArray ( $validatedData ) === true ) {
            return $validatedData ;
          } else {
            $this -> warning ( 'data' , 'validator' ) ;
          }
        } else {
          $this -> warning ( 'data' , 'filter' ) ;
        }
      } else {
        $this -> warning ( 'schema' , 'validator' ) ;
      }
    } else {
      $this -> warning ( 'schema' , 'filter' ) ;
    }
    return false ;
  }


}
