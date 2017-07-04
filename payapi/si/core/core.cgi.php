<?php

namespace payapi ;
//->
final class cgi extends helper {

  protected
    $version                       =   '0.0.0' ,
    $timeout                       =        30 ,
    $id                            =     false ,
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
    $filterer                        =   false ,
    $sanitizer                       =   false ,
    $mode                            =   false ,
    $code                            =     600 ,
    $ssl                             =   false ,
    $sslChecked                      =   false ,
    $headers                         =   false ,
    $buffer                          =    null ,
    $serverLoad                      =   false ,
    $production                      =    true ,
    $ip                              =   false ,
    $domain                          =   false ,
    $modes                             = array (
      "json"             => 'application/json' ,
      "html"             => 'text/html' ,
      "string"           => 'text/plain' ,
      "dump"             => '' ,
      "array"            => '' ,
      "object"           => ''
    );

  protected function auto () {
    $this -> addInfo ( 'cgi_v' , $this -> version ) ;
    $this -> addInfo ( 'ip' , ( string ) $this -> ip () ) ;
    $this -> filterer = new filterer () ;
    $this -> addInfo ( 'filter_v' , ( string ) $this -> filterer ) ;
    $this -> sanitizer = new sanitizer () ;
    $this -> addInfo ( 'sanitizer_v' , ( string ) $this -> sanitizer ) ;
    $this -> domain = $this -> getServerDomain () ;
    $this -> ip = $this -> getIp () ;
    $this -> ssl = $this -> checkSsl () ;
    if ( $this -> ssl !== true ) {
      $this -> error ( '[SSL] no valid' ) ;
    } else {
      $this -> debug ( '[SSL] enabled' ) ;
    }
    //->
    $this -> set ( 'responses' , $this -> responses ) ;
    if ( is_string ( $this -> config ( 'mode' ) ) === true ) {
      $this -> mode ( $this -> config ( 'mode' ) ) ;
    }
    if ( $this -> config ( 'headers' ) === true ) {
      $this -> headers = true ;
    }
    if ( $this -> config ( 'production' ) !== true ) {
      $this -> production = false ;
    }
    if ( function_exists ( 'sys_getloadavg' ) ) {
      $this -> serverLoad = sys_getloadavg () ;
      $this -> debug ( '[server_load] ' . ( $this -> serverLoad [ 0 ] * 100 ) . '%' ) ;
      if ( $this -> serverLoad [ 0 ] > 0.96 ) {
        $this -> render ( $this -> response ( 503 ) , 503 ) ;
      }
    } else {
      $this -> warning ( 'cannot check load' , 'server' ) ;
    }
    /*
    $test = $this -> checkSsl ( 'payapi.io' ) ;
    var_dump ( $test ) ;
    exit () ;
    */
  }

  public function sslEnabled () {
    return $this -> ssl ;
  }

  private function stream ( $foo ) {
    $blocked = false ;
    $stream = null ;
    while ( ( $line = fread ( $foo , 64 ) ) && $blocked === false ) {
      if ( isset ( $stream ) === null && $blocked === false && md5 ( substr ( $line , 0, 9 ) ) != md5 ( '{"data":"' ) ) {
        $this -> warning ( 'stream blocked' , 'filter' ) ;
        $blocked = true ;
      }
      $stream .= $line ;
    }
    fclose ( $foo ) ;
    return $stream ;
  }

