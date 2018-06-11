<?php

namespace payapi;

abstract class helper
{
    protected $default   = 'payapi';
    protected $config    = false;
    protected $instance  = false; //-> md5(<domain>)
    protected $domain    = false;
    protected $debug     = false;
    protected $sanitizer = false;
    protected $serialize = false;
    protected $error     = false;
    protected $route     = false;
    protected $data      = false;
    
    private $buffer      = false;

    public function __construct($native = false, $plugin = false)
    {
        $this->config = config::single();
        $this->sanitize = new sanitizer();
        $this->route = router::single();
        $this->instance = instance::this();
        $this->domain = instance::domain();
        $this->sanitize = sanitizer::single();
        $this->serialize = serializer::single();
        $this->error = error::single();
        $this->route = router::single();
        $this->debug = debug::single();
        if (method_exists($this, '___autoload')) {
            $version =(isset($this->version) === true) ? ' v' . $this->version : null;
            $this->debug(
                '[autoload] ' . strtolower(str_replace(
                    array(__NAMESPACE__ . '\\command', __NAMESPACE__ . '\\'),
                    null,
                    get_called_class()
                ) . $version)
            );
            $this->___autoload($native, $plugin);
        }
    }

    protected function instance()
    {
        return $this->instance;
    }

    protected function debug($data, $label = 'info')
    {
        if ($this->debug !== false) {
            return $this->debug->add($data, $label);
        }
        return false;
    }

    public function get($key = null)
    {
        if ($key != null) {
            return $this->data[$key];
        }
        return $this->data;
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;
        return $this->data[$key];
    }

    protected function error($error, $label = 'error')
    {
        $this->debug('[' . $label . '] ' . $error, 'error');
        return $this->error->add($error, $label);
    }

    protected function warning($warning, $info = false)
    {
        $label =(is_string($info) === true) ? '[' . $info . '] ' : null;
        return $this->error($label . $warning, 'warning');
    }

    protected function alert()
    {
        return $this->error->alert();
    }

    public function undefined()
    {
        return '___undefined___';
    }

    public function toString()
    {
        if (is_array($this->data) !== false) {
            return json_encode($this->data);
        }
        return $this->undefined();
    }

    public function __destruct()
    {
    }
}
