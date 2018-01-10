<?php

namespace payapi;

final class request
{

    public 
       static $single                          =   false;

    private   $method                          =     false;
    private   $shell                           =     false;
    private   $sanitize                        =   array();
    private   $serialize                       =   array();
    private   $data                            =     array(
                  'server'  => array(),
                  'get'     => array(),
                  'post'    => array(),
                  'session' => array()
              );

    private function __construct()
    {
        $this->sanitize = sanitizer::single();
        $this->serialize = serializer::single();
        if (isset($_SERVER) === true && isset($_GET) === true && isset($_POST) === true && isset($_SESSION) === true) {
            $this->data['server'] = $this->sanitize->clean($_SERVER);
            $this->data['get'] = $this->sanitize->clean($_GET);
            $this->data['post'] = $this->sanitize->clean($_POST);
            $this->data['session'] = $this->sanitize->clean($_SESSION);
            $this->method = strtolower($this->server('REQUEST_METHOD'));
        } else {
            $this->shell = true;
        }
    }

    public function method()
    {
          return $this->method;
    }

    public function shell()
    {
          return $this->shell;
    }

    public function server($key)
    {
        if($this->has('server') && isset($this->data['server'][$key]) === true) {
            return $this->data['server'][$key];
        }
        return false;
    }

    public function url()
    {
        if ($this->shell !== true) {
            $protocol = ($this->server('HTTPS') !== false) ? 'https' : 'http';
            return $protocol . ':' . '//' . $this->server('SERVER_NAME') . $this->server('REQUEST_URI');
        }
        return null;
    }

    private function urlBase($sanitize = false)
    {
        $requested = $this->url();
        if (is_string($requested) === true) {
            $parsed = $this->serialize->urlGet($requested);
            parse_str($this->serialize->urlGet($requested, 'query'), $query);
            if(isset($query['mode'])) {

                $clean = str_replace(array('&mode=' . $query['mode'], '&amp;mode=' . $query['mode'], '?mode=' . $query['mode']), null, $requested);
                return $clean;
            } else {
                return $requested;
            }
        }
        return null;
    }

    public function get($key)
    {
        if($this->has('get') && isset($this->data['get'][$key]) === true) {
            return $this->data['get'][$key];
        }
        return null;
    }
    /*
    public function get($key)
    {
        if ($key === false) {
            return $this->data;
        } else
        if ($this->has($key) === true) {
            return $this->data[$key];
        }
        return $key;
    }
    */
    public function post($key)
    {
        if($this->has('post') && isset($this->data['post'][$key]) === true) {
            return $this->data['post'][$key];
        }
        return $key;
    }

    public function session($key)
    {
        if($this->has('session') && isset($this->data['session'][$key]) === true) {
            return $this->data['session'][$key];
        }
        return $key;
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function has($key)
    {
        if (is_string($key) && isset($this->data[$key]) === true) {
            return true;
        }
        return false;
    }

    public static function single()
    {
        if (self::$single === false) {
            self::$single = new self();
        }
        return self::$single;
    }

    public function __toString()
    {
        return json_encode($this->data, true);
    }


}
