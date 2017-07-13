<?php
/**
*
*  Just for testing
*
**/

ini_set ( 'display_errors' , 1 ) ;
ini_set ( 'display_startup_errors' , 1 ) ;
error_reporting ( E_ALL ) ;

require ( "/opt/php-jwt/vendor/autoload.php" ) ;
use \Firebase\JWT\JWT;


//->     var_dump ( '__info' , $this -> payapi -> localize ( true , '84' . '.' . rand ( 150 , 200 ) . '.' . rand ( 10 , 250 ) . '.' . rand ( 10 , 250 ) ) ) ;
//->     exit () ;
/*
var_dump ( $this -> payapi -> response ( 200 ) ) ;
var_dump ( $this -> payapi -> info ( true ) ) ;
var_dump ( $this -> payapi -> localize () ) ;
var_dump ( '__info' , $this -> payapi -> localize ( true , '84.169.115.' . rand ( 101 , 240 ) ) ) ;
var_dump ( $this -> payapi -> settings () ) ;
exit () ;/*
for ( $cont = 0 ; $cont < 2 ; $cont ++ ) {
  var_dump ( '__info' , $this -> payapi -> localize ( true , '85.169.' . rand ( 141 , 240 ) . '.' . rand ( 141 , 240 ) ) ) ;
} exit () ;
//*/



class testing {

  public
    $retry                     =       20 ,
    $version                   =  '0.0.0' ;

  private
    $referenceId               =    false ,
    $mode                      =  'HS256' ,
    $payapi_public_id          =  'multimerchantshop' ,
    $payapi_api_key            =  'qETkgXpgkhNKYeFKfxxqKhgdahcxEFc9' ;


  public function test ( $referenceId = false ) {
    $this -> referenceId = date ( 'Ymd' , time () ) . '-' . ( ( is_int ( $referenceId ) === true ) ? $referenceId : rand ( 100000000 , 1000000000 ) ) ;
    return $this -> run () ;
  }

  public function run () {
    $endPoint = 'https://store.multimerchantshop.dev/index.php' ;
    $payload = json_encode ( $this -> payload () , true ) ;
    $signed = $this -> encode ( $payload , $this -> payapi_api_key ) ;
    $request = '{"data":"' . $signed . '"}' ;
    return $this -> request ( $endPoint , $request ) ;
  }