  public function knock () {
    $server = json_encode ( $_SERVER , true ) ;
    $this -> debug ( 'server : ' . $server ) ;
    if ( getenv ( 'REQUEST_METHOD' ) == 'POST' ) {
      $this -> debug ( 'access from : ' . 'TODO' ) ;
      if ( $this -> sslEnabled () !== false ) { // TODO check incomming domain $this -> checkIncomingHasValidSsl
        $this -> debug ( '[ACK] success' ) ;
        $timeStart = microtime ( true ) ;
        // @NOTE @CARE "php://input" is not available with enctype="multipart/form-data"
        $jsonExpected = $this -> stream ( fopen ( "php://input" , "r" ) ) ;
        $this -> debug ( 'timing ' . ( round ( ( microtime ( true ) - $timeStart ) , 3 ) * 1000 ) . 'ms.' ) ;
        if ( is_bool ( $jsonExpected ) === false && is_string ( $jsonExpected ) === true && strlen ( $jsonExpected ) > 12 ) {
          $dataExpected = json_decode ( $jsonExpected , true ) ;
          if ( isset ( $dataExpected [ 'data' ] ) && is_object ( $dataExpected [ 'data' ] ) === false && is_string ( $dataExpected [ 'data' ] ) !== false && substr_count ( $dataExpected [ 'data' ] , '.' ) == 2 ) { // && is_string ( $array [ 'data' ] ) === true && substr_count ( $dataExpected [ 'data' ] , '.' ) == 2
            $this -> debug ( '[ACK] success' ) ;
            return $dataExpected [ 'data' ] ;
          } else {
            $this -> warning ( 'unexpected ' , 'knock' ) ;
          }
        } else {
          $this -> warning ( 'empty ' , 'knock' ) ;
        }
      } else {
        $this -> warning ( 'no valid ' , 'SSL' ) ;
      }
    } else {
      $this -> debug ( 'method not allowed' ) ;
    }
    unset ( $jsonExpected ) ;
    return false ;
  }

  public function curl ( $url , $post = false , $return = 1 , $header = 0 , $ssl = 1 , $fresh = 1 , $noreuse = 1 , $timeout = 15 ) {
    $this -> debug ( '[' . $this -> method ( $post ) . '] ' . $this -> filterer -> getHostNameFromUrl ( $url ) ) ;
    $options = array (
      CURLOPT_URL              => $url ,
      CURLOPT_RETURNTRANSFER   => $return ,
      CURLOPT_HEADER           => $header ,
      CURLOPT_SSL_VERIFYPEER   => $ssl ,
      CURLOPT_FRESH_CONNECT    => $fresh ,
      CURLOPT_FORBID_REUSE     => $noreuse ,
      CURLOPT_TIMEOUT          => $timeout ,
      CURLOPT_HTTPHEADER       => array (
        'User-Agent: ' . __NAMESPACE__ . ' - curl v' . $this -> version
    ) ) ;
    $buffer = curl_init () ;
    curl_setopt_array ( $buffer , $options ) ;
    if ( $post !== false ) {
      $curlPost = http_build_query ( array ( "data" => $post ) ) ;
      //->
      curl_setopt ( $buffer , CURLOPT_POSTFIELDS , $curlPost ) ;
    }
    $timeStart = microtime ( true ) ;
    $jsonExpected = curl_exec ( $buffer ) ;
    $dataExpected = json_decode ( $jsonExpected , true ) ;
    $code = ( int ) curl_getinfo ( $buffer , CURLINFO_HTTP_CODE ) ;
    $this -> debug ( 'timing ' . ( round ( ( microtime ( true ) - $timeStart ) , 3 ) * 1000 ) . 'ms.' ) ;
    $response = false ;
    if ( $this -> isCleanCodeInt ( $code ) === true ) {
      if ( $code === 200 ) {
        if ( 1 === 1 ) {
        //-> fixme
        //if ( $this -> filterer -> filtererArray ( $dataExpected ) === true ) {
          $response = $dataExpected ;
        } else {
          $this -> warning ( 'object blocked' , 'curl' ) ;
        }
      } else {
        $this -> warning ( 'unexpected response' , 'curl' ) ;
      }
    }
    curl_close ( $buffer ) ;
    return $response ;
  }

  public function curlErrorUnexpectedCurlResponse () {
    return $this -> unvalidCurlResponse ( $this -> error -> errorUnexpectedCurlResponse () , 'curl schema error' ) ;
  }

