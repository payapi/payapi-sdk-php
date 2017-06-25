<?php
/***
*
*  @NOTE
*        @info()-return has to be an array
*
***/

namespace payapi ;

final class serializer {

  protected
    $version                  =   '0.0.1' ;

  public function validate ( $schema , $data ) {
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

  public function responses () {
    return $this -> responses ;
  }

  public function arrayToJson ( $array ) {
    $json = json_encode ( $array , true ) ;
    return $json ;
  }

  public function jsonToArray ( $json , $toArray = false ) {
    $array = json_decode ( $json , $toArray ) ;
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

  public function sign ( $array = array () ) {
    return array_merge ( $array , $this -> info () ) ;
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

  public function commandLineInterfaceAccess () {
    if ( function_exists ( 'php_sapi_name' ) ) {
      if ( php_sapi_name () === 'cli' ) {
        return true ;
      }
    } //-> else { /** this should trigger a alert/warning **/ }
    return false ;
  }

  public function intLenght ( $int ) {
    if ( preg_match ( "/^\d{10}$/" , $int ) === true ) {
      return true ;
    }
    return false ;
  }

  // @TODO move to loader
  public static function adaptor ( $adaptor ) {
    if ( ! is_string ( $adaptor ) ) {
      return false ;
    }
    $file = router :: dirCore ( 'adaptor' ) . 'adaptor' . '.' . strtolower ( trim ( $adaptor ) ) . '.' . 'php' ;
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

  public static function cleanLogNamespace ( $route ) {
    return str_replace ( array ( 'payapi\\' , 'controller_' , 'model_' ) , null , $route ) ;
  }

  public function undefined () {
    return 'undefined' ;
  }

  public static function undefinedToString () {
    return json_encode ( array (
      "error" => "undefined"
    )  ) ;
  }

  public static function toString ( $data ) {
    if ( is_string ( $data ) === true ) {
      return $data ;
    } else
    if ( is_array ( $data ) === true ) {
      return json_encode ( $data , true ) ;
    }
    return self :: undefinedToString () ;
  }

  public function __toString () {
    return self :: toString ( $this -> version ) ;
  }


}
