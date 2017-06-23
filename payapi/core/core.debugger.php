<?php

namespace payapi ;

final class debugger {

  public static
    $single                =   false ;

  protected
    $history               =   false ;

  private
    $microtime             =   false ,
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
    $this -> dir = router :: dir ( 'logs' ) ;
    $this -> file = date ( 'Ymd' ) . '.' . __NAMESPACE__ . '.' . 'log' ;
    router :: checkDir ( $this -> dir ) ;
    $this -> reset () ;
    $this -> add ( '___debugging___' ) ;
  }

  private function reset () {
    $this -> history = false ;
    return file_put_contents ( $this -> dir . $this -> file , '' ) ;
  }

  public function add ( $info , $label = 'info' ) {
    $trace = debug_backtrace () ;
    $class = str_replace ( 'payapi\\' , null , ( isset ( $trace [ 2 ] [ 'class' ] ) ) ? str_replace ( '"' , null , $trace [ 2 ] [ 'class' ] ) : $trace [ 1 ] [ 'class' ] ) ;
    $method = str_replace ( '__' , null , ( isset ( $trace [ 2 ] [ 'function' ] ) ) ? str_replace ( '"' , null , $trace [ 2 ] [ 'function' ] ) : $trace [ 1 ] [ 'function' ] ) ;
    $entry = ( date ( 'Y-m-d H:i:s' , time () ) . ' [' . ( string ) trim ( $label ) . '] ' . $class . '->' . $method . ' : ' . ( ( is_string ( $info ) ) ? $info : ( ( is_array ( $info ) ? json_encode ( $info ) : ( ( is_bool ( $info ) || is_object ( $info ) ) ? ( string ) $info : serializer :: undefined () ) ) ) ) ) ;
    $this -> history [] = $entry ;
    return $this -> set ( $entry ) ;
  }

  public function history () {
    return $this -> history ;
  }

  protected function set ( $entry ) {
    return error_log ( $entry . "\n" , 3 , $this -> dir . $this -> file ) ;
  }

  public static function single () {
    if ( self :: $single === false ) {
      self :: $single = new self ();
    }
    return self :: $single ;
  }

  public function __destruct () {
    $this -> add ( '___' . round ( ( microtime ( true ) - $this -> microtime ) , 5 ) . 'ms___' ) ;
    $this -> set ( null ) ;
  }


}
