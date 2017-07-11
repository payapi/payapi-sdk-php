<?php

namespace payapi ;

abstract class engine extends helper {

  protected
    $customer              =     false ,
    $default               =  'payapi' ,
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
    $settings              =     false ,
    $config                =  array () ,
    $arguments             =     false ;

  protected function ___autoload ( $entity , $native ) {
    $this -> config = $entity -> appConfig () ;
    $this -> entity = $entity ;
    $this -> native ( $native ) ;
    $this -> cache = new cache () ;
    $this -> sdk () ;
  }

  private function sdk () {
    $this -> arguments = $this -> entity -> get ( 'arguments' ) ;
    $this -> validate = $this -> entity -> get ( 'validate' ) ;
    $this -> entity -> remove ( 'validate' ) ;
    $this -> load = $this -> entity -> get ( 'load' ) ;
    $this -> entity -> remove ( 'load' ) ;
    $this -> api = $this -> entity -> get ( 'api' ) ;
    $this -> entity -> remove ( 'api' ) ;
    $this -> crypter = new crypter ( $this -> publicId ) ;
    $this -> token = $this -> crypter -> publicKey ( $this -> publicId ) ;
    $this -> apiKey = $this -> encode ( $this -> adaptor -> apiKey () , $this -> token , true ) ;
    $this -> settings = $this -> cache ( 'read' , 'settings' , $this -> publicId ) ;
    $this -> info () ;
  }

  protected function token () {
    return str_replace ( strtok ( $this -> token , '.' ) . '.' , null , $this -> token ) ;
  }

  private function native ( $native ) {
    $this -> adaptor = new adaptor ( $this -> entity , $native ) ;
    $this -> session = $this -> adaptor -> session () ;
    $this -> db = $this -> adaptor -> db () ;
    $this -> config = $this -> adaptor -> config () ;
    $this -> customer = $this -> adaptor -> customer () ;
    //-> @TODO update to use internal cache
    $this -> publicId = $this -> adaptor -> publicId () ;
  }

  private function info () {
    $this -> debug ( '[run] ' . strtolower ( $this -> entity -> get ( 'command' ) ) ) ;
    $this -> entity -> addInfo ( 'adaptor_' . $this -> entity -> get ( 'plugin' ) . '_v' , $this -> adaptor -> version () ) ;
    $this -> entity -> addInfo ( 'api_v' , ( string ) $this -> api ) ;
    $this -> entity -> addInfo ( 'crypter_v' , ( string ) $this -> crypter ) ;
    $this -> entity -> addInfo ( 'validator_v' , ( string ) $this -> validate ) ;
    $this -> entity -> addInfo ( 'tk' , $this -> token () ) ;
    $this -> entity -> addInfo ( 'public' , $this -> publicId ) ;
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

  public function publicId () {
    return $this -> publicId ;
  }

  protected function apiKey () {
    return $this -> decode ( $this -> apiKey , $this -> token , true ) ;
  }

  public function app ( $key = false ) {
    if ( is_string ( $key ) === true ) {
      if ( isset ( $this -> config [ $key ] ) === true ) {
        return $this -> config [ $key ] ;
      }
    }
    return false ;
  }

  protected function arguments ( $key ) {
    if ( isset ( $this -> arguments [ $key ] ) ) {
      return $this -> arguments [ $key ] ;
    }
    return false ;
  }

  protected function ip () {
    return $this -> api -> ip () ;
  }

  protected function curl ( $url , $post = false , $secured = true , $timeout = 1 , $return = 1 , $header = 0 , $ssl = 1 , $fresh = 1 , $noreuse = 1 ) {
    return $this -> api -> curl ( $url , $post , $secured , $timeout , $return , $header , $ssl , $fresh , $noreuse ) ;
  }

  protected function partialPayments () {
    if ( is_array ( $this -> settings ( 'partialPayments' ) ) !== false ) {
      return true ;
    }
    return false ;
  }

  protected function render ( $data ) {
    $render = $this -> api -> render ( $data , 200 ) ;
    $return = ( ( DEBUGGING ) ? $this -> entity -> addExtradata ( $render ) : $render ) ;
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
    $tokenCoded = $this -> encode ( $token , $this -> token , true ) ;
    $cacheKey = str_replace ( strtok ( $tokenCoded , '.' ) . '.' , null , $tokenCoded ) ;
    switch ( $action ) {
      case 'writte' :
        if ( is_array ( $data ) !== false ) {
          $data [ 'timestamp' ] = $this -> timestamp () ;
        }
        $encryptedData = $this -> encode ( $data , $this -> token , true ) ;
        return $this -> cache -> writte ( $type , $cacheKey , $encryptedData ) ;
      case 'read' :
        $cached = $this -> cache -> read ( $type , $cacheKey ) ;
        if ( $cached !== false ) {
          return $this -> decode ( $cached , $this -> token , true ) ;
        }
      break ;
      default :
        return false ;
      break ;
    }
    return false ;
  }

  private function timestamp () {
    return  microtime ( true ) ;
  }


}