  protected function payload () {
    $testCallback = '{
      "payment": {
        "status": "processing"
      },
      "order": {
        "sumInCentsIncVat": 322,
        "sumInCentsExcVat": 300,
        "vatInCents": 22,
        "currency": "EUR",
        "referenceId": "' . $this -> referenceId . '"
      },
      "products": [
        {
          "id": "bbc123456",
          "quantity": 1,
          "title": "Black bling cap",
          "description": "Flashy fine cap",
          "imageUrl": "https://example.com/black_bling_cap.png",
          "category": "Caps and hats",
          "options": ["size=1"],
          "model": "BLINGCAP123",
          "priceInCentsIncVat": 122,
          "priceInCentsExcVat": 100,
          "vatInCents": 22,
          "vatPercentage": 22,
          "extraData": "manufacturer=Bling Bling&origin=China"
        },
        {
          "id": "pbc123456",
          "quantity": 1,
          "title": "Pink bling cap",
          "description": "Flashy fine cap",
          "imageUrl": "https://example.com/pink_bling_cap.png",
          "category": "Caps and hats",
          "options": ["size=2"],
          "model": "BLINGCAP123",
          "priceInCentsIncVat": 222,
          "priceInCentsExcVat": 200,
          "vatInCents": 22,
          "vatPercentage": 22,
          "extraData": "manufacturer=Bling Bling&origin=China"
        }
      ],
      "shippingAddress": {
        "recipientName": "John Smith",
        "co": "Jane Doe",
        "streetAddress": "Delivery street 123",
        "streetAddress2": "Apt. 1202",
        "postalCode": "90210",
        "city": "New York",
        "stateOrProvince": "NY",
        "countryCode": "US"
      },
      "consumer": {
        "consumerId": "happyjohn",
        "email": "happyconsumer@example.com",
        "locale": "en-US",
        "mobilePhoneNumber": "34123456789"
      },
      "extraInputData": {
        "message": "message to merchant",
        "tableNumber": 12
      }
    }' ;
    $payload = json_decode ( $testCallback , true ) ;
    return $payload ;
  }

  public function request ( $url , $post = false , $return = 1 , $header = 0 , $ssl = 0 , $fresh = 1 , $noreuse = 1 , $timeout = 15 ) {
    $timeStart = microtime ( true ) ;
    $this -> debug ( $this -> getHostNameFromUrl ( $url ) ) ;
    $this -> reset () ;
    $options = array
      (
        CURLOPT_URL              => $url ,
        CURLOPT_RETURNTRANSFER   => $return ,
        CURLOPT_HEADER           => $header ,
        CURLOPT_SSL_VERIFYPEER   => $ssl ,
        CURLOPT_FRESH_CONNECT    => $fresh ,
        CURLOPT_FORBID_REUSE     => $noreuse ,
        CURLOPT_TIMEOUT          => $timeout ,
        CURLOPT_HTTPHEADER       => array (
          'User-Agent: PA test - curl v' . $this -> version
        )
      )
    ;
    $buffer = curl_init () ;
    curl_setopt_array ( $buffer , $options ) ;
    if ( $post != false ) {
      $curlPost = $post ;
      curl_setopt ( $buffer , CURLOPT_POSTFIELDS , $curlPost ) ;
    }
    //var_dump ( '___test' , curl_exec ( $buffer ) ) ; exit () ;
    //$responsed = json_decode ( curl_exec ( $buffer ) , true ) ;
    $responsed = curl_exec ( $buffer ) ;
    $code = ( int ) addslashes ( curl_getinfo ( $buffer , CURLINFO_HTTP_CODE ) ) ;
    $testResponse = array (
      "code" => $code ,
      "response" => $responsed
    ) ;
    curl_close ( $buffer ) ;
    //var_dump ( curl_exec ( $buffer ) ) ;
    $testJsonResponse = json_encode ( $testResponse , true ) ;
    return $testResponse ;
  }

  public function getHostNameFromUrl ( $url ) {
    $parse = parse_url ( $url ) ;
    if ( isset ( $parse [ 'host' ] ) === true ) {
      return $parse [ 'host' ] ;
    }
    return false ;
  }

  public function curlErrorUnexpectedCurlResponse () {
    return array (
      "code" => 404 ,
      "data" => 'curl schema error'
    ) ;
  }

  private function isCleanCodeInt ( $int ) {
    if ( is_int ( $int ) === true && $int >= 200 && $int <= 600 ) {
      return true ;
    }
    return false ;
  }

  public function unvalidCurlResponse ( $responseCode = false ) {
    $responseCode = ( is_int ( $responseCode ) === true ) ? $responseCode : 'error' ;
    $response = array (
      "code"  => 404 ,
      "data"  => "error." . ( string ) $responseCode
    ) ;
    return $response ;
  }

  public function response () {
    return $response ;
  }

  protected function reset () {
    $response = false ;
    $buffer = false ;
  }

  private function isCleanArray ( $data ) {
    if ( is_array ( $data ) === true && $this -> noObjectsAndFloats ( $data ) === true ) {
      return true ;
    }
    return false ;
  }
  //-> duplicated in filterer
  private function noObjectsAndFloats ( $unfiltererArray ) {
    foreach ( $unfiltererArray as $filtering ) {
      if ( is_array ( $filtering ) === true ) {
        if ( $this -> noObjectsAndFloats ( $filtering ) !== true ) {
          return false ;
        }
      }
      if ( is_string ( $filtering ) === true || is_int ( $filtering ) === true || is_bool ( $filtering ) ) {
        return true ;
      }
    }
    return false ;
  }

  protected function error ( $error ) {
    $this -> error [] = 'error: ' . $error ;
  }

  protected function warning ( $error ) {
    $this -> error [] = 'warning: ' . $error ;
  }

  protected function debug ( $debug ) {
    return true ;
  }

  public function decode ( $encoded , $hash = false ) {
    $hash_update = ( is_string ( $hash ) === true ) ? $hash : $this -> payapi_api_key ;
    try {
      $decoded = JWT :: decode ( $encoded , $hash , array ( $this -> mode ) ) ;
    } catch ( \Exception $e ) {
      $this -> error ( 'cannot decode payload : ' . json_encode ( $e -> getMessage () ) ) ;
      $decoded = false ;
    }
    return $decoded ;
  }

  public function encode ( $decoded , $hash = false ) {
    $hash_update = ( is_string ( $hash ) === true ) ? $hash : $this -> payapi_api_key ;
    try {
      $encoded = JWT :: encode ( $decoded , $hash , $this -> mode ) ;
    } catch ( \Exception $e ) {
      $this -> error ( 'cannot encode payload' ) ;
      $encoded = false ;
    }
    return $encoded ;
  }


}

function test () {//->
  exit () ;
  $testing = new testing () ;
  for ( $cont = 0 ; $cont < 100 ; $cont ++ ) {
    var_dump ( '___test' , $testing -> test () ) ;//->
    exit () ;
  }
  unset ( $testing ) ;
}

test () ;
