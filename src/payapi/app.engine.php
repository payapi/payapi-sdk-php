<?php

namespace payapi;

final class engine
{

    public static $single = false;

    private $version      = '4.0.5';
    private $plugin       = 'native';
    private $adapt        = false;
    private $debug        = false;
    private $error        = false;
    private $config       = false;
    private $localize     = false;
    private $entity       = false;
    private $route        = false;
    private $validate     = false;
    private $load         = false;
    private $api          = false;
    private $command      = false;
    private $arguments    = false;
    private $public       = array(
        "info"     => true,
        "plugin"   => true,
        "branding" => true,
        "server"   => true,
        "localize" => true,
        "settings" => true
    );

    private function __construct($adapt, $plugin, $branding)
    {
        if (is_string(date_default_timezone_get()) !== true) {
            date_default_timezone_set('UTC');
        }
        if (session_id() === false) {
            session_start();
        }
        foreach (glob(__DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . '*' . '.' . 'php') as $core) {
            require $core;
        }
        $this->route = router::single();
        set_error_handler(array($this, 'error_handler'));
        $this->error = error::single();
        if (is_string($plugin) === true && $this->route->plugin($plugin) !== false) {
            $this->plugin = $plugin;
            $this->adapt = $adapt;
        } else {
            //-> back to native, do not pass object
            if (is_array($adapt) !== false) {
                $this->adapt = $adapt;
            } else {
                $this->adapt = false;
            }
        }
        $this->load();
    }

    private function load()
    {
        $this->entity   = entity::single();
        $this->adaptor  = adaptor::single($this->adapt, $this->plugin);
        $this->config   = config::single();
        $this->debug    = debug::single();
        $this->error    = error::single();
        $this->entity->set('___info', (string) $this);
        $this->debug->add($this->entity->get('___info'));
        $this->debug->add('[plugin] ' . $this->plugin);
        $this->entity->addInfo('sdk_payapi_v', $this->version);
        $this->entity->set('adaptor', $this->adaptor);
        $this->validate = new validator();
        $this->load     = new loader();
        $this->api      = new api();
        $this->debug->load();
        $this->debug->blank('=== LISTENING ==>');
    }

    public function __call($command, $arguments = array())
    {
        if (md5($command) === md5('debug') && isset($arguments[0][0]) === true) {
            return $this->debug->add($arguments[0][0], 'debug');
        }

        return $this->worker($command, $arguments);
    }

    private function worker($command, $arguments)
    {
        $this->entity->set('adaptor', $this->adaptor);
        $this->entity->set('validate', $this->validate);
        $this->entity->set('load', $this->load);
        if ($this->load->command($command) === true) {
            //-> filter/validate
            $this->command = $command;
            $this->arguments = $arguments;
            $this->debug->add('()', 'run');
            return $this->run();
        }
        return $this->api->returnResponse($this->error->notValidMethod());
    }

    private function run()
    {
        $this->entity->set('command', $this->command);
        $this->entity->set('arguments', $this->arguments);
        $this->entity->set('api', $this->api);
        $controller = '\\payapi\\' . 'command' . ucfirst($this->command);
        $command = new $controller($this->adapt);
        if (method_exists($command, 'run') === true) {
            if ($this->validate->publicId($command->publicId()) === true ||
                in_array($this->command, $this->public) === true) {
                $this->debug->run(true);
                return $command->run();
            } else {
                return $command->returnResponse($this->error->forbidden());
            }
        }
    }

    public function error_handler($code, $message, $file, $line)
    {
        if (error_reporting() === 0) {
            return false;
        }
        switch ($code) {
            case E_ERROR:
            case E_USER_ERROR:
                $label = 'fatal';
                break;
            case E_WARNING:
            case E_USER_WARNING:
                $label = 'warning';
                break;
            case E_NOTICE:
            case E_USER_NOTICE:
                $label = 'notice';
                break;
            default:
                $label = 'undefined';
                break;
        }
        $error = $message . ' in ' . $file . ' on line ' . $line;
        return $this->error($error, $label);
    }

    protected function error($error, $label = 'error')
    {
        $this->debug('[' . $label . '] ' . $error, 'error');
        return $this->error->add($error, $label);
    }

    public function __toString()
    {
        return 'PayApi SDK v' . $this->version;
    }

    public static function single($adapt = false, $plugin = false, $branding = false)
    {
        if (self::$single === false) {
            self::$single = new self($adapt, $plugin, $branding);
        }
        return self::$single;
    }
}
