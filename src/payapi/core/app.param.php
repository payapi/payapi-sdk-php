<?php

namespace payapi;

final class param
{

    private   
        static $responses = array(
                  // @NOTE PHP ZEND INTERNAL STATUS HEADERS

                  // Informational 1xx
                  100 =>                        'continue',
                  101 =>             'switching Protocols',

                  // Success 2xx
                  200 =>                         'success',
                  201 =>                         'created',
                  202 =>                        'accepted',
                  203 =>   'non-Authoritative Information',
                  204 =>                      'no Content',
                  205 =>                   'reset Content',
                  206 =>                 'partial Content',

                  // Redirection 3xx
                  300 =>                'multiple choices',
                  301 =>               'moved permanently',
                  302 =>                           'found',  // 1.1
                  303 =>                       'see Other',
                  304 =>                    'not modified',
                  305 =>                       'use proxy',
                  // 306 is deprecated but reserved
                  307 =>              'temporary redirect',

                  // Client Error 4xx
                  400 =>                     'bad request',
                  401 =>                    'unauthorized',
                  402 =>                'payment required',
                  403 =>                       'forbidden',
                  404 =>                       'not found',
                  405 =>              'method not allowed',
                  406 =>                  'not acceptable',
                  407 =>   'proxy authentication required',
                  408 =>                 'request timeout',
                  409 =>                        'conflict',
                  410 =>                            'gone',
                  411 =>                 'length required',
                  412 =>             'precondition failed',
                  413 =>        'request entity too large',
                  414 =>            'request-uri too long',
                  415 =>          'unsupported media type',
                  416 => 'requested range not satisfiable',
                  417 =>              'expectation failed',

                  // Server Error 5xx
                  500 =>           'internal server error',
                  501 =>                 'not implemented',
                  502 =>                     'bad gateway',
                  503 =>             'service unavailable',
                  504 =>                 'gateway timeout',
                  505 =>      'http version not supported',
                  509 =>        'bandwidth limit exceeded',

                  // @NOTE Extra One(s) 6xx  :)
                  600 =>                         'boo boo'
              );

    private
       static $modes = array(
                 'json'         => 'application/json',
                 'html'         => 'text/html',
                 'sdk'          => false
              );      

    private 
       static $caches = array(
                  //             expiration days
                  'localize'    => 30,
                  'ssl'         => 1,
                  'product'     => 1,
                  'payment'     => 1,
                  'transaction' => false,
                  'update'      => false,
                  'reseller'    => false,
                  'instance'    => false,
                  'account'     => false,
                  'settings'    => false
              );

    private 
       static $labels = array(
                  'info'   ,
                  'time'   ,
                  'api'    ,
                  'cron'   ,
                  'test'   ,
                  'run'    ,
                  'debug'  ,
                  'error'  ,
                  'warning',
                  'fatal'
              );
    //-> @TOTEST
    private 
       static $cdn = array(
                  'domain'      => 'cdn.payapi.io',
                  'secure'      => true,
                  'secret'      => 'ec6sG{sGLTmQ&mZoJQsPv^kCUBWTqk&E'
              );
    //-> @TODO move to schema
  	private 
  	   static $schema = array(
        				  'createStore' => array(
          						'publicId'             => array('string', true),
          						'email'                => array('email',  true),
          						'apiKey'               => array('string', true),
          						'subDomain'            => array('string', true),
          						'locale'               => array('string', true),
          						'currency'             => array('string', true),
          						'companyName'          => array('string', true),
          						'phoneNumber'          => array('string', true),
          						'countryCode'          => array('string', true),
          						'streetAddress'        => array('string', true),
          						'stateOrProvince'      => array('string', true),
          						'postalCode'           => array('string', true),
          						'city'                 => array('string', true),
          						'companyLogoUrl'       => array('url',    true),
          						'endPoint'             => array('url',    true),
          						'cloneDomain'          => array('domain', true),
          						'reseller'             => array('array',  true, 'reseller')
        				  ),
        				  'reseller'    => array(
          						'partnerId'            => array('string', true),
          						'partnerName'          => array('string', true),
          						'partnerSlogan'        => array('string', true),
          						'partnerLogoUrl'       => array('string', true),
          						'partnerIconUrl'       => array('string', true),
          						'partnerSupportInfoL1' => array('string', true),
          						'webshopBaseDomain'    => array('string', true),
          						'partnerWebUrl'        => array('string', true),
          						'partnerContactEmail'  => array('string', true),
          						'partnerContactPhone'  => array('string', true)
        				  )
        			);

  	private 
  	   static $action = array(
        				  'store'  => array(
        						  'create',
        						  'handle'
        				  )
        			);

    private
       static $instance = array(
                'domain'   => array('string',  true),
                'key'      => array('string',  true),
                'clone'    => array('string', false),
                'brand'    => array('string',  true)
              );

    private
       static $extension = 'data';

    public static function cdn()
    {
      return self::$cdn;
    }

    public static function caches()
    {
      return self::$caches;
    }

    public static function modes()
    {
      return self::$modes;
    }

  	public static function get($key, $value)
  	{
      	if (isset(self::$key[$value]) === true) {
      		return self::$key[$value];
      	}
      	return false;
  	}

    public static function instance()
    {
        return self::$instance;
    }

    public static function domain()
    {
        //-> getenv(<variable>, true) -> force OS env values
        return str_replace('*', 'store', ((getenv('HTTP_HOST', true) !== false) ? getenv('HTTP_HOST', true) : getenv('HTTP_HOST')));
    }

    public static function this()
    {
        return md5(self::domain());
    }

    public function extension()
    {
        return self::$extension;
    }

    public static function schema($key)
    {
    	if (isset(self::$schema[$key]) === true) {
    		return self::$schema[$key];
    	}
    	return array();
    }

    public static function action($key)
    {
    	if (isset(self::$action[$key]) === true) {
    		return self::$action[$key];
    	}
    	return array();
    }

    public static function responses()
    {
    	return self::$responses;
    }

    public static function response($code)
    {
      if (isset(self::$responses[$code]) === true)
        {
          return self::$responses[$code];
        }
        return self::$responses[600];
    }

    public static function labels()
    {
      return self::$labels;
    }

    public static function getEnviroment($key)
    {
        return (getenv($key, true) ? getenv($key, true) : getenv($key));
    }



}