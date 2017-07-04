<?php

namespace payapi ;

final class framework extends helper {

  public
    $filterer                  =     false ,
    $serializer                =     false ,
    $sanitizer                 =     false ,
    $validator                 =     false ;

  protected function auto () {
    //-> moved to cgi
    $this -> filterer = new filterer () ;
    $this -> serializer = new serializer () ;
    $this -> sanitizer = new sanitizer () ;
    $this -> validator = new validator () ;
    $this -> addInfo ( 'serializer_v' , ( string ) $this -> serializer ) ;
    $this -> addInfo ( 'unix' , ( int ) microtime ( true ) ) ;
    $this -> addInfo ( 'stamp' , $this -> serializer -> timestamp () ) ;
  }

  public function autoloaded () {
    if ( is_object ( $this -> filterer ) === true && is_object ( $this -> serializer ) === true && is_object ( $this -> sanitizer ) === true && is_object ( $this -> validator ) === true ) {
      return true ;
    }
    return false ;
  }

  public function signature ( $populate = false ) {
    if ( is_array ( $populate ) === true ) {
      return array_merge ( $populate, $this -> data -> extradata () ) ;
    } else {
      return $this -> data -> extradata () ;
    }
  }

  public function getSchema ( $schemaKey ) {
    return $this -> validator -> getSchema ( $schemaKey ) ;
  }
  //-> moved to cgi
  public function knock () {
    $received = json_decode ( file_get_contents ( "php://input" ) , true ) ;
    $debugged = ( ( is_string ( $received ) === true ) ? $received : json_encode ( $received , true ) ) ;
    if ( $received !== false ) {
      $this -> debug ( '[ACK] success : ' . $debugged ) ;
    }
    //-> @TODO
    $filterer = $received ;
    if ( is_array ( $received ) === true && $this -> validate ( 'request.standard' , $received ) !== false ) {
      return $filterer ;
    }
    return false ;
  }

  public function validSchema ( $unknownSchemaKey , $unvalidatorData ) {
    $filtererSchemaKey = $this -> filterer -> schemaKey ( $unknownSchemaKey ) ;
    if ( $this -> validator -> isString ( $filtererSchemaKey ) === true ) {
      $schemaData = $this -> getSchema ( $filtererSchemaKey ) ;
      if ( $this -> validator -> isArray ( $schemaData ) === true && $this -> validator -> isArray ( $unvalidatorData ) === true ) {
        $filtererData = $this -> filterer -> filtererSchemaData ( $unvalidatorData ) ;
        if ( $this -> validator -> isArray ( $filtererData ) === true ) {
          $validatorData = $this -> validator -> schema ( $filtererSchemaKey , $filtererData ) ;
          if ( $this -> validator -> isArray ( $validatorData ) === true ) {
            $this -> debug ( '[schema] success' ) ;
            return $validatorData ;
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
