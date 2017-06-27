<?php

namespace payapi ;

abstract class controller extends helper {

  protected
    $model                         =             false ,
    $response                      =             false ;

  private
    $cgi                           =             false ,
    $arguments                     =             false ;

  protected function auto () {
    $this -> cgi = $this -> data -> get ( 'cgi' ) ;
    $this -> model = $this -> data -> get ( 'model' ) ;
    $this -> data -> set ( 'model' , false ) ;
    $this -> error = error :: single () ;
    $this -> arguments = $this -> get ( 'arguments' , false ) ;
    $this -> initialized () ;
  }

  public function validSchema ( $schema , $data ) {
    return $this -> model -> validSchema ( $schema , $data ) ;
  }

  protected function initialized () {
    if ( $this -> load && $this -> cgi && $this -> model ) {
      return true ;
    }
    $this -> fatal ( 'cannot init app' ) ;
    die ( 'boo boo' ) ;
  }

  protected function knock () {
    return $this -> cgi -> knock () ;
  }

  protected function curl ( $url , $data = null , $return = 1 , $header = 0 , $ssl = 0 , $fresh = 1 , $noreuse = 1 , $timeout = 15 ) {
    $curlResponse = $this -> cgi -> curl ( $url , $data , $return , $header , $ssl , $fresh , $noreuse , $timeout ) ;
    if ( $curlResponse !== false ) {
      $validator = $this -> validSchema ( 'response.standard' , $curlResponse ) ;
      if ( is_array ( $validator ) === true ) {
        return $validator ;
      }
    }
    return false ;
  }

  public function arguments ( $key = 0 ) {
    if ( ! isset ( $this -> arguments [ $key ] ) ) {
      return false ;
    }
    return $this -> arguments [ $key ] ;
  }

  protected function render ( $data , $code = false , $mode = false , $display = true ) {
    if ( is_array ( $data ) !== false && isset ( $data [ '___extradata' ] ) === false ) {
      $signed = $this -> signature ( $data ) ;
    } else {
      $signed = $data ;
    }
    return $this -> cgi -> render ( $signed , $code , $mode , $display ) ;
  }

  public function signature ( $populate = false ) {
    if ( is_array ( $populate ) === true ) {
      return array_merge ( $populate, $this -> extradata () ) ;
    } else {
      return $this -> extradata () ;
    }
  }

  public function extradata () {
    return $this -> data -> extradata () ;
  }

  protected function code ( $code ) {
    return $this -> cgi -> code ( $code ) ;
  }

  protected function response ( $code ) {
    $response = $this -> cgi -> response ( $code ) ;
    return $this -> cgi -> render ( $response , $code ) ;
  }

  public function __toString () {
    return json_encode ( $this -> model -> info () , true ) ;
  }

  public function __destruct () {}


}
