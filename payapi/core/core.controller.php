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
    $localized             =     false ,
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

  public function locate () {
    //-> @TODO adapt/validate : adaptor -> ()
    //->       just for customer not general
    $this -> localized = $this -> localization () ;
    //var_dump ( '___d' , $this -> ip () , $this -> localized , $this -> publicId () ) ; exit () ;
    if ( isset ( $this -> localized [ 'ip' ] ) === true && isset ( $this -> localized [ 'countryCode' ] ) === true ) {
      return true ;
    }
    $this -> error (  'not localized' , 'warning' ) ;
    return false ;
  }

  private function localization () {
    $ip = $this -> ip () ;
    $cached = $this -> cache ( 'read' , 'localize' , $ip ) ;
    if ( $cached !== false ) {
      return $cached ;
    }
    $endPoint = $this -> serialize -> endPointLocalization ( $ip ) ;
    $request = $this -> curl ( $endPoint , false , false ) ;
    if ( $request !== false && isset ( $request [ 'code' ] ) === true ) {
      if ( $request [ 'code' ] === 200) {
        $validated = $this -> validate -> schema ( $request [ 'data' ] , $this -> load -> schema ( 'localize' ) ) ;
        if ( is_array ( $validated ) !== false ) {
          $this -> debug ( '[localize] valid schema' ) ;
          $adaptedData = $this -> adaptor -> localized ( $validated ) ;
          $this -> cache ( 'writte' , 'localize' , $ip , $adaptedData ) ;
          return $this -> cache ( 'read' , 'localize' , $ip ) ;
        } else {
          //-> not valid schema from PA
          $this -> error ( 'no valid localization' , 'warning' ) ;
        }
      }
    }
    return false ;
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
      //-> read merchant settings reseller partnerKey
      $this -> brand = $this -> cache ( 'read' , 'reseller' , $this -> settings ( 'reseller' ) ) ;
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
    if ( $this -> brand !== false ) {
      $this -> entity -> addInfo ( 'brand' , $this -> brand ( 'partnerName' ) . ', ' . $this -> brand ( 'partnerSlogan' ) ) ;
    }
    $this -> debug ( '[run] ' . strtolower ( $this -> entity -> get ( 'command' ) ) ) ;
    $this -> entity -> addInfo ( 'adaptor_' . $this -> entity -> get ( 'plugin' ) . '_v' , ( string ) $this -> adaptor ) ;
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

  protected function brand ( $key = false ) {
    if ( $key === false ) {
      return $this -> brand ;
    }
    if ( isset ( $this -> brand [ $key ] ) === true ) {
      return $this -> brand [ $key ] ;
    }
    return false ;
  }

  protected function render ( $data ) {
    $render = $this -> api -> render ( $data , 200 ) ;
    $return = ( ( $this -> config -> debug () === true ) ? $this -> entity -> addExtradata ( $render ) : $render ) ;
    $sanitized =  $this -> sanitize -> render ( $return ) ;
    return $sanitized ;
  }

  protected function response ( $code ) {
    return $this -> api -> response ( $code ) ;
  }

  public function returnResponse ( $code ) {
    $render =$this -> api -> returnResponse ( $code ) ;
    $return = ( ( $this -> config -> debug () === true ) ? $this -> entity -> addExtradata ( $render ) : $render ) ;
    $sanitized =  $this -> sanitize -> render ( $return ) ;
    return $sanitized ;
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
          $data [ 'timestamp' ] = $this -> serialize -> microstamp () ;
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


}
