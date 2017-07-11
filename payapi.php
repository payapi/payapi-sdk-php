<?php
/**
*
*  CORE PayApi OC v2.3 plugin v0.2 (BOC)
*  PHP +v5.x
*  https://payapi.io/
*
*  @package  PayApi OC v2.3 plugin v0.2 (BOC)
*  @category Payments, Social Sharing
*  @version  v0.0.0.2 (20170704)
*
*  @param constant     ___error___           defined undefined string
*  @param constant     ___HTTPS___           defined https url prefix string
*  @param constant     STAGING               BOC PROD/STAG servers FLAG
*  @param constant     DEBUGGING             BOC writte info into log FLAG
*  @param constant     BOC_VERSION           BOC version string
*  @param constant     DIR_PRIVATE           OC system log dir string
*  @param constant     DIR_SEP               OC system DIRECTORY_SEPARATOR string
*  @param object       $oc                   OC $registry
*
*  @uses   OC $registry
*  @uses   \Log
*          if DEBUGGING == true
*
*
*  @author florin
*  @copyright PayApi Ltd
*  @license GPL v3.0
*
*  @todo ever!
*        move debug load here, pass debugging set
*
*  $this -> localize () ;
*  $this -> settings ( 'public_id' , 'api_key' , true ) ;
*  $this -> partialPayment ( $totalInCents , $currency ) ;
*  $this -> response ( 600 ) ;
*  $this -> info () ;
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
      return $command -> run () ;
    }
  }

  public function __toString () {
    return __CLASS__ . ' SDK v' . $this -> version ;
  }


}
