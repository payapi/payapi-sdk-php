<?php
/**
*
*  @package    PayApi PHP SDK
*              https://payapi.io/
*
*  @uses       PHP +v5.x
*              JWT
*
*  @category   Payments, Social Sharing
*  @version    v0.0.0.0 (20170711)
*
*  @param      ( object ) native = ( main sdk config || native model for plugin )
*
*  @NOTE
*        - 1. to log into sdk thought :
*              $sdk -> settings ( $payapi_public_id , $payapi_api_enc_key ) ;
*              * this returns merchantSettings payload if valid
*                and creates an encrypted cache for both:
*                payapi/cache/<instanceKey>/account
*                payapi/cache/<instanceKey>/settings
*
*        - 2. commands :
*              $sdk -> callback () ;                                  //-> gets/cache callback decoded transaction
*              $sdk -> localize () ;                                  //-> gets/cache ip decoded localization (plugin adds native country and zone ids)
*              $sdk -> settings () ;                                  //-> gets instance cached merchantSettings
*              $sdk -> settings ( 'public_id' , 'api_key' , true ) ;  //-> refresh & gets instance merchantSettings
*              $sdk -> partialPayment ( $totalInCents , $currency ) ; //-> calculate partialPayment from merchantSettings
*                                                                          @TODO review using cached/plugin
*              $sdk -> response ( <standard_response_code_int> ) ;    //-> get response info
*              $sdk -> info () ;                                      //-> gets sdk debug info
*
*  @return
*        - success :
*              array (
*                "code"  =>    ( int )            200 ,
*                "data"  =>  ( array ) <expectedData>
*              ) ;
*
*        - error :
*              array (
*                "code"  =>    ( int )    <errorCode> ,
*                "error" => ( string )    <errorData>
*              ) ;
*
*
*  @author     florin
*  @copyright  PayApi Ltd
*  @license    GPL v3.0
*
*
*  @TODO       ever!
*              finish OC isolation
*              sdk default noplugin settings
*
*
**/

use \payapi\entity as entity ;
use \payapi\debug as debug ;
use \payapi\error as error ;
use \payapi\validator as validator ;
use \payapi\loader as load ;
use \payapi\api as api ;

class payapi {

  private
    $version                   =     '0.0.0' ,
    $plugin                    = 'opencart2' ,
    $native                    =       false ,
    $debug                     =       false ,
    $entity                    =       false ,
    $router                    =       false ,
    $validate                  =       false ,
    $load                      =       false ,
    $api                       =       false ,
    $command                   =       false ,
    $arguments                 =       false ;

  public function __construct ( $native = false ) {
    $this -> native = $native ;
    foreach ( glob ( __DIR__ . DIRECTORY_SEPARATOR . 'payapi' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . '*' . '.' . 'php' ) as $core ) {
      require $core ;
    }
    $this -> worker () ;
  }

  public function __call ( $command , $arguments = array () ) {
    return $this -> run ( $command , $arguments ) ;
  }

  private function worker () {
    $this -> entity = new entity () ;
    $this -> debug = debug :: single ( true ) ;
    $this -> entity -> set ( '___info' , ( string ) $this ) ;
    $this -> debug -> add ( '[app] ' . $this -> entity -> get ( '___info' ) ) ;
    $this -> validate = new validator () ;
    $this -> load = new load () ;
    $this -> api = new api () ;
    $this -> error = error :: single () ;
    $this -> entity -> set ( 'validate' , $this -> validate ) ;
    $this -> entity -> set ( 'load' , $this -> load ) ;
    $this -> entity -> set ( 'plugin' , $this -> plugin ) ;
    $this -> entity -> addInfo ( 'sdk_' . __CLASS__ . '_v' , $this -> version ) ;
  }

  private function configuration () {
    $this -> entity -> config ( 'debug' , true ) ;
  }

  private function run ( $command , $arguments ) {
    //-> filter/validate
    if ( $this -> load -> command ( $command ) === true ) {
      $this -> command = $command ;
      $this -> arguments = $arguments ;
    } else {
      return $this -> api -> returnResponse ( $this -> error -> notValidMethod () ) ;
    }
    $this -> entity -> set ( 'command' , $this -> command ) ;
    $this -> entity -> set ( 'arguments' , $this -> arguments ) ;
    $controller = '\\' . __CLASS__ . '\\command' . $this -> command ;
    $this -> entity -> set ( 'api' , $this -> api ) ;
    $command = new $controller ( $this -> entity , $this -> native ) ;
    if ( method_exists ( $command , 'run' ) === true ) {
      $public = array ( 'info' , 'settings' ) ;
      if ( $this -> validate -> publicId ( $command -> publicId () ) === true || in_array ( $this -> command , $public ) === true ) {
        return $command -> run () ;
      } else {
        return $this -> api -> returnResponse ( 403 ) ;
      }
    }
  }

  public function __toString () {
    return __CLASS__ . ' SDK v' . $this -> version ;
  }


}
