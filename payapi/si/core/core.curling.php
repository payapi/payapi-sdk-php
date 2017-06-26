<?php

namespace payapi ;

final class curling extends helper {

  protected
    $version                    =   '0.1' ;

  private
    $response                   =   false ,
    $code                       =   false ,
    $buffer                     =   false ,
    $url                        =   false ,
    $post                       =   false ,
    $return                     =    true ,
    $header                     =   false ,
    $ssl                        =    true ,
    $fresh                      =    true ,
    $noreuse                    =    true ,
    $timeout                    =      15 ;

  public function request ( $url , $post = false , $return = 1 , $header = 0 , $ssl = 1 , $fresh = 1 , $noreuse = 1 , $timeout = 15 ) {
    $timeStart = microtime ( true ) ;
    $this -> debug ( $this -> getHostNameFromUrl ( $url ) ) ;
    $this -> reset () ;
    $options = array
      (
        CURLOPT_URL              => $url ,
        CURLOPT_RETURNTRANSFER   => $return ,
        CURLOPT_HEADER           => $header ,
        CURLOPT_SSL_VERIFYPEER   => $ssl ,
        CURLOPT_FRESH_CONNECT    => $fresh ,
        CURLOPT_FORBID_REUSE     => $noreuse ,
        CURLOPT_TIMEOUT          => $timeout ,
        CURLOPT_HTTPHEADER       => array (
          'User-Agent: ' . __NAMESPACE__ . ' - curl v' . $this -> version
        )
      )
    ;
    $this -> buffer = curl_init () ;
    curl_setopt_array ( $this -> buffer , $options ) ;
    if ( $post != false ) {
      $curlPost = http_build_query ( array ( "data" => $post ) ) ;
      curl_setopt ( $this -> buffer , CURLOPT_POSTFIELDS , $curlPost ) ;
    }
    $response = json_decode ( curl_exec ( $this -> buffer ) , true ) ;
    $code = ( int ) addslashes ( curl_getinfo ( $this -> buffer , CURLINFO_HTTP_CODE ) ) ;
    $this -> debug ( 'timing ' . ( round ( ( microtime ( true ) - $timeStart ) , 3 ) * 1000 ) . 'ms.' ) ;
    if ( $this -> isCleanCodeInt ( $code ) === true ) {
      $this -> code = $code ;
      if ( $this -> code === 200 ) {
        if ( $this -> isCleanArray ( $response ) === true ) {
          $this -> response = $response ;
        } else {
          $this -> warning ( 'object blocked' , 'curl' ) ;
          $this -> response = $this -> unvalidCurlResponse () ;
        }
      } else {
        $this -> warning ( 'unexpected response' ) ;
        $this -> response = $this -> unvalidCurlResponse ( $this -> code ) ;
      }
    } else {
      $this -> response = $this -> unvalidCurlResponse () ;
    }
    curl_close ( $this -> buffer ) ;
    return $this -> response ;
  }

  public function getHostNameFromUrl ( $url ) {
    $parse = parse_url ( $url ) ;
    if ( isset ( $parse [ 'host' ] ) === true ) {
      return $parse [ 'host' ] ;
    }
    return false ;
  }

  public function curlErrorUnexpectedCurlResponse () {
    return array (
      "code" => $this -> error -> errorUnexpectedCurlResponse () ,
      "data" => 'curl schema error'
    ) ;
  }

  private function isCleanCodeInt ( $int ) {
    if ( is_int ( $int ) === true && $int >= 200 && $int <= 600 ) {
      return true ;
    }
    return false ;
  }

  public function unvalidCurlResponse ( $responseCode = false ) {
    $responseCode = ( is_numeric ( $responseCode ) === true ) ? $responseCode : 'error' ;
    $response = array (
      "code"  => $this -> error -> errorUnexpectedCurlResponse () ,
      "data"  => "error." . ( string ) $responseCode
    ) ;
    return $response ;
  }

  public function response () {
    return $this -> response ;
  }

  protected function reset () {
    $this -> response = false ;
    $this -> buffer = false ;
  }

  private function isCleanArray ( $data ) {
    if ( is_array ( $data ) === true && $this -> noObjectsAndFloats ( $data ) === true ) {
      return true ;
    }
    return false ;
  }
  //-> duplicated in filterer
  private function noObjectsAndFloats ( $unfilteredArray ) {
    foreach ( $unfilteredArray as $filtering ) {
      if ( is_array ( $filtering ) === true ) {
        if ( $this -> noObjectsAndFloats ( $filtering ) !== true ) {
          return false ;
        }
      }
      if ( is_string ( $filtering ) === true || is_int ( $filtering ) === true || is_bool ( $filtering ) ) {
        return true ;
      }
    }
    return false ;
  }


}
