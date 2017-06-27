<?php
namespace payapi ;

abstract class helper {

  protected
    $load                          =             false ,
    $error                         =             false ,
    $debugger                      =             false ,
    $data                          =             false ,
    $config                        =             false ;

  public function __construct () {
    $this -> error = error :: single () ;
    $this -> data = data :: single () ;
    $this -> config = $this -> data -> get ( 'config' ) ;
    $this -> load = $this -> data -> get ( 'loader' ) ;
    $this -> debugger = $this -> data -> get ( 'debug' , false ) ;
    if ( method_exists ( $this , 'auto' ) ) {
      $this -> debug ( '[autoload] ' . serializer :: cleanLogNamespace ( get_called_class () ) ) ;
      //$this -> addInfo () ;
      return $this -> auto () ;
    }
  }

  public function get ( $key = false ) {
    if ( $key === false )
      return $this -> data -> get () ;
    return $this -> data -> get ( $key ) ;
  }

  public function has ( $key ) {
    return $this -> data -> has ( $key ) ;
  }

  public function set ( $key , $value = false ) {
    if ( ! preg_match ( '~^[0-9a-z]+$~i' , $key ) ) {
      $this -> error ( 'use only (string) for key' ) ;
      return false ;
    }
    $this -> data -> set ( $key , $value ) ;
  }

  public function __set ( $key = false , $value = false ) {
    $this -> warning ( 'not allowed ' . $key , '__set' ) ;
  return false ;
  }

  public function __get ( $key = false ) {
    $this -> warning ( 'not allowed ' . $key , '__get' ) ;
    return false ;
  }

  public function info () {
    return $this -> get ( 'info' ) ;
  }

  protected function autoVersionSign () {
    if ( is_string ( $this -> version ) === true && is_string ( get_called_class () ) === true ) {
      $version = $this -> version ;
      $label = get_called_class ( $label . '_' . 'v' , $version ) ;
      $info = $this -> addInfo ( $label , $version ) ;
    }
  }

  public function addInfo ( $key , $info ) {
    //-> filter/validate
    return $this -> data -> addInfo ( $key , $info ) ;
  }

  public function load ( $data ) {
    $loadable = array ( 'model' , 'schema' ) ;
    $split = explode ( '/' , $data ) ;
    if ( ! isset ( $split [ 0 ] ) || ! isset ( $split [ 1 ] ) || ! in_array ( $split [ 0 ] , $loadable ) )
      $this -> error ( 'do not find loadable : ' . $data ) ;
  }

  public function config ( $key = false ) {
    if ( $key === false ) {
      return $this -> config ;
    }
    if ( isset ( $this -> config [ $key ] ) ) {
      return $this -> config [ $key ] ;
    }
    return false ;
  }

  public function debug ( $info , $label = 'info' ) {
    if ( $this -> debugger === false ) {
      return true ;
    }
    return $this -> debugger -> add ( trim ( $info ) , $label ) ;
  }

  public function error ( $errors , $key = 'error' ) {
    $this -> debug ( $errors , $key ) ;
    return $this -> error -> set (  $errors , $key ) ;
  }

  public function warning ( $warning , $label = false ) {
    $extraLabel = ( is_string ( $label ) === true ) ? '[' . $label . '] ' : null ;
    $entry = $extraLabel . $warning ;
    return $this -> error ( $entry , 'warning' ) ;
  }
  //->
  public function fatal (  $errors ) {
    $this -> debug ( $errors , 'fatal' ) ;
    $this -> error -> fatal (  $errors ) ;
    die () ;
  }


}
