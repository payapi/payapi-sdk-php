<?php

namespace payapi ;

abstract class engine {

  protected
    $error                         =             false ,
    $validator                     =             false ,
    $serializer                    =             false ,
    $debugger                      =             false ,
    $data                          =             false ,
    $config                        =             false ;

  public function __construct () {
    $this -> serializer = serializer :: single () ;
    $this -> error = error :: single () ;
    $this -> data = data :: single () ;
    $this -> config = $this -> data -> get ( 'config' ) ;
    if ( $this -> config ( 'debug' ) ) {
      $this -> debugger = debugger :: single () ;
    }
    if ( method_exists ( $this , 'auto' ) ) {
      $this -> debug ( 'autoload : ' . serializer :: cleanNamespace ( get_called_class () ) ) ;
      return $this -> auto () ;
    }
  }

  public function get ( $key = false ) {
    if ( $key === false )
      return $this -> model -> data -> get () ;
    return $this -> model -> data -> get ( $key ) ;
  }

  public function has ( $key ) {
    return $this -> model -> data -> has ( $key ) ;
  }

  public function set ( $key , $value = false ) {
    if ( ! preg_match ( '~^[0-9a-z]+$~i' , $key ) ) {
      $this -> error ( 'use only (string) for key' ) ;
      return false ;
    }
    $this -> model -> data -> set ( $key , $value ) ;
  }

  public function __set ( $key = false , $value = false ) {
    $this -> warning ( 'method not allowed : __set' ) ;
    return false ;
  }

  public function __get ( $key = false ) {
    $this -> warning ( 'method not allowed : __get ' . $key ) ;
    return false ;
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

  protected function valid ( $schema , $data ) {
    if ( ! $schema || ! is_array ( $data ) ) {
      return false ;
    }
    if ( $this -> validator === false ) {
      $this -> validator = new validator () ;
    }
    if ( $this -> validator -> validate ( $schema , $data ) ) {
      return true ;
    }
    return false ;
  }

  public function debug ( $info , $label = 'info' ) {
    if ( $this -> debugger === false ) {
      return true ;
    }
    return $this -> debugger -> add ( $info , $label ) ;
  }

  public function error (  $errors , $key = 'error' ) {
    $this -> debug ( $errors , $key ) ;
    return $this -> error -> set (  $errors , $key ) ;
  }

  public function warning ( $warning ) {
    return $this -> debug ( $warning , 'warning' ) ;
  }

  public function fatal (  $errors ) {
    $this -> debug ( $errors , 'fatal' ) ;
    $this -> error -> fatal (  $errors ) ;
    die () ;
  }


}
