<?php
namespace payapi ;
/***
* @param $schema -> schema key string
*                   (e.g. 'transaction')
* returns
*         true/false
*
* @TODO sanitize, unset not expected and to use sanitizer data
*
***/

final class validator {

  protected
    $version                  =   '0.0.1' ;

  private
    $schemas                   = array () ,
    $dirs                      = array () ;

  public function __construct () {
    $this -> dirs [ 'schemas' ] = router :: dirSi ( 'schema' ) ;
  }

  protected function reset () {
    $this -> schemas = false ;
  }

  public function getSchema ( $getSchema , $refresed = false ) {
    $schema = addslashes ( $getSchema ) ;
    $file = $this -> dirs [ 'schemas' ] . 'schema' . '.' . $schema . '.' . 'json' ;
    if ( is_file ( $file ) !== true ) {
      return false ;
    } else
    if ( isset ( $this -> schemas [ $schema ] ) === true && $refresed !== true ) {
      return $this -> schemas [ $schema ] ;
    }
    try {
      $schemaFileData = json_decode ( file_get_contents ( $file ) , true ) ;
    } catch ( Exception $e ) {
      return false ;
    }
    if ( is_array ( $schemaFileData ) === true && isset ( $schemaFileData [ '___info___' ] ) && is_array ( $schemaFileData [ '___info___' ] ) === true && isset ( $schemaFileData [ '___schema___' ] ) && is_array ( $schemaFileData [ '___schema___' ] ) === true ) {
    $this -> schemas [ $schema ] = $schemaFileData ;
    return $this -> schemas [ $schema ] ;
    }
    return false ;
  }

  public function schema ( $schema , $data ) {
    if ( is_array ( $this -> schemas [ $schema ] ) === true ) {
      foreach ( $this -> schemas [ $schema ] [ '___schema___' ] as $key => $value ) {
        if ( $key != '___mandatory___' && $key != '___type___' ) {
          if ( ( isset ( $data [ $key ] ) !== true && $value [ '___mandatory___' ] !== false ) ) {
            return false ;
          } else
          if ( isset ( $data [ $key ] ) && isset ( $value [ '___type___' ] ) && ! $this -> check ( $data [ $key ] , $value [ '___type___' ] ) ) {
            return false ;
          }
        }
      }
      return $data ;
    }
    return false ;
  }

  public function isString ( $string ) {
    return $this -> check ( $string , 'string' ) ;
  }

  public function isArray ( $array ) {
    return $this -> check ( $array , 'array' ) ;
  }

  public function isObject ( $object ) {
    return $this -> check ( $object , 'object' ) ;
  }

  public function isPlainString ( $string ) {
    return preg_match ( '~^[0-9a-z]+$~i' , $string ) ;
  }

  public function isfiltererSchema ( $filtererSchema ) {
    return $this -> isArray ( $filtererSchema ) ;
  }

  public function isPayload ( $payload , $crypted = false ) {
    $dots = ( $crypted ) ? 1 : 2 ;
    if ( $this -> isString ( $payload ) && substr_count ( $payload , '.' ) == $dots ) {
      $subpayload = explode ( '.' , $payload ) ;
      if ( isset ( $subpayload [ 0 ] ) && isset ( $subpayload [ 1 ] ) ) {
        if ( $this -> isPlainString ( $subpayload [ 0 ] ) &&  $this -> isString ( $subpayload [ 1 ] ) ) {
          if ( $crypted !== false ) {
            return true ;
          } else {
            if ( isset ( $subpayload [ 2 ] ) && $this -> isPlainString ( $subpayload [ 2 ] ) ) {
              return true ;
            }
          }
        }
      }
    }
    return false ;
  }

  public function isAlphaNumeric ( $unvalidatorKey ) {
    return preg_match ( '~^[0-9a-z]+$~i' , $unvalidatorKey ) ;
  }

  public function isNumeric ( $unvalidatorNumber ) {
    return $this -> check ( $unvalidatorNumber , 'number' ) ;
  }

  public function isValidCode ( $unvalidatorCode ) {
    if ( is_int ( $unvalidatorCode ) && preg_match ( '/^\d{3}$/' , $unvalidatorCode ) && $unvalidatorCode <= 600 && $unvalidatorCode >= 200 ) {
      return true ;
    }
    return false ;
  }

  protected function isPhoneNumber ( $unvalidatorPhone ) {
    if ( $this -> check ( $unvalidatorPhone , 'int' ) === true && $unvalidatorPhone > 9999999 && $unvalidatorPhone < 9999999999999999999 ) {
      return true ;
    }
    return false ;
  }

  protected function isEmail ( $email ) {
    return filter_var ( $data , FILTER_VALIDATE_EMAIL ) ;
  }

  protected function isUrl ( $unvalidatorUrl ) {
    return $this -> check ( $unvalidatorUrl , 'url' ) ;
  }

  public function knock () {
    $knock = file_get_contents ( "php://input" ) ;
    if ( is_string ( $knock ) === true ) {
      return $knock ;
    }
    return false ;
  }

  private function check ( $data , $type = 'string' ) {
    if ( ! is_string ( $type ) ) {
      return false ;
    }
    switch ( $type ) {
      case 'undefined':
        if ( is_object ( $data ) !== true ) {
          return true ;
        }
        return false ;
      break;
      case 'string':
        return ( is_string ( $data ) === true ) ;
      break;
      case 'url':
      return ( filter_var ( $data , FILTER_VALIDATE_URL ) === true ) ;
      break;
      case 'email':
        return ( filter_var ( $data , FILTER_VALIDATE_EMAIL ) === true ) ;
      break;
      case 'phone':
        return $this -> isPhoneNumber ( $data ) ;
      break;
      case 'int':
        return ( is_int ( $data ) === true ) ;
      break;
      case 'float':
        return ( is_float ( $data ) === true ) ;
      break;
      case 'number':
        return ( is_numeric ( $data ) === true ) ;
      break;
      case 'boolean':
        return ( is_bool ( $data ) === true ) ;
      break;
      case 'array':
        return ( is_array ( $data ) !== false ) ;
      break;
      case 'object':
        return ( is_object ( $data ) === true ) ;
      break;
      default:
        return false ;
      break;
    }
  }

  public function __toString () {
    return serializer :: toString ( $this -> version ) ;
  }


}
