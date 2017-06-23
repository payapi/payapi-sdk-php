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
    $schemas                   = array () ;


  public function validate ( $schema , $data ) {
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

  public function process ( $data ) {
    foreach ( $this -> schema [ '___schema___' ] as $key => $value ) {
      if ( $key != '___mandatory___' && $key != '___type___' ) {
        // @TODO check value ___type___
        if ( ( ! isset ( $data [ $key ] ) && $value [ '___mandatory___' ] !== false ) ) {
          return false ;
        } else
        if ( isset ( $data [ $key ] ) && isset ( $value [ '___type___' ] ) && ! $this -> check ( $data [ $key ] , $value [ '___type___' ] ) ) {
          return false ;
        }
      }
    }
    return true ;
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
      case 'email':
        return filter_var ( $data , FILTER_VALIDATE_EMAIL ) ;
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


}
