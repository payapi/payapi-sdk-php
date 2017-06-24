<?php

// @NOTE not used -> moved to cgi

final class response {

  protected
    $code                 =  false ,
    $data                 =  false ,
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
    $default              =    600 ;

  public function construct ( $code = false , $data = false ) {
    $this -> code  = ( in_array ( $code , $this -> responses ) ) ? $code : $this -> default ;
    $this -> data = $data ;

    return ( string ) $this ;
  }

  public function __toString () {
    return json_encode ( array (
      "code" => $this -> code ,
      "data" => $this -> data
    ) , true ) ;
  }


}
