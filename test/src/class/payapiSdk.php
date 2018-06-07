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
    private $data     = array(
                'staging'   => true,
            );

    public function __construct($mode = 'terminal', $plugin = false)
    {
        if(md5($mode) === md5('server')) {
            //-> loads server hacks for server mode simulation
            require_once(str_replace('src' . DIRECTORY_SEPARATOR . 'class', 'hack', __DIR__) . DIRECTORY_SEPARATOR . 'hack' . '.' . 'server' . '.' . 'php');            
        }
        $this->plugin = (is_string($plugin) === true) ? $plugin : null;
        //-> loads sdk engine
        $this->app = app::single($this->config(), $this->plugin);
        $this->debug = debug::single(true);
    }

    public function config()
    {
        return $this->config;
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
        return $this->response;
    }

    private function stringify($data)
    {
        return  json_encode($data, true);
    }

    private function debug($info, $label = 'test')
    {
        $backtrace = debug_backtrace();
        return $this->debug->add('[' . $label . ']' . '[' . $backtrace[1]['function'] . ']' . $info, $label);
    }


}
