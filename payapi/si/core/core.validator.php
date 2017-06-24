<?php
namespace payapi ;
/***
* @param $schema -> schema key string
*                   (e.g. 'transaction')
* returns
*         true/false
*
* @TODO sanitize, unset not expected and to use sanitized data
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

  public function isFilteredSchema ( $filteredSchema ) {
    return $this -> isArray ( $filteredSchema ) ;
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

  public function isAlphaNumeric ( $unvalidatedKey ) {
    return preg_match ( '~^[0-9a-z]+$~i' , $unvalidatedKey ) ;
  }

  public function isNumeric ( $unvalidatedNumber ) {
    return $this -> check ( $unvalidatedNumber , 'number' ) ;
  }

  public function isValidCode ( $unvalidatedCode ) {
    if ( is_int ( $unvalidatedCode ) && preg_match ( '/^\d{3}$/' , $unvalidatedCode ) && $unvalidatedCode <= 600 && $unvalidatedCode >= 200 ) {
      return true ;
    }
    return false ;
  }

  protected function isPhoneNumber ( $unvalidatedPhone ) {
    if ( $this -> check ( $unvalidatedPhone , 'int' ) === true && $unvalidatedPhone > 9999999 && $unvalidatedPhone < 9999999999999999999 ) {
      return true ;
    }
    return false ;
  }

  protected function isUrl ( $unvalidatedUrl ) {
    return $this -> check ( $unvalidatedUrl , 'url' ) ;
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
        return is_string ( $data ) ;
      break;
      case 'url':
      return ( filter_var ( $data , FILTER_VALIDATE_URL ) === true ) ;
      break;
      case 'email':
        return filter_var ( $data , FILTER_VALIDATE_EMAIL ) ;
      break;
      case 'phone':
        return $this -> isPhoneNumber ( $data ) ;
      break;
      case 'int':
        return is_int ( $data ) ;
      break;
      case 'float':
        return is_float ( $data ) ;
      break;
      case 'number':
        return is_numeric ( $data ) ;
      break;
      case 'boolean':
        return is_bool ( $data ) ;
      break;
      case 'array':
        return is_array ( $data ) ;
      break;
      case 'object':
        return is_object ( $data ) ;
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
