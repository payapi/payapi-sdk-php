<?php

namespace payapi;

//-> SDK config
final class config
{

    public static $single = false;

    private $settings     = false;
    private $staging      = false;
    private $instance     = false;
    private $schema       = array(
        "debug" => false,
        "demo"  => false
    );

    private function __construct($config)
    {
        $this->instance = instance::this();
        foreach ($this->schema as $key => $value) {
            if (isset($config[$key]) === true) {
                if ($config[$key] === true) {
                    $this->settings[$key] = true;
                } else {
                    $this->settings[$key] = $value;
                }
            }
        }
    }

    public function __call($key, $arguments = array())
    {
        if ($arguments === array() && is_string($key) === true && isset($this->settings[$key]) === true) {
            return $this->settings[$key];
        }
        //->  @NOTE
        return null;
    }

    //-> @NOTE true enables staging mode, false for production
    public function mode($mode)
    {
        if ($mode !== false) {
            $this->staging = true;
        } else {
            $this->staging = false;
        }
    }

    public function staging()
    {
        return $this->staging;
    }

    public static function single($config = array())
    {
        if (self::$single === false) {
            self::$single = new self($config);
        }
        return self::$single;
    }

    public function __toString()
    {
        return $this->data;
    }
}
