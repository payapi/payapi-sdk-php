<?php

namespace payapi ;

final class loader extends helper {

  protected
    $root                =      false ,
    $loaded              =      array (
      "controller"       =>  array () ,
      "model"            =>  array () ,
      "library"          =>  array () ,
      "schema"           =>  array ()
    ) ;

  protected function auto () {
    $this -> root = str_replace ( basename ( str_replace ( basename ( __FILE__ ) , null , __FILE__ ) ) . DIRECTORY_SEPARATOR . basename ( __FILE__ ) , null , __FILE__ ) ;
  }

  public function model ( $key ) {
    if ( isset ( $this -> loaded [ 'model' ] [ $key ] ) ) {
      return true ;
    }
    $file = router :: dirSi ( 'model' ) . 'model' . '.' . $key . '.' . 'php' ;
    if ( is_file ( $file ) ) {
      return require ( $file ) ;
    }
    return false ;
  }

  public function library ( $key ) {
    $dir = str_replace ( 'core' , 'library' , __DIR__ ) ;
    $library = $dir . DIRECTORY_SEPARATOR . 'library' . '.' . $key . '.' . 'php' ;
    if ( is_file ( $library ) === true ) {
      require ( $library ) ;
      if ( class_exists ( $key ) ) {
        return new $key () ;
      }
    }
    return false ;
  }

  public static function getProccessedFile ( $file ) {
    $output = false ;
    ob_start () ;
    self :: requireFile ( $file ) ;
    $output = ob_get_contents () ;
    ob_end_clean () ;
    return $output ;
  }

  public static function requireFile ( $file ) {
    if ( is_file ( $file ) === true && require ( $file ) !== false ) {
      return true ;
    }
    return false ;
  }


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

  private function dirRoot ()  {
    return $this -> parentDir ( $this -> dirPayapi () ) ;
  }

  public function dirSi ( $key = false ) {
    return $this -> parentDir ( __DIR__ ) . $key ;
  }

  private function parentDir ( $dir ) {
    return str_replace ( DIRECTORY_SEPARATOR . basename ( $dir ) , null , $dir ) . DIRECTORY_SEPARATOR ;
  }

  public function dirPayapi ( $key = false ) {
    $dir = $this -> validString ( $key , true ) ;
    return $this -> parentDir ( $this -> dirSi () ) . $dir ;
  }

  public function archivalTransaction ( $key ) {
    if ( is_string ( $key ) === true ) {
      return $this -> dirPayapi ( 'archival' ) . 'archive' . '.' . $key . '.' . 'log' ;
    }
    return false ;
  }

  private function validString ( $string , $separator = false ) {
    if ( is_string ( $string ) === true ) {
      return strtolower ( trim ( $string ) ) . ( ( $separator ) ? DIRECTORY_SEPARATOR : null ) ;
    } else {
      return null ;
    }
  }

  private function validDir ( $dir ) {
    return preg_match ( '~^[0-9a-z]+$~i' , $dir ) ;
  }


  public function schema () {}

  public function dir ( $key = false ) {
    $subdir = ( is_string ( $key ) ) ? $key . DIRECTORY_SEPARATOR : null ;
    return $this -> root . $subdir ;
  }

  public function root () {
    return $this -> root ;
  }

  public function loaded ( $key ) {
    if ( isset ( $this -> loaded [ $key ] ) )
      return false ;
    return $this -> loaded [ $key ] ;
  }

  public function __toString () {
    return json_encode ( $this -> loaded , true ) ;
  }


}
