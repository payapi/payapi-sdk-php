<?php 



namespace Payapi\PaymentsSdk;

use \payapi\engine as app;

class payapiSdkTest
{

    private $app = false;

    public function __construct($adapt = false, $plugin = false, $branding = false)
    {
        //-> loads server hacks for server mode simulation
        require_once(str_replace('test' . DIRECTORY_SEPARATOR . 'src', 'hack', __DIR__) .
            DIRECTORY_SEPARATOR . 'hack' . '.' . 'server' . '.' . 'php');
        //-> loads dependences/phpunit
        require_once(str_replace('src', 'composer', __DIR__) .
            DIRECTORY_SEPARATOR . 'vendor' .DIRECTORY_SEPARATOR . 'autoload' . '.' . 'php');
        //-> loads sdk engine
        require_once(str_replace('test' . DIRECTORY_SEPARATOR, null, __DIR__) . DIRECTORY_SEPARATOR . 'payapi' .
            DIRECTORY_SEPARATOR . 'app' . '.' . 'engine' . '.' . 'php');
        $this->app = app :: single($adapt, $plugin, $branding);
        return $this->app;
    }

    public function __call($command, $arguments = array())
    {
        return $this->app->$command($arguments);
    }
}

$sdk = new payapiSdkTest(array(
	'debug' => true
));
