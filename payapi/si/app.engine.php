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
*       move class content to engine
*
**/

require ( "/opt/php-jwt/vendor/autoload.php" ) ;

use \payapi\cgi as cgi ;
use \payapi\crypter as crypter ;
use \payapi\validator as validator ;
use \payapi\serializer as serializer ;
use \payapi\router as router ;
use \payapi\data as data ;

final class payapi {

  public
    $info                          =              array (
      "___app"                     => 'PayApi Payments' ,
      "___v"                       =>           '0.0.1'
    ) ;

  private
    $validator                     =              false ,
    $serializer                    =              false ,
    $crypter                       =              false ,
    $cgi                           =              false ,
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
    $this -> validator = new validator () ;
    $this -> serializer = serializer :: single () ;
    $this -> data -> set ( 'validator' , $this -> validator ) ;
    $this -> configure ( $config ) ;
    $this -> cgi = new cgi ( $this -> config ) ;
  }

  private function configure ( $config ) {
    $validated = array (
      "mode" => ( ( isset ( $config [ 'mode' ] ) && in_array ( $config [ 'mode' ] , array ( 'json' , 'html' , 'string' , 'array' , 'object' ) ) ) ? $config [ 'mode' ] : 'json' ) ,
      "branding" => ( ( isset ( $config [ 'branding' ] ) && is_string ( $config [ 'branding' ] ) ) ? $config [ 'branding' ] : false ) ,
      "plugin" => ( ( isset ( $config [ 'plugin' ] ) && is_string ( $config [ 'plugin' ] ) && in_array ( $config [ 'plugin' ] , array ( 'opencart' , 'magento' , 'prestashop' , 'default' ) ) ) ? $config [ 'plugin' ] : 'default' ) ,
      "headers" => ( ( isset ( $config [ 'headers' ] ) && $config [ 'headers' ] !== false ) ? true : false ) ,
      "archival" => ( ( isset ( $config [ 'archival' ] ) && $config [ 'archival' ] !== false ) ? true : false ) ,
      "production" => ( ( isset ( $config [ 'production' ] ) && $config [ 'production' ] !== false ) ? true : false ) ,
      "debug" => ( ( isset ( $config [ 'debug' ] ) && $config [ 'debug' ] !== false ) ? true : false ) ,
      "payapi_public_id" => ( isset ( $config [ 'payapi_public_id' ] ) && is_string ( $config [ 'payapi_public_id' ] ) && preg_match ( '~^[0-9a-z]+$~i' , $config [ 'payapi_public_id' ] ) ) ? $config [ 'payapi_public_id' ] : null
    ) ;
    $this -> crypter = new crypter ( md5 ( $validated [ 'payapi_public_id' ] ) ) ;
    $this -> config = array_merge ( $validated ,
      array (
        "encoded_payapi_api_key" => ( isset ( $config [ 'payapi_api_key' ] ) && $this -> validator -> isString ( $config [ 'payapi_api_key' ] ) ) ? $this -> crypter -> encode ( $config [ 'payapi_api_key' ] , $validated [ 'payapi_public_id' ] , true ) : null
      )
    ) ;
    $this -> data -> set ( 'crypter' , $this -> crypter ) ;
    $this -> data -> set ( 'config' , $this -> config ) ;
    $this -> data -> set ( 'info' , $this -> info ) ;
    return true ;
  }

  public function debug ( $info , $label = false ) {
    return $this -> cgi -> debug ( $info , $label ) ;
  }

  public function __call ( $command , $arguments = array () ) {
    //$this -> debug ( json_encode ( $this -> config ) , 'test' ) ;
    if ( isset ( $this -> config [ 'payapi_public_id' ] ) && isset ( $this -> config [ 'encoded_payapi_api_key' ] ) && $this -> validator -> isString ( $this -> config [ 'payapi_public_id' ] ) && $this -> validator -> isPayload ( $this -> config [ 'encoded_payapi_api_key' ] , true ) ) {
      $this -> validate ( $command , $arguments ) ;
      $this -> debug ( '[command] ' . $this -> command );
      $this -> data -> set ( 'arguments' , $arguments ) ;
      $this -> model () ;
      $this -> data -> set ( 'cgi' , $this -> cgi ) ;
      $this -> controller () ;
      if ( method_exists ( $this -> controller , 'run' ) ) {
        return $this -> controller -> run () ;
      }
    } else {
      return $this -> unauthorized () ;
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

  public function __destruct () {
    $this -> debug ( '[destructed]' ) ;
  }


}
