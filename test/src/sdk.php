<?php 



namespace Payapi\PaymentsSdk;

use \payapi\engine as app;

class payapiSdkTest
{

    private $app = false;
    private $test = false;

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
        $this->tester();
        return $this->app;
    }

    private function tester()
    {
        require_once(__DIR__ . DIRECTORY_SEPARATOR . 'tester' . '.' . 'php');
    	//->return $this->app->settings(true, 'multimerchantshop', 'qETkgXpgkhNKYeFKfxxqKhgdahcxEFc9');
    }

    public function __call($command, $arguments = array())
    {
        $this->test = new tester($this->app->$command($arguments));
        return $this->test->result();
    }
}

$sdk = new payapiSdkTest(array(
	'debug' => true
), 'native', 'payapi');
