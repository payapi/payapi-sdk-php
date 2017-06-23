<?php

namespace payapi ;

final class model_curl {


  protected
    $buffer                     =   false ,
    $response                   =   false ;

  private
    $version                    =   '0.1' ,
    $url                        =   false ,
    $data                       =   false ,
    $return                     =    true ,
    $header                     =   false ,
    $ssl                        =    true ,
    $fresh                      =    true ,
    $noreuse                    =    true ,
    $timeout                    =      15 ;

  public function request ( $url , $data = null , $return = 1 , $header = 0 , $ssl = 1 , $fresh = 1 , $noreuse = 1 , $timeout = 15 ) {
    $this -> reset () ;
    //$this -> log ( 'curling : ' . $url ) ;
    $this -> response = false ;
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
    if ( $data != false ) {
      $post = http_build_query ( array ( "data" => $data ) ) ;
      //var_Dump ( $post ) ; exit () ;

      curl_setopt ( $this -> buffer , CURLOPT_POSTFIELDS , $post ) ;
    }
    $response = curl_exec ( $this -> buffer ) ;
    $this -> response = array (
      "code"  => curl_getinfo ( $this -> buffer , CURLINFO_HTTP_CODE ) ,
      "data"  => $response
    ) ;
    curl_close ( $this -> buffer ) ;
    return $this -> response ;
  }

  public function response () {
    return $this -> response ;
  }

  protected function reset () {
    $this -> request = false ;
    $this -> response = false ;
    $this -> buffer = false ;
  }


}
