<?php

namespace payapi ;

// @NOTE we will handle merchant personalitations

class branding extends helper {

  private
    $default                  =  'payapi' ,
    $defaulted                =     false ,
    $brandKey                 =     false ,
    $brand                    =     false ;

  public function auto () {
    if ( $this -> config ( 'branding' ) && is_file ( router :: brandingFile ( $this -> config ( 'branding' ) ) ) ) {
      $this -> brandKey = $this -> config ( 'branding' ) ;
      $this -> brand = $this -> getBrand ( $this -> brandKey ) ;
    }
    if ( $this -> brand === false ) {
      $this -> brandKey = $this -> default ;
      $this -> brand = $this -> getBrand ( $this -> brandKey ) ;
      $this -> defaulted = true ;
      $this -> warning ( 'not found, using default' , 'brand' ) ;
    }
    $this -> set ( 'brand' , $this -> brand ) ;
    $this -> debug ( $this -> brandKey , 'brand' ) ;
  }

  private function getBrand ( $key ) {
    $brand = json_decode ( file_get_contents ( router :: brandingFile ( $key ) ) , true ) ;
    if ( $brand === false ) {
      $this -> warning ( 'no valid schema' , 'brand' ) ;
    }
    return $brand ;
  }

  public function getBrandKey () {
    return $this -> brandKey ;
  }


}
