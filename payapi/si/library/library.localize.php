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

  public static function getIp () {
    if ( getenv ( 'HTTP_CLIENT_IP' ) !== false ) {
      $ipAddress = getenv ( 'HTTP_CLIENT_IP' ) ;
    } else if ( getenv ( 'HTTP_X_FORWARDED_FOR' ) !== false ) {
      $ipAddress = getenv ( 'HTTP_X_FORWARDED_FOR' ) ;
    } else if ( getenv ( 'HTTP_X_FORWARDED' ) !== false ) {
      $ipAddress = getenv ( 'HTTP_X_FORWARDED' ) ;
    } else if ( getenv ( 'HTTP_FORWARDED_FOR' ) !== false ) {
      $ipAddress = getenv ( 'HTTP_FORWARDED_FOR' ) ;
    } else if ( getenv ( 'HTTP_FORWARDED' ) !== false ) {
      $ipAddress = getenv ( 'HTTP_FORWARDED' ) ;
    } else if ( getenv ( 'REMOTE_ADDR' ) !== false ) {
      $ipAddress = getenv ( 'REMOTE_ADDR' ) ;
    } else {
      $ipAddress = null ;
    }
    //-> @NOTE @CARE!!! @TODELETE after DEV
    if ( $ipAddress == '192.168.10.1' ) {
      $ipAddress = '84.79.234.58' ;
    }

    return $ipAddress;
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
