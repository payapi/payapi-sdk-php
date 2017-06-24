<?php

namespace payapi ;

final class router {

  public static function checkDir ( $dir ) {
    if ( is_dir ( $dir ) ) {
      return true ;
    }
    return mkdir ( $dir , 0750 ) ;
  }

  public static function brandingFile ( $key ) {
    if ( self :: isAlphaNumeric ( $key ) ) {
      return self :: dirPrivate ( 'branding' ) . 'branding' . '.' . $key . '.' . 'json' ;
    }
    return false ;
  }

  public static function dirPrivate ( $key = false ) {
    $dir = self :: validFolder ( $key , true ) ;
    return self :: dirPayapi ( 'private' ) . $dir ;
  }

  public static function dirSi ( $key = false ) {
    $dir = self :: validFolder ( $key , true ) ;
    return self :: parentDir ( __DIR__ ) . $dir ;
  }

  public static function dirPayapi ( $key = false ) {
    $dir = self :: validFolder ( $key , true ) ;
    return self :: parentDir ( self :: dirSi () ) . $dir ;
  }

  public static function dirCore ( $key = false ) {
    $dir = self :: validFolder ( $key , true ) ;
    return __DIR__ . $dir  ;
  }

  public static function dirLogs () {
    return self :: dirRoot () . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR ;
  }

  private static function dirRoot ()  {
    return self :: parentDir ( self :: dirPayapi () ) ;
  }

  private static function parentDir ( $dir ) {
    return str_replace ( DIRECTORY_SEPARATOR . basename ( $dir ) , null , $dir ) . DIRECTORY_SEPARATOR ;
  }

  private static function validFolder ( $folder , $separator = false ) {
    if ( is_string ( $folder ) ) {
      return strtolower ( trim ( $folder ) ) . ( ( $separator ) ? DIRECTORY_SEPARATOR : null ) ;
    } else {
      return null ;
    }
  }
  // duplicated in serializer
  private static function isAlphaNumeric ( $key ) {
    return preg_match ( '~^[0-9a-z]+$~i' , $key ) ;
  }


}
