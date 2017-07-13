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
*  @param      ( object ) adapt = ( main sdk config || adapt model for plugin )
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
*              $sdk -> localize () ;                                  //-> gets/cache ip decoded localization (plugin adds adapt country and zone ids)
*              $sdk -> localize ( true ) ;                            //-> gets & refresh ip decoded localization cache (plugin adds adapt country and zone ids)
*              $sdk -> settings ( 'public_id' , 'api_key' , true ) ;  //-> verify account & gets/cache instance merchantSettings, also refresh account data
*              $sdk -> settings ( false , false , true ) ;            //-> refresh & gets instance merchantSettings
*              $sdk -> settings () ;                                  //-> gets instance cached merchantSettings
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

use \payapi\config as config ;
use \payapi\adaptor as adaptor ;
use \payapi\entity as entity ;
use \payapi\debug as debug ;
use \payapi\router as router ;
use \payapi\error as error ;
use \payapi\validator as validator ;
use \payapi\loader as load ;
use \payapi\api as api ;

class payapi {

  public static
    $single                    =       false ;

  private
    $version                   =     '0.0.0' ,
    $plugin                    = 'opencart2' ,
    $adapt                    =       false ,
    $debug                     =       false ,
    $config                    =       false ,
    $entity                    =       false ,
    $router                    =       false ,
    $validate                  =       false ,
    $load                      =       false ,
    $api                       =       false ,
    $command                   =       false ,
    $arguments                 =       false ,
    $settings                  =      array (
      "debug"                  =>       true ,
      "staging"                =>       true
    ) ;

  public function __construct ( $adapt ) {
    if ( self :: $single !== false ) {
      return self :: $single ;
    }
    $this -> adapt = $adapt ;
    $this -> load () ;
  }

  private function load () {
    foreach ( glob ( __DIR__ . DIRECTORY_SEPARATOR . 'payapi' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . '*' . '.' . 'php' ) as $core ) {
      require_once $core ;
    }
    $this -> entity = entity :: single () ;
    $this -> config = config :: single ( $this -> settings ) ;
    $this -> debug = debug :: single ( $this -> config -> debug () ) ;
    $this -> error = error :: single () ;
    $this -> entity -> set ( '___info' , ( string ) $this ) ;
    $this -> router = router :: single ( $this -> config -> staging () ) ;
    $this -> debug -> add ( '[app] ' . $this -> entity -> get ( '___info' ) ) ;
    $this -> adaptor = new adaptor ( $this -> adapt , $this -> plugin ) ;
    $this -> entity -> set ( 'adaptor' , $this -> adaptor ) ;
    $this -> entity -> addInfo ( 'sdk_' . __CLASS__ . '_v' , $this -> version ) ;
    $this -> debug -> load () ;
    $this -> debug -> blank ( '//=== LISTENING ==>' ) ;
  }

  public function __call ( $command , $arguments = array () ) {
    return $this -> worker ( $command , $arguments ) ;
  }

  private function worker ( $command , $arguments ) {
    $this -> validate = new validator () ;
    $this -> load = new load () ;
    $this -> api = new api () ;
    $this -> entity -> set ( 'validate' , $this -> validate ) ;
    $this -> entity -> set ( 'load' , $this -> load ) ;
    //-> if ( checkSsl ( $this -> ip () ) ) === true
    if ( $this -> load -> command ( $command ) === true ) {
      //-> filter/validate
      $this -> command = $command ;
      $this -> arguments = $arguments ;
      return $this -> run () ;
    }
    return $this -> api -> returnResponse ( $this -> error -> notValidMethod () ) ;
    //-> else
    //-> return $this -> api -> returnResponse ( $this -> error -> noValidSsl () ) ;
  }

  private function run () {
    $this -> entity -> set ( 'command' , $this -> command ) ;
    $this -> entity -> set ( 'arguments' , $this -> arguments ) ;
    $this -> entity -> set ( 'api' , $this -> api ) ;
    $controller = '\\' . __CLASS__ . '\\command' . $this -> command ;
    $command = new $controller ( $this -> adapt ) ;
    if ( method_exists ( $command , 'run' ) === true ) {
      $public = array ( 'info' , 'settings' ) ;
      if ( $this -> validate -> publicId ( $command -> publicId () ) === true || in_array ( $this -> command , $public ) === true ) {
        $this -> debug -> run ( true ) ;
        return $command -> run () ;
      } else {
        return $this -> api -> returnResponse ( $this -> error -> forbidden () ) ;
      }
    }
  }

  public function __toString () {
    return __CLASS__ . ' SDK v' . $this -> version ;
  }

  public static function single ( $adapt = false ) {
    if ( self :: $single === false ) {
      self :: $single = new self ( $adapt ) ;
    }
    return self :: $single ;
  }


}
