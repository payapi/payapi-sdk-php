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
*
* @TODO
*       move class content to engine
*
**/

require ( "/opt/php-jwt/vendor/autoload.php" ) ;

use \payapi\error as error ;
use \payapi\cgi as cgi ;
use \payapi\debugger as debugger ;
use \payapi\framework as framework ;
use \payapi\crypter as crypter ;
use \payapi\loader as loader ;
use \payapi\data as data ;

final class payapi {

  public
    $info                             =              array (
      "crypter_v"      =>                 false ,
      "v"              =>               '0.0.0' ,
      "app"            => 'PayApi Payments SDK'
    ) ;

  private
    $debugger                      =                 false ,
    $loader                        =                 false ,
    $framework                       =                 false ,
    $crypter                       =                 false ,
    $cgi                           =                 false ,
    $command                       =                 false ,
    $arguments                     =                 false ,
    $config                        =              array () ,
    $data                          =                 false ,
    $model                         =                 false ,
    $controller                    =                 false ,
    $error                         =                 false ,
    $autoloaded                    =                 false ,
    $configs                       =                 false ,
    $commands                      =                 array (
                                                    "info" ,
                                                "settings" ,
                                             "transaction" ,
                                                "localize" ,
                                                "callback" ,
                                                "validate" ,
                                                   "error" ,
                                                   "brand"
                                                         ) ;

  public function __construct ( $config = array () ) {
    $configConstruct = ( is_array ( $config ) === true ) ? $config : array () ;
    //-todo
    foreach ( glob ( __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . '*' . '.' . 'php' ) as $core ) {
      require $core ;
    }
    $this -> data = data :: single () ;
    $this -> autoload ( $configConstruct ) ;
  }

  private function autoload ( $configConstruct ) {
    if ( $this -> configure ( $configConstruct ) === true && is_object ( $this -> crypter ) === true ) {
      $this -> data -> set ( 'crypter' , $this -> crypter ) ;
      $this -> data -> set ( 'config' , $this -> config ) ;
      //$this -> data -> set ( 'info' , array () ) ;
      $this -> cgi = new cgi ( $this -> config ) ;
      if ( is_object  ( $this -> cgi ) === true ) {
        $this -> loader = new loader () ;
        if ( is_object  ( $this -> loader ) === true ) {
          $this -> framework = new framework () ;
          $this -> setInfo () ;
          if ( is_object  ( $this -> framework ) === true ) {
            $this -> debug ( '[framework] loaded' ) ;
            $this -> data -> set ( 'loader' , $this -> loader ) ;
            $this -> data -> set ( 'framework' , $this -> framework ) ;
            $this -> autoloaded = true ;
          } else {
            $this -> error ( '[framework] failed' ) ;
          }
        } else {
          $this -> error ( '[loader] failed' ) ;
        }
      } else {
        $this -> error ( '[cgi] failed' ) ;
      }
    } else {
      $this -> error ( '[config] failed') ;
    }
  }

  private function autoloaded () {
    return $this -> autoloaded ;
  }

