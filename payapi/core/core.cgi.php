<?php

namespace payapi ;

final class cgi extends engine {

  protected
    $responses =    array (
      // @NOTE PHP ZEND INTERNAL STATUS HEADERS

      // Informational 1xx
      100 =>                        'continue' ,
      101 =>             'switching Protocols' ,

      // Success 2xx
      200 =>                         'success' ,
      201 =>                         'created' ,
      202 =>                        'accepted' ,
      203 =>   'non-Authoritative Information' ,
      204 =>                      'no Content' ,
      205 =>                   'reset Content' ,
      206 =>                 'partial Content' ,

      // Redirection 3xx
      300 =>                'multiple choices' ,
      301 =>               'moved permanently' ,
      302 =>                           'found' ,  // 1.1
      303 =>                       'see Other' ,
      304 =>                    'not modified' ,
      305 =>                       'use proxy' ,
      // 306 is deprecated but reserved
      307 =>              'temporary redirect' ,

      // Client Error 4xx
      400 =>                     'bad request' ,
      401 =>                    'unauthorized' ,
      402 =>                'payment required' ,
      403 =>                       'forbidden' ,
      404 =>                       'not found' ,
      405 =>              'method not allowed' ,
      406 =>                  'not acceptable' ,
      407 =>   'proxy authentication required' ,
      408 =>                 'request timeout' ,
      409 =>                        'conflict' ,
      410 =>                            'gone' ,
      411 =>                 'length required' ,
      412 =>             'precondition failed' ,
      413 =>        'request entity too large' ,
      414 =>            'request-uri too long' ,
      415 =>          'unsupported media type' ,
      416 => 'requested range not satisfiable' ,
      417 =>              'expectation failed' ,

      // Server Error 5xx
      500 =>           'internal server error' ,
      501 =>                 'not implemented' ,
      502 =>                     'bad gateway' ,
      503 =>             'service unavailable' ,
      504 =>                 'gateway timeout' ,
      505 =>      'http version not supported' ,
      509 =>        'bandwidth limit exceeded' ,

      // @NOTE Extra One(s) 6xx  :)
      600 =>                         'boo boo'
    ) ;

  private
    $mode                  =   false ,
    $code                  =     600 ,
    $headers               =   false ,
    $buffer                =    null ,
    $serverLoad            =   false ,
    $modes                   = array (
      "json"    => 'application/json' ,
      "html"    => 'text/html' ,
      "string"  => 'text/plain' ,
      "array"   => '' ,
      "object"  => ''
    );

  public function auto () {
    if ( $this -> config ( 'mode' ) ) {
      $this -> mode ( $this -> config ( 'mode' ) ) ;
    }
    if ( $this -> config ( 'headers' ) ) {
      $this -> headers = true ;
    }
    if ( function_exists ( 'sys_getloadavg' ) ) {
      $this -> serverLoad = sys_getloadavg () ;
      $this -> debug ( 'server load : ' . ( $this -> serverLoad [ 0 ] * 100 ) . '%' ) ;
      if ( $this -> serverLoad [ 0 ] > 0.96 ) {
        $this -> render ( $this -> response ( 503 ) , 503 ) ;
      }
    } else {
      $this -> warning ( 'cannot check server load' ) ;
    }
  }

  public function render ( $data , $code = false , $mode = false , $headers = 'undefined' ) {
    if ( $mode ) $this -> mode ( $mode ) ;
    if ( $code ) $this -> code ( $code ) ;
    $this -> debug ( 'rendering response ' ) ;
    $this -> debug ( '[mode] ' . $this -> mode ) ;
    $this -> debug ( '[code] ' . $this -> code ) ;
    if ( is_bool ( $headers ) && $headers != 'undefined' && $headers !== false ) $this -> headers = true ;
    switch ( $this -> mode ) {
      case 'json' :
        $this -> buffer = ( is_array ( $data ) ) ? json_encode ( $data , true ) : $data ;
      break ;
      case 'html' :
        if ( is_array ( $data ) )
        $this -> buffer = ( is_array ( $data ) ) ? json_encode ( $data , true ) : $data ;
      break ;
      case 'string' :
        $this -> buffer = ( ! is_array ( $this -> buffer ) ) ? var_dump ( $data ) : $data ;
      break ;
      case 'object' :
        $this -> buffer = ( ! is_object ( $data ) ) ? ( object ) $data  : $data ;
        return $this -> buffer ;
      break ;
      case 'array' :
        $this -> buffer = ( ! is_array ( $data ) ) ? ( array ) $data : $data ;
        return $this -> buffer ;
      break ;
      default :
        $this -> buffer = $data ;
        return $this -> buffer ;
      break ;
    }
    $this -> headers () ;
    return $this -> display () ;
  }

  private function headers () {
    if ( ! $this -> headers ) {
      return true ;
    }
    $this -> debug ( 'sending headers' ) ;
    header ( 'Content-type: ' . $this -> modes [ $this -> mode ] ) ;
    return http_response_code ( $this -> code ) ;
  }

  public function serverLoad () {
    return $this -> serverLoad ;
  }

  private function display () {
    return die ( $this -> buffer ) ;
  }

  public function response ( $code = false ) {
    end ( $this -> responses ) ;
    $validated = ( is_int ( $code ) && isset ( $this -> responses [ $code ] ) ) ? $code : key ( $this -> responses ) ;
    return array (
      "code" => $validated ,
      "data" => $this -> responses [ $validated ]
    ) ;
  }

  public function mode ( $mode ) {
    if ( is_string ( $mode ) && isset ( $this -> modes [ $mode ] ) ) {
      $this -> mode = $mode ;
      return true ;
    }
    reset ( $this -> modes ) ;
    $this -> mode = key ( $this -> modes ) ;
    return false ;
  }

  public function code ( $code ) {
    if ( is_int ( $code ) && isset ( $this -> responses [ $code ] ) ) {
      $this -> code = $code ;
      return true ;
    }
    end ( $this -> responses ) ;
    $this -> code = current ( $this -> responses ) ;
    return false ;
  }


}
