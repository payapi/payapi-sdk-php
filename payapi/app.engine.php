<?php

namespace payapi ;

final class engine {

  public static
    $single                  =    false ;

  private
    $version                   =     '0.0.1' ,
    $plugin                    =    'native' ,
    //$plugin                    = 'opencart2' ,
    $adapt                     =       false ,
    $debug                     =       false ,
    $config                    =       false ,
    $entity                    =       false ,
    $router                    =       false ,
    $validate                  =       false ,
    $load                      =       false ,
    $api                       =       false ,
    $command                   =       false ,
    $arguments                 =       false ,
    $public                    =      array (
      "info"                   =>       true ,
      "localize"               =>       true ,
      "settings"               =>       true
    ) ;

  private function __construct ( $adapt ) {
    $this -> adapt = $adapt ;
    $this -> load () ;
    //->
  }

  private function load () {
    foreach ( glob ( __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . '*' . '.' . 'php' ) as $core ) {
      require_once $core ;
    }
    $this -> entity = entity :: single () ;
    $this -> adaptor = adaptor :: single ( $this -> adapt , $this -> plugin ) ;
    $this -> config = config :: single () ;
    $this -> debug = debug :: single () ;
    $this -> error = error :: single () ;
    $this -> entity -> set ( '___info' , ( string ) $this ) ;
    $this -> debug -> add ( $this -> entity -> get ( '___info' ) ) ;
    $this -> entity -> addInfo ( 'sdk_payapi_v' , $this -> version ) ;
    $this -> validate = new validator () ;
    $this -> load = new loader () ;
    $this -> api = new api () ;
    $this -> debug -> load () ;
    $this -> debug -> blank ( '=== LISTENING ==>' ) ;
  }

  public function __call ( $command , $arguments = array () ) {
    return $this -> worker ( $command , $arguments ) ;
  }

  private function worker ( $command , $arguments ) {
    $this -> entity -> set ( 'adaptor' , $this -> adaptor ) ;
    $this -> entity -> set ( 'validate' , $this -> validate ) ;
    $this -> entity -> set ( 'load' , $this -> load ) ;
    if ( $this -> load -> command ( $command ) === true ) {
      //-> filter/validate
      $this -> command = $command ;
      $this -> arguments = $arguments ;
      $this -> debug -> add ( '()' , 'run' ) ;
      return $this -> run () ;
    }
    return $this -> api -> returnResponse ( $this -> error -> notValidMethod () ) ;
  }

  private function run () {
    $this -> entity -> set ( 'command' , $this -> command ) ;
    $this -> entity -> set ( 'arguments' , $this -> arguments ) ;
    $this -> entity -> set ( 'api' , $this -> api ) ;
    $controller = '\\payapi\\' . 'command' . ucfirst ( $this -> command ) ;
    $command = new $controller ( $this -> adapt ) ;
    if ( method_exists ( $command , 'run' ) === true ) {
      if ( $this -> validate -> publicId ( $command -> publicId () ) === true || in_array ( $this -> command , $this -> public ) === true ) {
        $this -> debug -> run ( true ) ;
        return $command -> run () ;
      } else {
        return $this -> api -> returnResponse ( $this -> error -> forbidden () ) ;
      }
    }
  }

  public function __toString () {
    return 'PayApi SDK v' . $this -> version ;
  }

  public static function single ( $adapt = false ) {
    if ( self :: $single === false ) {
      self :: $single = new self ( $adapt ) ;
    }
    return self :: $single ;
  }


}
