<?php

namespace payapi ;

final class data {

  public static
    $single            =       false ;

  private
    $signatureKey      = 'extradata' ,
    $prefix            =       '___' ,
    $signature         =       false ,
    $data              =     array (
      "debugger"       =>      false ,
      "info"           =>     array ()
    ) ;

  protected function __construct () {
    $this -> signature = $this -> prefix . $this -> signatureKey ;
  }

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
        $this -> prefix . ( string ) $key => ( string ) $info
      ) ,
      $this -> get ( 'info' )
    ) ;
    $this -> set ( 'info' , $info ) ;
  }

  public function extradata () {
    return array (
      $this -> signature => $this -> get ( 'info' )
    ) ;
  }

  public function sanitizeSignature ( $array ) {
    if ( isset ( $array [ $this -> signature ] ) ) {
      unset ( $array [ $this -> signature ] ) ;
    }
    return $array ;
  }
  //-> sanitize output privates2
  public function sanitizePrivate ( $array ) {
    $sanitizing = array ( 'tk' , 'sign' ) ;
    foreach ( $sanitizing as $sanitize ) {
      if ( isset ( $array [ $this -> signature ] [ $this -> prefix . $sanitize ] ) ) {
        unset ( $array [ $this -> signature ] [ $this -> prefix . $sanitize ] ) ;
      }
    }
    return $array ;
  }

  public function addSignature ( $array ) {
    return array_merge (
      $this -> sanitizeSignature ( $array ) ,
      $this -> extradata
    ) ;
  }

  public function signature () {
    return $this -> signature ;
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
