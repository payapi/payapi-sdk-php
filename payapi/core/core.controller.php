<?php

namespace payapi ;

abstract class controller extends helper {

  protected
    $customer              =     false ,
    $entity                =     false ,
    $session               =     false ,
    $data                  =     false ,
    $token                 =     false ,
    $cache                 =     false ,
    $validate              =     false ,
    $sanitizer             =     false ,
    $load                  =     false ,
    $adaptor               =     false ,
    $db                    =     false ;

  private
    $api                   =     false ,
    $crypter               =     false ,
    $publicId              =     false ,
    $apiKey                =     false ,
    $account               =     false ,
    $settings              =     false ,
    $arguments             =     false ;

  protected function ___autoload ( $native ) {
    $this -> entity = entity :: single () ;
    $this -> crypter = new crypter () ;
    $this -> cache = new cache () ;
    $this -> brand = new brand ( 'payapi' ) ;
    $this -> validate = $this -> entity -> get ( 'validate' ) ;
    $this -> api = $this -> entity -> get ( 'api' ) ;
    $this -> account = $this -> cache ( 'read' , 'account' , $this -> instance () ) ;
    if ( is_string ( $this -> cache ( 'read' , 'ssl' , $this -> api -> ip () ) ) !== true ) {
      $validated = $this -> validate -> ssl () ;
      if ( is_resource ( $validated ) === true ) {
        $this -> cache ( 'writte' , 'ssl' , $this -> api -> ip () , ( string ) $validated ) ;
      } else {
        return $this -> api -> returnResponse ( $this -> error -> noValidSsl () ) ;
      }
    }
    $this -> adaptor = $this -> entity -> get ( 'adaptor' ) ;
    $this -> sdk () ;
  }

  private function sdk () {
    $this -> arguments = $this -> entity -> get ( 'arguments' ) ;
    $this -> entity -> remove ( 'validate' ) ;
    $this -> load = $this -> entity -> get ( 'load' ) ;
    $this -> entity -> remove ( 'load' ) ;
    $this -> entity -> remove ( 'api' ) ;
    $this -> publicId = $this -> publicId () ;
    if ( $this -> validate -> publicId ( $this -> publicId ) === true ) {
      $this -> settings = $this -> cache ( 'read' , 'settings' , $this -> instance () ) ;
      $this -> token = $this -> crypter -> instanceToken ( $this -> publicId () ) ;
      $this -> entity -> addInfo ( 'public' , $this -> publicId () ) ;
      $this -> entity -> addInfo ( 'tk' , $this -> token () ) ;
    } else {
      $this -> entity -> addInfo ( 'public' , 'anonymous' ) ;
    }
    $this -> info () ;
  }

  public function instance () {
    return $this -> instance ;
  }

  protected function token () {
    return str_replace ( strtok ( $this -> token , '.' ) . '.' , null , $this -> token ) ;
  }

  private function info () {
    $this -> entity -> addInfo ( 'by' , $this -> brand -> partnerName () . ', ' . $this -> brand -> partnerSlogan () ) ;
    $this -> debug ( '[run] ' . strtolower ( $this -> entity -> get ( 'command' ) ) ) ;
    $this -> entity -> addInfo ( 'adaptor_' . $this -> entity -> get ( 'plugin' ) . '_v' , $this -> adaptor -> version () ) ;
    $this -> entity -> addInfo ( 'api_v' , ( string ) $this -> api ) ;
    $this -> entity -> addInfo ( 'crypter_v' , ( string ) $this -> crypter ) ;
    $this -> entity -> addInfo ( 'validator_v' , ( string ) $this -> validate ) ;
    $this -> entity -> addInfo ( 'sanitizer_v' , ( string ) $this -> sanitizer ) ;
    $this -> entity -> addInfo ( 'serializer_v' , ( string ) $this -> serialize ) ;
  }
  //-> SDK passed argument(s)
  protected function arguments ( $key ) {
    //-> to filter
    if ( isset ( $this -> arguments [ 0 ] [ $key ] ) ) {
      return $this -> arguments [ 0 ] [ $key ] ;
    }
    return false ;
  }
  //-> merchantSettings
  protected function settings ( $key = false ) {
    if ( $this -> settings == false ) {
      return false ;
    }
    if ( $key == false ) {
      return $this -> settings ;
    }
    if ( isset ( $this -> settings [ $key ] ) ) {
      return $this -> settings [ $key ] ;
    }
    return false ;
  }
  //-> account login
  public function publicId () {
    return $this -> account ( 'publicId' ) ;
  }

