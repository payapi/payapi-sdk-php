<?php

namespace payapi ;

final class validator extends helper {

  public
    $version                    = '0.0.1' ;

  public function command ( $command ) {
    //->
    if ( is_string ( $command ) === true ) {
      return true ;
    }
    return false ;
  }

  public function schema ( $data , $schema ) {
    if ( $this -> noObjects ( $data ) === true ) {
      if ( isset ( $schema [ '___schema' ] ) === true && is_array ( $schema [ '___schema' ] ) !== false ) {
        $error = 0 ;
        foreach ( $schema [ '___schema' ] as $key => $value ) {
          if ( isset ( $data [ $key ] ) === true ) {
            $min = ( ( isset ( $value [ '___min' ] ) === true && is_int ( $value [ '___min' ] ) === true ) ? $value [ '___min' ] : false ) ;
            $max = ( ( isset ( $value [ '___max' ] ) === true && is_int ( $value [ '___max' ] ) === true ) ? $value [ '___max' ] : false ) ;
            if ( $this -> check ( $data [ $key ] , $value [ '___type' ] , $min , $max ) !== true ) {
              $error ++ ;
              $this -> error ( 'no valid value : ' . $key , 'warning' ) ;
            }
          } else if ( $value [ '___mandatory' ] !== false ) {
            $error ++ ;
            $this -> error ( 'mandatory field missed' , 'warning' ) ;
          }
        }
        if ( $error === 0 ) {
          return true ;
        }
      } else {
        $this -> error ( 'no valid schema' , 'warning' ) ;
      }
    }
    return false ;
  }

  public function checkString ( $string , $min = false , $max = false ) {
    if ( is_string ( $string ) === true ) {
      $error = 0 ;
      if ( is_int ( $min ) && strlen ( $string ) < $min ) {
        $error ++ ;
      }
      if ( is_int ( $max ) && strlen ( $string ) > $max ) {
        $error ++ ;
      }
      if ( $error === 0 ) {
        return true ;
      }
    }
    return false ;
  }

  public function checkArray ( $array , $min = false , $max = false ) {
    if ( is_array ( $array ) !== false && $this -> noObjects ( $array ) === true ) {
      $error = 0 ;
      if ( is_int ( $min ) && count ( $array ) < $min ) {
        $error ++ ;
      }
      if ( is_int ( $max ) && count ( $array ) > $max ) {
        $error ++ ;
      }
      if ( $error === 0 ) {
        return true ;
      }
    }
    return false ;
  }

  private function checkSetting ( $setting , $min , $max ) {
    if ( $setting === false || $this -> checkArray ( $setting , $min , $max ) === true ) {
      return true ;
    }
    return false ;
  }

  public function checkInteger ( $integer , $min = false , $max = false ) {
    if ( is_int ( $integer ) === true ) {
      $error = 0 ;
      if ( is_int ( $min ) && $integer < $min ) {
        $error ++ ;
      }
      if ( is_int ( $max ) && $integer > $max ) {
        $error ++ ;
      }
      if ( $error === 0 ) {
        return true ;
      }
    }
    return false ;
  }

  public function checkNumber ( $number , $min = false , $max = false ) {
    if ( is_numeric ( $number ) === true ) {
      return $this -> integer ( (int ) $number ) ;
    }
    return false ;
  }

  private function check ( $data , $type , $min = false , $max = false ) {
    switch ( $type ) {
      case 'string':
        return $this -> checkString ( $data , $min , $max ) ;
      break;
      case 'integer':
        return $this -> checkInteger ( $data , $min , $max ) ;
      break;
      case 'number':
        return $this -> checkNumber ( $data ) ;
      break;
      case 'ip':
        return $this -> ip ( $data ) ;
      break;
      case 'setting':
        return $this -> checkSetting ( $data , $min , $max ) ;
      break;
      case 'array':
        return $this -> checkArray ( $data , $min , $max ) ;
      break;
      default:
        return false ;
      break;
    }
    return false ;
  }

  public function ip ( $ip ) {
    if ( filter_var ( $ip , FILTER_VALIDATE_IP ) !== false && ip2long ( $ip ) !== false ) {
      return true ;
    }
    return false ;
  }
  //->
  public function publicId ( $apiKey ) {
    if ( $this -> checkString ( $apiKey , 4 , 250 ) === true ) {
      return true ;
    }
    return false ;
  }
  //->
  public function apiKey ( $apiKey ) {
    if ( $this -> checkString ( $apiKey , 4 , 250 ) === true ) {
      return true ;
    }
    return false ;
  }

  private function noObjects ( $data ) {
    //-> @FIXME
    return true ;
    if ( is_array ( $data ) !== false ) {
      foreach ( $data as $key => $value ) {
        if ( $this -> noObjects ( $value ) !== true ) {
          return false ;
        }
      }
    } else
    if ( is_object ( $data ) !== true ) {
      return true ;
    }
    return false ;
  }

  public function __toString () {
    return $this -> version ;
  }


}
