<?php

namespace payapi ;

final class loader {

  protected
    $root                =      false ,
    $loaded              =      array (
      "controller"       =>  array () ,
      "model"            =>  array () ,
      "library"          =>  array () ,
      "schema"           =>  array ()
    ) ;

  public function __construct () {
    $this -> root = str_replace ( basename ( str_replace ( basename ( __FILE__ ) , null , __FILE__ ) ) . DIRECTORY_SEPARATOR . basename ( __FILE__ ) , null , __FILE__ ) ;
  }

  public function model ( $key ) {
    if ( isset ( $this -> loaded [ 'model' ] [ $key ] ) ) {
      return true ;
    }
    $file = router :: dirCore ( 'model' ) . 'model' . '.' . $key . '.' . 'php' ;
    if ( is_file ( $file ) ) {
      return require ( $file ) ;
    }
    return false ;
  }

  public function schema () {}

  public function adaptor () {}

  public function dir ( $key = false ) {
    $subdir = ( is_string ( $key ) ) ? $key . DIRECTORY_SEPARATOR : null ;
    return $this -> root . $subdir ;
  }

  public function root () {
    return $this -> root ;
  }

  public function loaded ( $key ) {
    if ( isset ( $this -> loaded [ $key ] ) )
      return false ;
    return $this -> loaded [ $key ] ;
  }

  public function __toString () {
    return json_encode ( $this -> loaded , true ) ;
  }


}
