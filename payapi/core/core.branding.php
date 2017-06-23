<?php

namespace payapi ;

// @NOTE we will handle merchant personalitations

class branding extends engine {

  private
    $default                  =  'payapi' ,
    $key                      =     false ,
    $brand                    =  array () ;

  public function auto () {
    if ( $this -> config ( 'branding' ) && is_file ( router :: brandingFile ( $this -> config ( 'branding' ) ) ) ) {
      $this -> key = $this -> config ( 'branding' ) ;
      $this -> debug ( 'brand : ' . $this -> key ) ;
    } else {
      $this -> key = $this -> default ;
      $this -> warning ( 'brand not found, using default' ) ;
    }
    $this -> brand = $this -> serializer -> jsonToArray ( file_get_contents ( router :: brandingFile ( $this -> key ) ) , true );
    $this -> data -> set ( 'branding' , $this -> brand ) ;
  }


}