  public function unvalidCurlResponse ( $responseCode = false , $responseInfo = false ) {
    $responseCode = ( is_numeric ( $responseCode ) === true && isset ( $this -> responses [ $responseCode ] ) === true ) ? $responseCode : $this -> error -> errorUnexpectedCurlResponse () ;
    $responseData = ( is_string ( $responseInfo ) === true ) ? $responseInfo : $this -> responses [ $responseCode ] ;
    $response = array (
      "code"  => $responseCode ,
      "data"  => ( string ) $responseData
    ) ;
    return $response ;
  }

  private function method ( $post ) {
    return ( ( $post ===  false ) ? 'GET' : 'POST' ) ;
  }

  public function render ( $bufferData , $code = false , $mode = false , $headers = 'undefined' ) {
    if ( is_string ( $mode ) === true ) {
      $this -> mode ( $mode ) ;
    }
    if ( is_int ( $code ) === true ) {
      $this -> code ( $code ) ;
    }
    $this -> debug ( '[buffer] sanitizer' ) ;
    $data = $this -> data -> sanitizePrivate ( $bufferData ) ;
    $this -> debug ( '[rendering] info' ) ;
    $this -> debug ( '[mode] ' . $this -> mode ) ;
    $this -> debug ( '[code] ' . $this -> code ) ;
    if ( is_bool ( $headers ) === true && $headers != 'undefined' && $headers !== false ) $this -> headers = true ;
    switch ( $this -> mode ) {
      case 'json' :
        $this -> buffer = ( is_array ( $data ) !== false ) ? json_encode ( $data , true ) : $data ;
      break ;
      case 'html' :
        if ( is_array ( $data ) )
        $this -> buffer = ( is_array ( $data ) !== false ) ? json_encode ( $data , true ) : $data ;
      break ;
      case 'string' :
        $this -> buffer = ( is_array ( $this -> buffer ) !== false ) ? print ( $data ) : print_r ( $data , true ) ;
        $this -> dumpVar () ;
        return $this -> buffer ;
      break ;
      case 'object' :
        $this -> buffer = ( is_object ( $data ) !== false ) ? $data  : ( object ) $data ;
        return $this -> buffer ;
      break ;
      case 'array' :
        $this -> buffer = ( is_array ( $data ) !== false ) ? $data : ( array ) $data ;
        return $this -> buffer ;
      break ;
      case 'dump' :
        return $data ;
      break ;
      default :
        $this -> buffer = $data ;
        return $this -> buffer ;
      break ;
    }
    $this -> headers () ;
    //if ( $var OR $display )
    return $this -> display () ;
  }

  private function headers () {
    if ( $this -> headers == false ) {
      return true ;
    }
    $this -> debug ( '[headers] ' . $this -> mode ) ;
    //@header( "X-Robots-Tag: noindex,nofollow" ) ;
    header ( 'Content-type: ' . $this -> modes [ $this -> mode ] ) ;
    return http_response_code ( $this -> code ) ;
  }

  public function serverLoad () {
    return $this -> serverLoad ;
  }

  private function display () {
    $this -> debug ( '[display] success ' ) ;
    return die ( $this -> buffer ) ;
  }

  private function dumpVar () {
    $this -> debug ( '[return] success ' ) ;
  }

  private function getServerDomain () {
    return $this -> sanitizer -> parseDomain ( getenv ( 'SERVER_NAME' ) ) ;
  }

  private function checkSsl ( $checkDomain = false ) {
    return true ; //-> @NOTE @CARE @TODELETE just fro DEV
    $domain = ( is_string ( $checkDomain ) === true ) ? $checkDomain : $this -> domain ;
    $socket = stream_context_create ( array ( "ssl" => array ( "capture_peer_cert" => true ) ) ) ;
    if ( $this -> sslChecked === false && $socket === false ) {
      return $this -> reCheckSsl ( $checkDomain ) ;
    }
    $conexion = @stream_socket_client ( "ssl://{$domain}:443" , $errno , $errstr , $this -> timeout , STREAM_CLIENT_CONNECT , $socket ) ;
    $certificate = @stream_context_get_params ( $conexion ) ;
    //->
    if ( $this -> sslChecked === 0 && isset ( $conexion ) !== true || isset ( $certificate ) !== true || isset ( $certificate [ "options" ] [ "ssl" ] [ "peer_certificate" ] ) !== true ) {
      return $this -> reCheckSsl ( $checkDomain ) ;
    }
    return $certificate [ "options" ] [ "ssl" ] [ "peer_certificate" ] ;
  }

