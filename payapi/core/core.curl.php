<?php

namespace payapi ;

final class curl extends helper {

  private
    $version               = '0.0.1' ,
    $code                  =   false ;
  //-> @TODO add retry on timeout
  public function proccess ( $url , $post = false , $secured = true , $timeout = 1 , $return = 1 , $header = 0 , $ssl = 1 , $fresh = 1 , $noreuse = 1 , $retried = false ) {
    $this -> debug ( '[' . $this -> method ( $post ) . '] ' . $this -> parseDomain ( $url ) ) ;
    $options = array (
      CURLOPT_URL              => $url ,
      CURLOPT_RETURNTRANSFER   => $return ,
      CURLOPT_HEADER           => $header ,
      CURLOPT_SSL_VERIFYPEER   => $ssl ,
      CURLOPT_FRESH_CONNECT    => $fresh ,
      CURLOPT_FORBID_REUSE     => $noreuse ,
      CURLOPT_TIMEOUT          => $timeout ,
      CURLOPT_HTTPHEADER       => array (
        'User-Agent: ' . __NAMESPACE__ . ' - curl v' . $this -> version
    ) ) ;
    $buffer = curl_init () ;
    curl_setopt_array ( $buffer , $options ) ;
    if ( $post !== false ) {
      $curlPost = http_build_query ( array ( "data" => $post ) ) ;
      curl_setopt ( $buffer , CURLOPT_POSTFIELDS , $curlPost ) ;
    }
    $timeStart = microtime ( true ) ;
    $jsonExpected = curl_exec ( $buffer ) ;
    if ( $jsonExpected != false ) {
      $dataExpected = json_decode ( $jsonExpected , true ) ;
      $code = curl_getinfo ( $buffer , CURLINFO_HTTP_CODE ) ;
      $this -> debug ( 'timing ' . ( round ( ( microtime ( true ) - $timeStart ) , 3 ) * 1000 ) . 'ms.' ) ;
      if ( $this -> isCleanCodeInt ( $code ) === true ) {
        if ( $code === 200 ) {
          if ( $secured !== false ) {
            if ( isset ( $dataExpected [ 'data' ] ) === true && is_string ( $dataExpected [ 'data' ] ) === true && substr_count ( $dataExpected [ 'data' ] , '.' ) === 2 ) { // review dynamic signaures
              $serialized = array (
                "code" => ( int ) ( ( isset ( $dataExpected [ 'code' ] ) === true && $this -> isCleanCodeInt ( $dataExpected [ 'code' ] ) === true ) ? $dataExpected [ 'code' ] : $code ) ,
                "data" => $dataExpected [ 'data' ]
              ) ;
              curl_close ( $buffer ) ;
              return $serialized ;
            } else {
              $this -> error ( 'unexpected data' , 'curl' ) ;
            }
          } else {
            //-> filter objects
            if ( is_array ( $dataExpected ) !== false ) {
              $serialized = array (
                "code" => ( int ) ( ( isset ( $dataExpected [ 'code' ] ) === true && $this -> isCleanCodeInt ( $dataExpected [ 'code' ] ) === true ) ? $dataExpected [ 'code' ] : $code ) ,
                "data" => $dataExpected
              ) ;
              curl_close ( $buffer ) ;
              return $serialized ;
            } else {
              $this -> error ( 'unexpected unsecured data' , 'curl' ) ;
            }
          }
        } else {
          $this -> error ( 'unexpected response' , 'curl' ) ;
        }
      }
    } else {
      curl_close ( $buffer ) ;
      return  $this -> proccess ( $url , $post , $secured , $timeout , $return , $header , $ssl , $fresh , $noreuse , true ) ;
    }
    curl_close ( $buffer ) ;
    return false ;
  }

  private function isCleanCodeInt ( $int ) {
    if ( is_int ( $int ) === true && $int >= 200 && $int <= 600 ) {
      return true ;
    }
    return false ;
  }

  private function method ( $post ) {
    return ( ( $post ===  false ) ? 'GET' : 'POST' ) ;
  }

  private function parseDomain ( $url ) {
    $parsed = parse_url ( $url ) ;
    if ( ! isset ( $parsed [ 'host' ] ) )
      return false ;
    return $parsed [ 'host' ] ;
  }


}
