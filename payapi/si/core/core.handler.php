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
      "___filter_v"     => ( string ) $this -> filtered ,
      "___serializer_v" => ( string ) $this -> serialized ,
      "___sanitizer_v"  => ( string ) $this -> sanitized ,
      "___validator_v"  => ( string ) $this -> validated ,
      "___ip"             => $this -> serialized -> getIp () , // tomove
      "___stamp"          => $this -> serialized -> timestamp ()
    ) ) ;
    $this -> set ( 'info' , $this -> info ) ;
  }

  public function autoloaded () {
    if ( is_object ( $this -> filtered ) === true && is_object ( $this -> serialized ) === true && is_object ( $this -> sanitized ) === true && is_object ( $this -> validated ) === true ) {
      return true ;
    }
    return false ;
  }

  public function signature ( $populate = false ) {
    if ( is_array ( $populate ) === true ) {
      return array_merge ( $populate, $this -> extradata () ) ;
    } else {
      return $this -> extradata () ;
    }
  }

  public function extradata () {
    /*
    return array (
      "___extradata" => $this -> get ( 'info' )
    ) ;
    */
    return $this -> get ( 'info' ) ;
  }

  public function arguments ( $key = 0 ) {
    if ( ! isset ( $this -> arguments [ $key ] ) ) {
      return false ;
    }
    return $this -> arguments [ $key ] ;
  }

  public function getSchema ( $schemaKey ) {
    return $this -> validated -> getSchema ( $schemaKey ) ;
  }

  public function knock () {
    $received = json_decode ( file_get_contents ( "php://input" ) , true ) ;
    $debugged = ( ( is_string ( $received ) === true ) ? $received : json_encode ( $received , true ) ) ;
    if ( $received !== false ) {
      $this -> debug ( '[ACK] success : ' . $debugged ) ;
    }
    //-> @TODO
    $filtered = $received ;
    if ( is_array ( $received ) === true && $this -> validate ( 'request.standard' , $received ) !== false ) {
      return $filtered ;
    }
    return false ;
  }

  public function NEWknock () {
    $received = $this -> validated -> knocked () ;
    if ( $received !== false ) {
      $filtered = $this -> filtered -> knock () ;
      if ( $filtered !== false ) {
        $this -> debug ( '[ACK] success' ) ;
        return $filtered ;
      } else {
        unset ( $received ) ;
        $this -> warning ( 'unexpected ' , 'ack' ) ;
        return $this -> error -> errorUnexpectedCallbackInput () ;
      }
    } else {
      unset ( $received ) ;
      $this -> debug ( '[ACK] empty ' ) ;
      return $this -> error -> errorCallbackNoRequest () ;
    }
    return false ;
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