  protected function apiKey () {
    $this -> debug ( 'encKey decoded ' ) ;
    return $this -> decode ( $this -> account ( 'apiKey' ) , false , true ) ;
  }

  private function account ( $key ) {
    if ( is_array ( $this -> account ) !== false ) {
      if ( isset ( $this -> account [ $key ] ) === true ) {
        return $this -> account [ $key ] ;
      }
    }
    return false ;
  }

  protected function ip () {
    return $this -> api -> ip () ;
  }

  protected function curl ( $url , $post = false , $secured = true , $timeout = 1 , $return = 1 , $header = 0 , $ssl = 1 , $fresh = 1 , $noreuse = 1 ) {
    return $this -> api -> curl ( $url , $post , $secured , $timeout , $return , $header , $ssl , $fresh , $noreuse ) ;
  }

  protected function knock () {
    return $this -> api -> knock () ;
  }

  protected function partialPayments () {
    if ( is_array ( $this -> settings ( 'partialPayments' ) ) !== false ) {
      return true ;
    }
    return false ;
  }

  protected function render ( $data ) {
    $render = $this -> api -> render ( $data , 200 ) ;
    $return = ( ( $this -> config -> debug () === true ) ? $this -> entity -> addExtradata ( $render ) : $render ) ;
    return $return ;
  }

  protected function response ( $code ) {
    return $this -> api -> response ( $code ) ;
  }

  protected function returnResponse ( $code ) {
    return $this -> api -> returnResponse ( $code ) ;
  }

  public function decode ( $encoded , $hash = false , $crypted = false ) {
    return $this -> crypter -> decode ( $encoded , $hash , $crypted ) ;
  }

  public function encode ( $decoded , $hash = false , $crypted = false ) {
    return $this -> crypter -> encode ( $decoded , $hash , $crypted ) ;
  }

  protected function cache ( $action , $type , $token , $data = false ) {
    $tokenCoded = $this -> encode ( $token , false , true ) ;
    $cacheKey = str_replace ( strtok ( $tokenCoded , '.' ) . '.' , null , $tokenCoded ) ;
    switch ( $action ) {
      case 'writte' :
        if ( is_array ( $data ) !== false ) {
          $data [ 'timestamp' ] = $this -> timestamp () ;
        }
        $encryptedData = $this -> encode ( $data , false , true ) ;
        return $this -> cache -> writte ( $type , $cacheKey , $encryptedData ) ;
      case 'read' :
        $cached = $this -> cache -> read ( $type , $cacheKey ) ;
        if ( $cached !== false ) {
          return $this -> decode ( $cached , false , true ) ;
        }
      break ;
      default :
        return false ;
      break ;
    }
    return false ;
  }

  public function endPointLocalization ( $ip ) {
    $api = $this -> https () . $this -> staging () . 'input' . '.' . 'payapi' . '.' . 'io' . '/' . 'v1' . '/' . 'api' . '/' . 'fraud' . '/' . 'ipdata' . '/' . $ip ;
    return $api ;
  }

  public function endPointSettings ( $publicId ) {
    $api = $this -> https () . $this -> staging () . $this -> api () . 'merchantSettings' . '/' . $publicId ;
    return $api ;
  }

  private function api () {
    return 'input' . '.' . 'payapi' . '.' . 'io' . '/' . 'v1' . '/' . 'api' . '/' ;
  }

  private function https () {
    return 'https' . ':' . '//' ;
  }

  private function staging () {
    $route = ( ( $this -> config -> staging () === true ) ? 'staging' . '-' : null ) ;
    return $route ;
  }

  private function timestamp () {
    return  microtime ( true ) ;
  }


}
