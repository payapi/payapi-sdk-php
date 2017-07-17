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
*  @param      ( object ) adapt = ( main sdk config || adapt model for adaptor plugin )
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
*              $sdk -> instantPayment ( $data ) ;                     //-> validate/sanitize/cache product/payment data
*                                                                          returns array ( "metadata" , "endPointPayment" , "endPointProductPayment" ) ;
*              $sdk -> brand () ;                                     //-> gets brand/partner info
*              $sdk -> callback () ;                                  //-> gets/cache callback decoded transaction
*              $sdk -> localize () ;                                  //-> gets/cache ip decoded localization (plugin adds adapt country and zone ids)
*              $sdk -> localize ( true ) ;                            //-> gets & refresh ip decoded localization cache (plugin adds adapt country and zone ids)
*              $sdk -> settings ( 'public_id' , 'api_key' , true ) ;  //-> verify account & gets/cache instance merchantSettings, also refresh account data
*              $sdk -> settings ( false , false , true ) ;            //-> refresh & gets instance merchantSettings
*              $sdk -> settings () ;                                  //-> gets instance cached merchantSettings
*              $sdk -> partialPayment ( $totalInCents , $currency ) ; //-> calculate partialPayment from merchantSettings
*              $sdk -> response ( <standard_response_code_int> ) ;    //-> get response info
*              $sdk -> info () ;                                      //-> gets sdk debug info
*
*        ( * ) plugin is defined in /payapi/app.engine.php
*              - native
*              - opencart2
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
*              sdk default noplugin settings
*
*
**/

require ( "/opt/php-jwt/vendor/autoload.php" ) ;
use \Firebase\JWT\JWT;

use \payapi\engine as engine ;

class payapi {

  private
    $app                       =   false ;

  public function __construct ( $adapt ) {
    $this -> hack  () ;
    require_once ( 'app' . '.' . 'engine' . '.' . 'php' ) ;
    $this -> app = engine :: single ( $adapt ) ;
    return $this -> app ;
  }

  private function hack () {
    //-> cli hack
    if ( is_string ( getenv ( 'SERVER_NAME' ) ) !== true || getenv ( 'SERVER_NAME' ) === 'store.multimerchantshop.dev' ) {
      putenv ( 'SERVER_NAME=store.multimerchantshop.xyz' ) ;
    }
    if ( is_string ( getenv ( 'SERVER_NAME' ) ) !== true ) {
      putenv ( 'REMOTE_ADDR=84.79.234.58' ) ;
    }
  }

  public function __call ( $command , $arguments = array () ) {
    return $this -> app -> $command ( $arguments ) ;
  }


}

$settings = array (
  "debug"    => true ,
  "staging"  => true
) ;

$sdk = new payapi ( $settings ) ;

for ( $cont = 0 ; $cont < 100 ; $cont ++ ) {
  var_dump ( '__info' , $sdk -> localize ( true , '79.159.' . rand ( 141 , 240 ) . '.' . rand ( 141 , 240 ) ) ) ;
} exit () ;

exit () ;