  private function configure ( $config ) {
    if ( is_array ( $config ) ) {
      //-> tofilter
      $validator = array (
        "mode"             => ( ( isset ( $config [ 'mode' ] ) === true && in_array ( $config [ 'mode' ] , array ( 'json' , 'html' , 'string' , 'array' , 'object' , 'dump' ) ) === true ) ? $config [ 'mode' ] : 'json' ) ,
        "branding"         => ( ( isset ( $config [ 'branding' ] ) === true && is_string ( $config [ 'branding' ] ) ) ? $config [ 'branding' ] : false ) ,
        "plugin"           => ( ( isset ( $config [ 'plugin' ] ) === true && is_string ( $config [ 'plugin' ] ) && in_array ( $config [ 'plugin' ] , array ( 'opencart' , 'magento' , 'prestashop' , 'default' ) ) === true ) ? $config [ 'plugin' ] : 'default' ) ,
        "headers"          => ( ( isset ( $config [ 'headers' ] ) === true && $config [ 'headers' ] !== false ) ? true : false ) ,
        "archival"         => ( ( isset ( $config [ 'archival' ] ) === true && $config [ 'archival' ] !== false ) ? true : false ) ,
        "production"       => ( ( isset ( $config [ 'production' ] ) === true && $config [ 'production' ] !== false ) ? true : false ) ,
        "debug"            => ( ( isset ( $config [ 'debug' ] ) === true && $config [ 'debug' ] !== false ) ? true : false ) ,
        "payapi_public_id" => ( ( isset ( $config [ 'payapi_public_id' ] ) === true && is_string ( $config [ 'payapi_public_id' ] ) !== false && preg_match ( '~^[0-9a-z]+$~i' , $config [ 'payapi_public_id' ] ) !== false ) ? $config [ 'payapi_public_id' ] : null )
      ) ;
      if ( $validator [ 'debug' ] === true ) {
        $this -> debugger = debugger :: single () ;
        $this -> data -> set ( 'debugger' , $this -> debugger ) ;
        $this -> debug ( 'Wake up ' . $this -> info [ 'app' ] . ' v' . $this -> info [ 'v' ] ) ;
      }
      $this -> data -> set ( 'debug' , $this -> debugger ) ;
      $this -> crypter = new crypter ( md5 ( $validator [ 'payapi_public_id' ] ) ) ;
      $this -> info [ 'crypter_v' ] = ( string ) $this -> crypter ;
      $configApp = array_merge ( $validator ,
        array (
          "encoded_payapi_api_key" => ( isset ( $config [ 'payapi_api_key' ] ) === true && is_string ( $config [ 'payapi_api_key' ] ) === true ) ? $this -> crypter -> encode ( $config [ 'payapi_api_key' ] , $validator [ 'payapi_public_id' ] , true ) : null
        )
      ) ;
      if ( is_array ( $configApp ) !== false ) {
        $this -> config = $configApp ;
        return true ;
      }
    }
    return false ;
  }

  private function setInfo () {
    foreach ( $this -> info as $key => $value ) {
      $this -> data -> addInfo ( $key , $value ) ;
    }
  }

  public function debug ( $info , $label = false ) {
    if ( is_object ( $this -> debugger ) === true ) {
      return $this -> debugger -> add ( $info , $label ) ;
    }
    return true ;
  }

  public function __call ( $command , $arguments = array () ) {
    if ( $this -> autoloaded () === true && $this -> framework -> validator -> isString ( $this -> config [ 'payapi_public_id' ] ) === true && $this -> framework -> validator -> isPayload ( $this -> config [ 'encoded_payapi_api_key' ] , true ) === true ) {
      if ( $this -> cgi -> sslEnabled () === true && $this -> cgi -> checkSslAccess () ) {
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
        return $this -> unsecure () ;
      }
    } else {
      if ( $this -> autoloaded () === true ) {
        return $this -> unauthorized () ;
      }
      return $this -> booBoo () ;
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

  private function error ( $error = false ) {
    if ( $error === false ) {
      return $this -> error ;
    }
    if ( is_string ( $error ) ) {
      $this -> debug ( $error , 'error' ) ;
      $this -> error [] = $error ;
      return true ;
    }
    $this -> error [] = 'undefined' ;

  }

  private function booBoo () {
    header ( 'Content-type: ' . $this -> modes [ $this -> mode ] ) ;
    http_response_code ( 503 ) ;
    return die ( json_encode ( array (
      "code" => 503 ,
      "data" => 'service unavailable'
    ) ) ) ;
  }

  protected function unauthorized () {
    $this -> debug ( 'no valid payapi account' ) ;
    return $this -> cgi -> render ( $this -> cgi -> response ( 403 ) , 403 ) ;
  }

  protected function unsecure () {
    $this -> debug ( 'no valid SSL certificate' ) ;
    return $this -> cgi -> render ( $this -> cgi -> response ( 505 ) , 505 ) ;
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
    $this -> debug ( '[destructed] success' ) ;
  }


}
