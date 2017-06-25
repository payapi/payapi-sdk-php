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
    $this -> arguments = $this -> model -> get ( 'arguments' ) ;
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

  protected function arguments ( $key = 0 ) {
    return $this -> model -> arguments ( $key ) ;
  }

  protected function render ( $data , $code = false , $mode = false , $display = true ) {
    return $this -> cgi -> render ( $data , $code , $mode , $display ) ;
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
