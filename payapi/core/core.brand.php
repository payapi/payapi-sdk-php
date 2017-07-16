<?php

namespace payapi ;

class brand extends helper {

  private
    $brand                     = array () ,
    $brands                    =    false ;

  public function ___autoload ( $brand ) {
    return $this -> read ( $brand ) ;
  }

  public function info () {
    return $this -> brand ;
  }

  private function call ( $key ) {
    if ( isset ( $this -> brand [ $key ] ) ) {
      return $this -> brand [ $key ] ;
    }
    //-> @NOTE
    return null ;
  }

  private function read ( $key ) {
    $brand = json_decode ( file_get_contents ( $this -> route -> brand ( $key ) ) , true ) ;
    if ( isset ( $brand [ 'partnerId' ] ) === true && md5 ( $key ) === md5 ( $brand [ 'partnerId' ] ) ) {
      $this -> brand = $brand ;
      $this -> brands [ $key ] = $this -> brand ;
      return $this -> brand ;
    }
    return $this -> returnResponse ( $this -> error -> notFound () ) ;
  }

  public function __call ( $key , $arguments = array () ) {
    if ( $arguments === array () && is_string ( $key ) === true ) {
      return $this -> call ( $key ) ;
    }
    return $this -> returnResponse ( $this -> error -> badRequest () ) ;
  }


}
