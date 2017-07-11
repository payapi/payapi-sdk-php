<?php

namespace payapi ;

final class cache extends helper {

  private
    $dir                   =   false ,
    $cache                 =   array (
      //             expiration days
      "transaction"        =>     30 ,
      "localize"           =>     30 ,
      "settings"           =>  false ,
      "callback"           =>     90
    ) ;

  public function read ( $key , $token ) {
    $file = $this -> validate ( $key , $token ) ;
    if ( is_string ( $file ) === true ) {
      if ( is_file ( $file ) ) {
        $this -> debug ( '[' . $key . '] cached' ) ;
        $cacheInfo = date ( '' ,filemtime ( $file ) ) ;
        if ( $this -> cache [ $key ] === false || filemtime ( $file ) > strtotime ( "-" . $this -> cache [ $key ] . " days" ) ) {
          return file_get_contents ( $file ) ;
        } else {
          $this -> debug ( '[' . $key . '] cache expired' ) ;
        }
      } else {
        $this -> debug ( '[' . $key . '] uncached' ) ;
      }
    }
    $this -> debug ( '[' . $key . '] no valid key' ) ;
    return false ;
  }

  public function delete ( $key , $token ) {
    if ( is_string ( $file = $this -> validate ( $key , $token ) ) === true ) {
      if ( is_file ( $file ) ) {
        return unlink ( $file ) ;
      } else {
        $this -> error ( 'to delete cache file not found' ) ;
      }
    }
    return false ;
  }

  public function writte ( $key , $token , $data ) {
    if ( is_string ( $file = $this -> validate ( $key , $token ) ) === true ) {
      //-> checks data is encrypted
      if ( is_string ( $data ) === true && substr_count ( $data , '.' ) === 1 ) {
        return file_put_contents ( $file , $data , LOCK_EX ) ;
      } else {
        $this -> error ( 'cache data not properly encrypted' ) ;
      }
    }
    return false ;
  }

  protected function validate ( $key , $token ) {
    if ( isset ( $this -> cache [ $key ] ) ) {
      if ( is_string ( $token ) === true ) {
        return $this -> route -> cache ( $key , $token ) ;
      } else {
        $this -> error ( '[cache] token no valid' ) ;
      }
    } else {
      $this -> error ( '[cache] key no valid' ) ;
    }
    return false ;
  }


}
