<?php

namespace payapi ;

final class plugin {

  public
    $version                   =        '0.0.0' ;

  private
    $entity                    =          false ;

  public function __construct ( $entity ) {
    $this -> entity = $entity ;
    $this -> config = $this -> entity -> get ( 'config' ) ;
  }

  public function config () {
    return false ;
  }

  public function session () {
    return false ;
  }

  public function db () {
    return false ;
  }

  public function customer () {
    return false ;
  }

  public function debugging () {
    return false ;
  }

  public function version () {
    return $this -> version ;
  }

  public function settings () {
    return false ;
  }

  public function publicId () {
    return false ;
  }

  public function __toString () {
    return $this -> version ;
  }


}
