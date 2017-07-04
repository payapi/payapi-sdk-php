<?php

namespace payapi ;

final class debugger {

  public static
    $single                =   false ;

  protected
    $history               =   false ;

  private
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

  protected function __construct () {
    $this -> microtime = microtime ( true ) ;
    $this -> dir = router :: dirLogs () ;
    $this -> file = date ( 'Ymd' ) . '.' . __NAMESPACE__ . '.' . 'log' ;
    router :: checkDir ( $this -> dir ) ;
    $this -> reset () ;
    //$this -> add ( '[debugger] enabled' ) ;
  }

  private function reset () {
    $this -> history = false ;
    return file_put_contents ( $this -> dir . $this -> file , '' ) ;
  }

  public function add ( $info , $label = 'info' ) {
    $trace = $this -> trace ( debug_backtrace () ) ;
    $entry = ( date ( 'Y-m-d H:i:s' , time () ) . ' [' . $this -> label ( $label ) . '] ' . $trace . ' ' . ( ( is_string ( $info ) ) ? $info : ( ( is_array ( $info ) ? json_encode ( $info ) : ( ( is_bool ( $info ) || is_object ( $info ) ) ? ( string ) $info : serializer :: undefined () ) ) ) ) ) ;
    $this -> history [] = $entry ;
    return $this -> set ( $entry ) ;
  }

  public function trace ( $traced ) {
    $separator = '->' ;
    if ( $this -> fullTrace !== true ) {
      //$class = str_replace ( 'payapi\\' , null , ( ( isset ( $trace [ 2 ] [ 'class' ] ) ) ? $trace [ 2 ] [ 'class' ] : $trace [ 1 ] [ 'class' ] ) ) ;
      //$method = str_replace ( '__' , null , ( ( isset ( $trace [ 2 ] [ 'function' ] ) ) ? $trace [ 2 ] [ 'function' ] : $trace [ 1 ] [ 'function' ] ) ) ;
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
    $fileredEntry = filter_var ( $entry , FILTER_SANITIZE_STRING , FILTER_FLAG_NO_ENCODE_QUOTES ) ;
    return error_log ( $fileredEntry . "\n" , 3 , $this -> dir . $this -> file ) ;
  }

  public static function single () {
    if ( self :: $single === false ) {
      self :: $single = new self () ;
    }
    return self :: $single ;
  }

  public function __toString () {
    return serializer :: toString ( $this -> labels ) ;
  }

  public function __destruct () {
    $this -> add ( 'app timing ' . round ( ( ( microtime ( true ) - $this -> microtime ) ) , 3 ) * 1000 . 'ms.' ) ;
    $this -> set ( null ) ;
  }


}