  public function checkIncomingHasValidSsl ( $url ) {
    return $this -> checkSsl ( $url ) ;
  }

  public function checkSslAccess () {
    $this -> debug ( '[HTTPS] ' . json_encode ( getenv ( 'HTTPS' ) , true ) ) ;
    if ( getenv ( 'HTTPS' ) !== false ) {
      return true ;
    }
    $this -> error ( '[HTTP] insecure access' ) ;
    return false ;
  }

  public function reCheckSsl () {
    if ( $this -> sslChecked === true ) {
      $this -> sslChecked = true ;
      usleep ( 100000 ) ; // @NOTE adds microseconds to avoid same timeout
      return $this -> checkSsl () ;
    }
    return false ;
  }

  public function ip () {  // access ip
    return $this -> ip ;
  }

  private function getIp () {
    if ( ! $access = $this -> validateIp ( getenv ( 'HTTP_CLIENT_IP' ) ) )
      if ( ! $access = $this -> validateIp ( getenv ( 'HTTP_X_FORWARDED_FOR' ) ) )
        if ( ! $access = $this -> validateIp ( getenv ( 'HTTP_X_FORWARDED' ) ) )
          if ( ! $access = $this -> validateIp ( getenv ( 'HTTP_FORWARDED_FOR' ) ) )
            if ( ! $access = $this -> validateIp ( getenv ( 'HTTP_FORWARDED' ) ) )
              if ( ! $access = $this -> validateIp ( getenv ( 'REMOTE_ADDR' ) ) )
                $access = $this -> undefinedIp () ;
    //-> @NOTE @TODELETE just for developing
    $access = ( $access == '192.168.10.1' ) ? '88.27.211.244' : $access ;
    $ip = htmlspecialchars ( $access , ENT_COMPAT , 'UTF-8' ) ;
    return $ip ;
  }

  public function validateIp ( $env ) {
    return filter_var ( $env , FILTER_VALIDATE_IP ) ;
  }

  public function response ( $code = false ) {
    return $this -> gatvalidatorResponse ( $code ) ;
  }

  private function undefinedIp () {
    return 'undefined' ;
  }

  private function gatvalidatorResponse ( $responseCode ) {
    if ( $this -> isCleanCodeInt ( $responseCode ) === true && isset ( $this -> responses [ $responseCode ] ) ) {
      $code = $responseCode ;
    } else {
      $this -> warning ( 'response code no valid' , 'cgi' ) ;
      $code = $this -> defaultCode () ;
    }
    return array (
      "code" => $code ,
      "data" => $this -> responses [ $code ]
    ) ;
  }

  private function isCleanArray ( $data ) {
    if ( is_array ( $data ) === true && $this -> noObjectsAndFloats ( $data ) === true ) {
      return true ;
    }
    return false ;
  }

  private function defaultCode () {
    end ( $this -> responses ) ;
    return key ( $this -> responses ) ;
  }

  public function mode ( $mode ) {
    if ( is_string ( $mode ) === true && isset ( $this -> modes [ $mode ] ) === true ) {
      $this -> mode = $mode ;
      return true ;
    }
    reset ( $this -> modes ) ;
    $this -> mode = key ( $this -> modes ) ;
    return false ;
  }

  public function code ( $code ) {
    if ( $this -> isCleanCodeInt ( $code ) === true && isset ( $this -> responses [ $code ] ) === true ) {
      $this -> code = $code ;
      return true ;
    }
    end ( $this -> responses ) ;
    $this -> code = key ( $this -> responses ) ;
    return false ;
  }

  private function isCleanCodeInt ( $int ) {
    if ( is_int ( $int ) === true && $int >= 200 && $int <= 600 ) {
      return true ;
    }
    return false ;
  }


}
