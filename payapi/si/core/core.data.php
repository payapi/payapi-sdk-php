<?php

namespace payapi ;

final class data {

  public static
    $single            =    false ;

  private
    $data              =   array (
      "debugger"       =>   false ,
      "info"           =>  array ()
    ) ;

  protected function __construct () {}

  public function config ( $key = false ) {
    if ( $key === false ) {
      return $this -> data [ 'config' ] ;
    } else
    if ( ! isset ( $this -> data [ 'config' ] ) ) {
      return false ;
    } else
    if ( ! isset ( $this -> data [ 'config' ] [ $key ] ) ) {
      return $this -> data [ 'config' ] [ $key ] ;
    }
    return false ;
  }

  public function get ( $key = false ) {
    if ( $key === false )
      return $this -> data ;
    return ( ( $this -> has ( $key ) ) ? $this -> data [ $key ] : null ) ;
  }

  public function has ( $key ) {
    return isset ( $this -> data [ $key ] ) ;
  }

  public function set ( $key , $value = false ) {
    if ( ! preg_match ( '~^[0-9a-z]+$~i' , $key ) ) {
      //$this -> error ( 'use only (string) for key' ) ;
      return false ;
    }
    return ( $this -> data [ $key ] = $value ) ;
  }

  public function addInfo ( $key , $info ) {
    $info = array_merge (
      array (
      "___" . ( string ) $key => ( string ) $info
      ) ,
      $this -> get ( 'info' )
    ) ;
    $this -> set ( 'info' , $info ) ;
  }

  public function extradata () {
    return array (
      "___extradata" => $this -> get ( 'info' )
    ) ;
    return $this -> get ( 'info' ) ;
  }

  public function reset ( $fullReset = false) {
    $exceptions = array ( 'config' , 'cgi' ) ; // @TODO update after isolate controller
    foreach ( $this -> data as $key => $value ) {
      if ( ! in_array ( $key , $exceptions ) ) {
        unset ( $this -> data [ $key ] ) ;
      }
    }
  }

  public static function single () {
    if ( self :: $single === false ) {
      self :: $single = new self ;
    }
    return self :: $single ;
  }


}
