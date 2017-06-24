<?php

// @NOTE not used -> moved to cgi

final class response {


  protected
    $code                 =  false ,
    $data                 =  false ;

  private
    $default              =    600 ,
    $responses            =  array (
      "200" => 'success' ,
      "404" => 'not found'
    ) ;

  public function construct ( $code = false , $data = false ) {
    $this -> code  = ( in_array ( $code , $this -> responses ) ) ? $code : $this -> default ;
    $this -> data = $data ;

    return ( string ) $this ;
  }

  public function __toString () {
    return json_encode ( array (
      "code" => $this -> code ,
      "data" => $this -> data
    ) , true ) ;
  }


}
