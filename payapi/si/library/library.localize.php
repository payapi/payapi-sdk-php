<?php

//-> just basic model

final class localize {

  public
    $localization        =    array () ;

  private
    $ip                  =        null ,
    $endPoint            =       false ,
    $schema              =       array (
      "ip"               => 1 ,
      "countryCode"      => 1 ,
      "countryName"      => 1 ,
      "regionName"       => 1 ,
      "regionCode"       => 1 ,
      "postalCode"       => 1 ,
      "timezone"         => 1 ,
      "isp"              => 1
    ) ;

  public function __construct ( $ip = false , $production = false ) {
    if ( filter_var ( $ip  , FILTER_VALIDATE_IP ) !== false ) {
      $this -> ip = $ip ;
    } else {
      $this -> ip = $this -> getIp () ;
    }
    if ( filter_var ( $this -> ip  , FILTER_VALIDATE_IP ) !== false ) {
      $this -> endPoint = ( 'https://' . ( ( $production != true ) ? 'staging-' : null ) . 'input.payapi.io/v1/api/fraud/ipdata/' ) ;
      $this -> localization = $this -> getInfo () ;
    } else {
      $this -> localization = $this -> undefinedIpInfo () ;
    }
    return ( string ) $this ;
  }

  public function getInfo () {
    $call = $this -> endPoint . $this -> ip ;
    $output = false ;
    $curl = curl_init();
    curl_setopt ( $curl , CURLOPT_URL , $call ) ;
    curl_setopt ( $curl , CURLOPT_RETURNTRANSFER , 1 ) ;
    $output = curl_exec( $curl ) ;
    curl_close( $curl );
    $ipInfo = false ;
    if ( $output ) {
      $ipInfo = json_decode ( $output , true ) ;
      if ( $ipInfo != false ) {
        return $ipInfo ;
      }
    }
    return $this -> undefinedIpInfo () ;
  }

  private function getIp () {
    if ( ( $access = $this -> validateIp ( getenv ( 'HTTP_CLIENT_IP' ) ) ) == false )
      if ( ( $access = $this -> validateIp ( getenv ( 'HTTP_X_FORWARDED_FOR' ) ) ) == false )
        if ( ( $access = $this -> validateIp ( getenv ( 'HTTP_X_FORWARDED' ) ) ) == false )
          if ( ( $access = $this -> validateIp ( getenv ( 'HTTP_FORWARDED_FOR' ) ) ) == false )
            if ( ( $access = $this -> validateIp ( getenv ( 'HTTP_FORWARDED' ) ) ) == false )
              if ( ( $access = $this -> validateIp ( getenv ( 'REMOTE_ADDR' ) ) ) == false )
                $access = null ;
    //-> @NOTE @TODELETE just for developing
    $access = ( $access == '192.168.10.1' ) ? '84.79.234.58' : $access ;
    $ip = htmlspecialchars ( $access , ENT_COMPAT , 'UTF-8' ) ;
    return $ip ;
  }

  public function validateIp ( $env ) {
    if ( filter_var ( $env , FILTER_VALIDATE_IP ) !== false && ip2long ( $env ) !== false ) {
      return preg_replace ( "/[^0-0.\d ]/i" , '' , $env ) ;
    }
    return false ;
  }

  private function undefinedIpInfo () {
    $undefined = array () ;
    foreach ( $this -> schema as $key => $value ) {
      if ( $key !== 'ip' ) {
        $undefined [ $key ] = 'undefined' ;
      } else {
        $undefined [ $key ] = $this -> ip ;
      }
    }
    return $undefined ;
  }

  public function __toString () {
    return json_encode ( $this -> localization , true ) ;
  }


}
