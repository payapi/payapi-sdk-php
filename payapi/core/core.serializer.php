<?php
/***
*
*  @NOTE
*        @info()-return has to be an array
*
***/

namespace payapi ;

final class serializer {

  public static
    $single                   =  false ;

  protected
    $version                  =  array (
      "___serializer__v"      => '0.0.1'
    ) ;

  private
    $info                     =  array () ;

  protected function validate ( $schema , $data ) {
    if ( ! is_array ( $schema ) || ! $data )
      return false ;
    foreach ( $schema as $key => $value ) {
      if ( is_array ( $value ) ) {
        foreach ( $value as $fallback ) {
          if ( ! $this -> validate ( $fallback ) ) {
            return false ;
          }
        }
      }
      if ( ! isset ( $data [ $key ] ) && $value == 1 ) {
        return false ;
      }
    }
    return true ;
  }

  public function arrayToJson ( $array ) {
    $json = json_encode ( $array , true ) ;
    return $json ;
  }

  public function jsonToArray ( $json , $toArray = false ) {
    $array = json_decode ( $data , $toArray ) ;
    return $array ;
  }

  public function objectToArray ( $object ) {
    $array = ( object ) $object ;
    return $array ;
  }

  public function arrayToObject ( $array ) {
    $object = ( object ) $array ;
    return $object ;
  }

  public function serializeString ( $string ) {
    return ( string ) $string ;
  }

  public function serializeNumber ( $int ) {
    return ( int ) $int ;
  }

  public function isString ( $string ) {
    return preg_match ( '~^[0-9a-z]+$~i' , $string ) ;
  }

  public function isNumeric ( $number ) {
    return preg_match ( '~^[0-9]+$~i' , $number ) ;
  }

  public function sign ( $array = array () ) {
    return array_merge ( $array , $this -> info () ) ;
  }

  public function info ( $populate = false ) {
    $info = array_merge ( $this -> info ,
      array (
        "___ip"    => $this -> getIp () ,
        "___stamp" => date ( 'Y-m-d H:i:s T' , time () )
      )
    ) ;
    if ( is_array ( $populate ) ) {
      return array_merge ( $info , $populate ) ;
    } else {
      return $info ;
    }
  }

  public function undefined () {
    return 'undefined' ;
  }

  public function getIp () {
    if ( ! $access = $this -> validateIp ( getenv ( 'HTTP_CLIENT_IP' ) ) )
      if ( ! $access = $this -> validateIp ( getenv ( 'HTTP_X_FORWARDED_FOR' ) ) )
        if ( ! $access = $this -> validateIp ( getenv ( 'HTTP_X_FORWARDED' ) ) )
          if ( ! $access = $this -> validateIp ( getenv ( 'HTTP_FORWARDED_FOR' ) ) )
            if ( ! $access = $this -> validateIp ( getenv ( 'HTTP_FORWARDED' ) ) )
              if ( ! $access = $this -> validateIp ( getenv ( 'REMOTE_ADDR' ) ) )
                $access = $this -> undefined () ;
    // @NOTE @TODELETE just for developing
    $access = ( $access == '192.168.10.1' ) ? '88.27.211.244' : $access ;
    $ip = htmlspecialchars ( $access , ENT_COMPAT , 'UTF-8' ) ;
    return $ip ;
  }

  public function validateIp ( $ip ) {
    return filter_var ( $ip , FILTER_VALIDATE_IP ) ;
  }

  function commandLineInterfaceAccess () {
    return ( php_sapi_name () === 'cli' ) ;
  }

  // @TODO move to loader
  public static function adaptor ( $adaptor ) {
    if ( ! is_string ( $adaptor ) ) {
      return false ;
    }
    $file = router :: checkDir ( 'adaptor' ) . 'adaptor' . '.' . strtolower ( trim ( $adaptor ) ) . '.' . 'php' ;
    if ( is_file ( $file ) ) {
      try {
        require ( $file ) ;
      } catch ( Exception $e ) {
        return false ;
      }
      // @FIXME
      //if ( class_exists ( 'adaptor' ) ) {
        return new adaptor () ;
      //}
    }
    return false ;
  }

  public static function cleanNamespace ( $route ) {
    return str_replace ( array ( 'payapi\\' , 'controller_' , 'model_' ) , null , $route ) ;
  }

  public function toString ( $data ) {
    if ( ! is_array ( $data ) )
      return $this -> undefined () ;
    return $this -> arrayToJson ( $data ) ;
  }

  public function __toString () {
    return $this -> toString ( $this -> info () ) ;
  }

  public static function single () {
    if ( self :: $single === false ) {
      self :: $single = new self () ;
    }
    return self :: $single ;
  }


}
