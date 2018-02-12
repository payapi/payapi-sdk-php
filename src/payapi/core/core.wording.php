<?php

namespace payapi;

//-> @TODO if wording file do not exist revert to en-gb

class wording
{
    public static $single = false;

    private $locale = 'en-gb';
    private $data = array();
    private $route = false;
    private $loaded = array();


    private function __construct($locale)
    {
        //-> @FIXME gets es-es
        //die($locale);
        //-> @TODO validate locale if passedy
        $this->data = array();
        $this->route = router::single();
        if (is_string($locale) === true && is_string($this->route->dictionary($locale)) === true) {
            $this->locale = $locale;
        }
        $this->load($this->locale);
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function get($key = false)
    {
        if ($key === false) {
            return $this->data;
        } elseif ($this->has($key) === true) {
            return $this->data[$key];
        }
        return false;
    }

    public function has($key)
    {
        if (isset($this->data[$key]) === true) {
            return true;
        }
        return false;
    }

    public function load($key)
    {
        //-> @TODO default to en-gb if false
        if (in_array($key, $this->loaded)) {
            return true;
        }
        $dictionary = $this->route->wording($key, $this->locale);
        if (is_string($dictionary) === true) {
            $___ = array();
            require($dictionary);
            $this->data = array_merge($this->data, $___);
            $this->loaded[] = $key;
            return true;
        }
        return false;
    }

    public static function single($locale = false)
    {
        if (self::$single === false) {
            self::$single = new self($locale);
        }
        return self::$single;
    }

    public function __toString()
    {
        return json_encode($this->data, JSON_PRETTY_PRINT);
    }
}
