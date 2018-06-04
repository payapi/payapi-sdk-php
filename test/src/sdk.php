<?php

namespace Payapi\PaymentsSdk;

use \payapi\engine as app;

class payapiSdkTest
{

    private $app      = false;
    private $test     = false;
    private $login    = false;
    private $config   = array(
                'debug' => true
            );
    private $plugin   = 'native';
    private $branding = 'payapi';
    private $data     = array(
                'public_id' => 'multimerchantshop',
                'api_key'   => 'qETkgXpgkhNKYeFKfxxqKhgdahcxEFc9',
                'staging'   => true,
            );

    public function __construct($plugin = false, $branding = false)
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
        $this->app = app::single($this->config(), $this->plugin, $this->branding);
        $this->tester();
        $this->login();
    }

    public function config()
    {
        return $this->config;
    }

    private function tester()
    {
        require_once(__DIR__ . DIRECTORY_SEPARATOR . 'tester' . '.' . 'php');
    }

    public function login()
    {
        if ($this->login === true) {
            return $this->settings($this->data['staging'], $this->data['public_id'], $this->data['api_key']);
        }
    }

    public function __call($command, $arguments = array())
    {
        $this->test = new tester($this->app->$command($arguments));
        return $this->test->result();
    }


}

$sdk = new payapiSdkTest();
