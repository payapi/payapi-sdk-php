<?php

use payapi\engine as app;
use payapi\debug as debug;

class payapiSdk
{

    private $app      = false;
    private $test     = false;
    private $login    = false;
    private $debug      = false;
    private $config   = array(
                'debug' => true
            );
    private $plugin   = 'native';
    private $branding = 'payapi';
    private $data     = array(
                'public_id' => '<public_id>',
                'api_key'   => '<api_key>',
                'staging'   => true,
            );

    public function __construct($plugin = false, $branding = false)
    {
        //-> loads server hacks for server mode simulation
        //require_once(str_replace('test' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'class', 'hack', __DIR__) . DIRECTORY_SEPARATOR . 'hack' . '.' . 'server' . '.' . 'php');
        //-> loads sdk engine
        $this->app = app::single($this->config(), $this->plugin, $this->branding);
        $this->debug = debug::single(true);
        $this->debug('[autoload] tester');

        //$this->login();
    }

    public function config()
    {
        return $this->config;
    }

    public function login()
    {
        if ($this->login === true) {
            return $this->settings($this->data['staging'], $this->data['public_id'], $this->data['api_key']);
        }
    }
    public function __call($command, $arguments = array())
    {
        return $this->app->$command($arguments);
        //return $this->response($this->app->$command($arguments));
    }

    private function response($return)
    {
        if (isset($return['code']) === true) {
            if ($return['code'] === 200 && isset($return['data']) === true) {
                $this->debug(' success');
                $this->response = $return['data'];
            } else {
                $this->error[] = '[' . $return['code'] . '] ' . $return['error'];
            }
        } else {
            $this->error[] = 'undefined error';
        }
        if (is_array($this->error) === true)  {
            $this->debug($this->stringify($this->error), 'error');
        }
        //-> @FIXME @TODELETE
        //var_dump('__debug', $this->response);
        //->
        return $this->response;
    }

    private function stringify($data)
    {
        return  json_encode($data, true);
    }

    private function debug($info, $label = 'test')
    {
        return $this->debug->add('[' . $label . ']' . $info, $label);
    }


}
