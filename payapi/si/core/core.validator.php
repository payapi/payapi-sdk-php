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

  private
    $schemas                   = array () ,
    $sanitizer                 =    false ;

    public function __construct () {
      $this -> sanitizer = new sanitizer () ;
    }

  public function validSchema ( $schema , $data ) {
    $this -> reset () ;
    if ( is_string ( $schema ) && is_array ( $data ) && $this -> schema ( $schema ) && $this -> process ( $data ) ) {
      return true ;
    }
    return false ;
  }

  protected function reset () {
    $this -> schema = false ;
    $this -> data = false ;
  }

  private function schema ( $key ) {
    $file = str_replace ( 'core' . DIRECTORY_SEPARATOR . basename ( __FILE__ ) , null , __FILE__ ) . 'schema' . DIRECTORY_SEPARATOR . 'schema' . '.' . $key . '.' . 'json' ;
    if ( ! is_file ( $file ) ) {
      return false ;
    }
    try {
      $this -> schema = json_decode ( file_get_contents ( $file ) , true ) ;
    } catch ( Exception $e ) {
      return false ;
    }
    return $this -> schema ;
  }

  public function process ( $unvalidated ) {
    $data = $this -> sanitizer -> sanitizeFromSchema ( $this -> schema , $unvalidated ) ;
    foreach ( $this -> schema [ '___schema___' ] as $key => $value ) {
      if ( $key != '___mandatory___' && $key != '___type___' ) {
        // @TODO check value ___type___
        if ( ( ! isset ( $data [ $key ] ) && $value [ '___mandatory___' ] !== false ) ) {
          var_dump ( $key ) ;
          exit () ;
          return false ;
        } else
        if ( isset ( $data [ $key ] ) && isset ( $value [ '___type___' ] ) && ! $this -> check ( $data [ $key ] , $value [ '___type___' ] ) ) {
          return false ;
        }
      }
    }
    // @TODO use sanitized/validated data
    return $data ;
  }

  private function check ( $data , $type = 'string' ) {
    if ( ! is_string ( $type ) ) {
      return false ;
    }
    switch ( $type ) {
      case 'undefined':
        return true ;
      break;
      case 'string':
        return is_string ( $data ) ;
      break;
      case 'url':
        return $this -> isUrl ( $data ) ;
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

  public function isString ( $string ) {
    return is_string ( $string ) ;
  }

  public function isPlainString ( $string ) {
    return preg_match ( '~^[0-9a-z]+$~i' , $string ) ;
  }

  public function isPayload ( $payload , $coded = false ) {
    $dots = ( $coded ) ? 1 : 2 ;
    if ( $this -> isString ( $payload ) && substr_count ( $payload , '.' ) == $dots ) {
      $subpayload = explode ( '.' , $payload ) ;
      if ( isset ( $subpayload [ 0 ] ) && isset ( $subpayload [ 1 ] ) ) {
        if ( $this -> isPlainString ( $subpayload [ 0 ] ) &&  $this -> isString ( $subpayload [ 1 ] ) ) {
          if ( $coded !== false ) {
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

  public function isAlphaNumeric ( $key ) {
    return preg_match ( '~^[0-9a-z]+$~i' , $key ) ;
  }

  public function isNumeric ( $number ) {
    return preg_match ( '~^[0-9]+$~i' , $number ) ;
  }

  protected function isPhoneNumber ( $phone ) {
    // @TODO
    return is_string ( $phone ) ;
  }

  protected function isUrl ( $phone ) {
    // @TODO
    return is_string ( $phone ) ;
  }


}
