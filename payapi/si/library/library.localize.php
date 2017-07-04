<?php

namespace payapi ;

final class localize {

  public
    $localization        =    array () ;

  private
    $ip                  =        null ,              ,
    $endPoint            =       false ,
    $cache               =        true ,
    $folder              =       false ,
    $file                =       false ,
    $expiration          =        '30' ; //-> days

  public function __construct ( $ip = false , $production = false ) {
    if ( filter_var ( $ip  , FILTER_VALIDATE_IP ) === true ) {
      $this -> endPoint = 'https://' . ( ( $production !== true ) ? 'staging-' : null ) . 'input.payapi.io/v1/api/fraud/ipdata/' . $this -> ip ;
      $this -> file =  $this -> folder . 'ip' . md5 ( $this -> ip ) . '.' . 'log' ;
      if ( is_string ( $ip ) === true ) {
        $this -> ip = $ip ;
      } else {
        $this -> ip = $this -> getIp () ;
      }
      $this -> folder = PATH_PRIVATE_IP . md5 ( $this -> ip ) . DIRECTORY_SEPARATOR ;
      $this -> localization = $this -> getInfo () ;
    } else {
      $this -> localization = $this -> undefinedIpInfo () ;
    }
    return ( string ) $this ;
  }

  public function getInfo () {
    $call = $this -> endPoint . $this -> ip ;
    $cached = $this -> checkCache () ;
    if ( $cached === false ) {
      $output = false ;
      $curl = curl_init();
      curl_setopt ( $curl , CURLOPT_URL , $call ) ;
      curl_setopt ( $curl , CURLOPT_RETURNTRANSFER , 1 ) ;
      $output = curl_exec( $curl ) ;
      curl_close( $curl );
      $ipInfo = false ;
      if ( $output ) {
        $ipInfo = json_decode ( $output , true ) ;
      }
      if ( $output == false || $ipInfo === false || isset ( $ipInfo [ 'error' ] ) === true ) {
        $output = $this -> undefinedIpInfo () ;
      }
      if ( $this -> cache === true ) {
        $this -> saveIpData ( $output ) ;
      }
    } else {
      $output = $cached ;
    }
    return $output ;
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
    if ( $ipAddress == '192.168.10.1' && STAGING == 1 ) {
      $ipAddress = '83.49.192.216' ;
    }

    return $ipAddress;
  }

  private function saveIpData ( $ipdata ) {
    if ( ! is_dir ( $this -> folder ) ) {
      mkdir ( $this -> folder , 0750 ) ;
    }
    //-> has to be json ( string )
    file_put_contents ( $this -> file , $ipdata ) ;
  }

  private function checkCache () {
    //-> @NOTE @CARE @2TEST
    return false ;
    if ( is_file ( $this -> file ) === true && filemtime ( $this -> file ) > strtotime ( $this -> expiration . ' days' ) ) {
      $cached = file_get_contents ( $this -> file ) ;
      if ( is_array ( json_decode ( $cached , true ) ) !== false ) {
        return $cached ;
      }
    }
    return false ;
  }

  private function undefinedIpInfo () {
    return array (
      "ip"           => $this -> ip ,
      "countryCode"  => "undefined" ,
      "countryName"  => "undefined" ,
      "regionName"   => "undefined" ,
      "regionCode"   => "undefined" ,
      "postalCode"   => "undefined" ,
      "timezone"     => "undefined" ,
      "isp"          => "undefined"
    ) ;
  }
  //-> to adaptor
  public function getNativeConuntryId () {
    return $_SESSION  [ 'ip' ] [ 'country_id' ] ;
  }

  public function getNativeZoneId () {
    return $_SESSION  [ 'ip' ] [ 'zone_id' ] ;
  }

  public function __toString () {
    return json_encode ( $this -> localization , true ) ;
  }


}
