<?php

namespace payapi ;

final class router {

  public static function checkDir ( $dir ) {
    if ( is_dir ( $dir ) ) {
      return true ;
    }
    return mkdir ( $dir , 0750 ) ;
  }

  public static function dir ( $key = false ) {
    if ( is_string ( $key ) ) {
      $dir = strtolower ( trim ( $key ) ) . DIRECTORY_SEPARATOR ;
    } else {
      $dir = null ;
    }
    return self :: root () . $dir ;
  }

  private static function root ()  {
    return str_replace ( 'core' . DIRECTORY_SEPARATOR . basename ( __FILE__ ) , null , __FILE__ ) ;
  }


}
