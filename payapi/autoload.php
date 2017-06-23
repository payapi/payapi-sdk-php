<?php
/**
*  PayApi Payments PHP Library
*  PHP v5.4+
*  https://payapi.io/
*
*  @package    PayApi Payments PHP Library
*  @category   Payments, Social Sharing
*  @version    v0.0.0.1 (20170616)

*  @copyright  PayApi Ltd
*  @license    GPL v3.0?
*
*  @REQUIRE
*              JWT
*              curl
*
* @TODO
*       encode payapi data account!
*
**/

require ( "/opt/php-jwt/vendor/autoload.php" ) ;

use \payapi\cgi as cgi ;
use \payapi\debugger as debugger ;
use \payapi\data as data ;

final class payapi {

  public
    $info                          =              array (
      "___app"                     => 'PayApi Payments' ,
      "___v"                       =>           '0.0.1'
    ) ;

  private
    $cgi                           =              false ,
    $debug                         =              false ,
    $command                       =              false ,
    $arguments                     =              false ,
    $config                        =           array () ,
    $data                          =              false ,
    $model                         =              false ,
    $controller                    =              false ,
    $error                         =              false ,
    $commands                      =              array (
                                                 "info" ,
                                             "settings" ,
                                          "transaction" ,
                                             "callback" ,
                                             "validate" ,
                                                "error"
                                                      ) ;

  public function __construct ( $config = array () ) {
    foreach ( glob ( __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . '*' . '.' . 'php' ) as $core ) {
      require $core ;
    }
    $this -> data = data :: single () ;
    $this -> configure ( $config ) ;
    if ( $this -> config [ 'debug' ] ) {
      $this -> debug = debugger :: single () ;
    }
    $this -> cgi = new cgi ( $this -> config ) ;
  }

  private function configure ( $config ) {
    $this -> config = array (
      "mode" => ( ( isset ( $config [ 'mode' ] ) && in_array ( $config [ 'mode' ] , array ( 'json' , 'html' , 'string' , 'array' , 'object' ) ) ) ? $config [ 'mode' ] : 'json' ) ,
      "branding" => ( ( isset ( $config [ 'branding' ] ) && is_string ( $config [ 'branding' ] ) ) ? $config [ 'branding' ] : false ) ,
      "plugin" => ( ( isset ( $config [ 'plugin' ] ) && is_string ( $config [ 'plugin' ] ) && in_array ( $config [ 'plugin' ] , array ( 'opencart' , 'magento' , 'prestashop' , 'default' ) ) ) ? $config [ 'plugin' ] : 'default' ) ,
      "headers" => ( ( isset ( $config [ 'headers' ] ) && $config [ 'headers' ] !== false ) ? true : false ) ,
      "archival" => ( ( isset ( $config [ 'archival' ] ) && $config [ 'archival' ] !== false ) ? true : false ) ,
      "production" => ( ( isset ( $config [ 'production' ] ) && $config [ 'production' ] !== false ) ? true : false ) ,
      "debug" => ( ( isset ( $config [ 'debug' ] ) && $config [ 'debug' ] !== false ) ? true : false ) ,
      "payapi_public_id" => ( isset ( $config [ 'payapi_public_id' ] ) && is_string ( $config [ 'payapi_public_id' ] ) && preg_match ( '~^[0-9a-z]+$~i' , $config [ 'payapi_public_id' ] ) ) ? $config [ 'payapi_public_id' ] : false ,
      "payapi_api_key" => ( isset ( $config [ 'payapi_api_key' ] ) && is_string ( $config [ 'payapi_api_key' ] ) && preg_match ( '~^[0-9a-z]+$~i' , $config [ 'payapi_api_key' ] ) ) ? $config [ 'payapi_api_key' ] : false
    ) ;
    $this -> data -> set ( 'config' , $this -> config ) ;
    $this -> data -> set ( 'info' , $this -> info ) ;
    return true ;
  }

  public function debug ( $info , $label = 'info' ) {
    if ( $this -> debugger === false ) {
      return true ;
    }
    return $this -> debugger -> add ( $info , $label ) ;
  }

  public function __call ( $command , $arguments = array () ) {
    if ( $this -> config [ 'payapi_public_id' ] === false || $this -> config [ 'payapi_public_id' ] === false ) {
      return $this -> unauthorized () ;
    }
    $this -> validate ( $command , $arguments ) ;
    $this -> data -> set ( 'arguments' , $arguments ) ;
    $this -> model () ;
    $this -> data -> set ( 'cgi' , $this -> cgi ) ;

    $this -> controller () ;
    if ( method_exists ( $this -> controller , 'run' ) ) {
      return $this -> controller -> run () ;
    }
  }

  private function validate ( $command , $arguments ) {
    if ( in_array ( $command , $this -> commands ) ) {
      $this -> command = $command ;
      $this -> arguments = $arguments ;
    } else {
      $this -> command = 'error' ;
      $this -> arguments = array () ;
    }
  }

  protected function unauthorized () {
    $this -> cgi -> error ( 'no valid payapi account' ) ;
    return $this -> cgi -> render ( $this -> cgi -> response ( 403 ) , 403 ) ;
  }

  private function model () {
    if ( is_file ( $this -> file ( 'model' ) ) ) {
      require ( $this -> file ( 'model' ) ) ;
      $class = '\payapi\model_' . $this -> command ;
    } else {
      $class = '\payapi\model' ;
    }
    $this -> model = new $class () ;
    $this -> data -> set ( 'model' , $this -> model ) ;
  }

  private function controller () {
    require ( $this -> file ( 'controller' ) ) ;
    $class = '\payapi\controller_' . $this -> command ;
    $this -> controller = new $class () ;
  }

  private function file ( $key ) {
    return ( __DIR__ . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . $key . '.' . $this -> command . '.' . 'php' );
  }

  public function __toString () {
    return json_encode ( $this -> info , true ) ;
  }

  public function __destruct () {}


}
