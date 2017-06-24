<?php

namespace payapi ;

// @NOTE we will handle merchant personalitations

class branding extends helper {

  private
    $default                  =  'payapi' ,
    $defaulted                =     false ,
    $key                      =     false ,
    $brand                    =     false ;

  public function auto () {
    if ( $this -> config ( 'branding' ) && is_file ( router :: brandingFile ( $this -> config ( 'branding' ) ) ) ) {
      $this -> key = $this -> config ( 'branding' ) ;
      $this -> brand = $this -> getBrand ( $this -> key ) ;
    }
    if ( $this -> brand === false ) {
      $this -> key = $this -> default ;
      $this -> brand = $this -> getBrand ( $this -> key ) ;
      $this -> defaulted = true ;
      $this -> warning ( 'not found, using default' , 'brand' ) ;
    }
    $this -> set ( 'brand' , $this -> brand ) ;
    $this -> debug ( $this -> key , 'brand' ) ;
  }

  private function getBrand ( $key ) {
    $brand = json_decode ( file_get_contents ( router :: brandingFile ( $key ) ) , true ) ;
    if ( $brand === false ) {
      $this -> warning ( 'no valid schema' , 'brand' ) ;
    }
    return $brand ;
  }


}
