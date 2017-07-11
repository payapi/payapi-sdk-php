<?php

namespace payapi ;

final class debug {

  public static
    $single                =   false ;

  protected
    $history               =   false ;

  private
    $enabled               =   false ,
    $microtime             =   false ,
    $fullTrace             =   false ,
    $dir                   =   false ,
    $file                  =   false ,
    $labels                =   array (
      'info' ,
      'error' ,
      'warning' ,
      'fatal'
    );

  protected function __construct ( $enabled ) {
    // $this -> route
    if ( $enabled !== true ) {
      return false ;
    }
    $this -> enabled = $enabled ;
    $this -> microtime = microtime ( true ) ;
    $this -> dir = str_replace ( 'core' , 'debug' , __DIR__ ) . DIRECTORY_SEPARATOR ;
    $this -> file = $this -> dir . 'debug.' . __NAMESPACE__ . '.' . 'log' ;
    $this -> reset () ;
    $this -> blank () ;
    $this -> add ( '[debugger] enabled' ) ;
  }

  private function reset () {
    $this -> history = false ;
    return file_put_contents ( $this -> file , '' ) ;
  }

  public function add ( $info , $label = 'info' ) {
    $trace = $this -> trace ( debug_backtrace () ) ;
    $entry = ( date ( 'Y-m-d H:i:s' , time () ) . ' [' . $this -> label ( $label ) . '] ' . $trace . ' ' . ( ( is_string ( $info ) ) ? $info : ( ( is_array ( $info ) ? json_encode ( $info ) : ( ( is_bool ( $info ) || is_object ( $info ) ) ? ( string ) $info : serializer :: undefined () ) ) ) ) ) ;
    $this -> history [] = $entry ;
    return $this -> set ( $entry ) ;
  }

  private function blank () {
    $this -> set ( ' ' ) ;
  }

  public function trace ( $traced ) {
    $separator = '->' ;
    if ( $this -> fullTrace !== true ) {
      $class = str_replace ( 'payapi\\' , null , ( isset ( $traced [ 3 ] [ 'class' ] ) ) ? str_replace ( '"' , null , $traced [ 3 ] [ 'class' ] ) : ( ( isset ( $traced [ 2 ] [ 'class' ] ) ) ? $traced [ 2 ] [ 'class' ] : $traced [ 1 ] [ 'class' ] ) ) ;
      $function = str_replace ( '__' , null , ( isset ( $traced [ 3 ] [ 'function' ] ) ) ? str_replace ( '"' , null , $traced [ 3 ] [ 'function' ] ) : ( ( isset ( $traced [ 2 ] [ 'function' ] ) ) ? $traced [ 2 ] [ 'function' ] : $traced [ 1 ] [ 'function' ] ) ) ;
      $route = str_replace ( array ( 'payapi\\' , '___') , null , $class . $separator . $function ) ;
      return $route ;
    }
    $levels = 5 ;
    $route = null ;
    for ( $cont = count ( $traced ) ; $cont > 0 ; $cont -- ) {
      $route .= ( ( isset ( $traced [ $cont ] [ 'class' ] ) === true ) ? $traced [ $cont ] [ 'class' ] . $separator : null ) . ( ( isset ( $traced [ $cont ] [ 'function' ] ) === true ) ? $traced [ $cont ] [ 'function' ] . $separator : null ) ;
    }
    return $route ;
  }

  public function label ( $label ) {
    if ( is_string ( $label ) && preg_match ( '~^[a-z]+$~i' , $label ) && in_array ( $label , $this -> labels ) ) {
      return $label ;
    }
    reset ( $this -> labels ) ;
    return current ( $this -> labels ) ;
  }

  public function history () {
    return $this -> history ;
  }

  protected function set ( $entry ) {
    if ( $this -> enabled !== true ) {
      return false ;
    }
    $fileredEntry = filter_var ( $entry , FILTER_SANITIZE_STRING , FILTER_FLAG_NO_ENCODE_QUOTES ) ;
    return error_log ( $fileredEntry . "\n" , 3 , $this -> file ) ;
  }

  public static function single ( $enabled = false ) {
    if ( self :: $single === false ) {
      self :: $single = new self ( $enabled ) ;
    }
    return self :: $single ;
  }

  public function __toString () {
    return serializer :: toString ( $this -> labels ) ;
  }

  public function __destruct () {
    $this -> add ( '[app] timing ' . round ( ( ( microtime ( true ) - $this -> microtime ) ) , 3 ) * 1000 . 'ms.' ) ;
    $this -> blank () ;
  }


}
