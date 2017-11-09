<?php

namespace payapi;

final class router
{

    public 
      static  $single                     =   false;

    protected $version                    = '0.0.1';

    private   $root                       =   false;
    private   $instance                   =   false;
    private   $frontversion               =       1;

    private function __construct()
    {
        $this->root = $this->parentDir(__DIR__) . DIRECTORY_SEPARATOR;
        $this->instance = instance::this();
    }

    public function parentDir($dir)
    {
        return str_replace(DIRECTORY_SEPARATOR . basename($dir), null, $dir);
    }

    public function root($key = false)
    {
        if (is_string($key) === true) {
            return $this->root . $key . DIRECTORY_SEPARATOR;
        }
        return $this->root;
    }

    public function dictionary($locale = 'en-gb')
    {
        $dictionary = $this->root . 'locale' . DIRECTORY_SEPARATOR . $locale . DIRECTORY_SEPARATOR;
        if (is_file($dictionary . $locale . '.' . 'php') === true) {
            return $dictionary;
        }
        return false;
    }

    public function wording($key, $locale = 'en-gb', $retry = false)
    {
        $dictionary = $this->dictionary($locale);
        if (is_string($dictionary) === true) {
            $wording = $dictionary . $key . '.' . 'php';
            if (is_file($wording) === true) {
                return $wording;
            } else if ($retry === false) {
                return $this->wording($key, 'en-gb', true);
            }
        }
        return false;
    }

    public function ui()
    {
        return $this->root . 'ui' . DIRECTORY_SEPARATOR . 'v' . $this->frontversion . DIRECTORY_SEPARATOR;
    }

    public function demo($key)
    {
        $template = $this->ui() . 'demo' . DIRECTORY_SEPARATOR . $key . '.' . 'tpl';
        if (is_file($template) === true) {
            return $template;
        }
        return false;
    }

    public function view($key)
    {
        $view = $this->ui() . 'view' . DIRECTORY_SEPARATOR . 'view' . '.' . str_replace('/', '.', $key) . '.' . 'tpl';
        if (is_file($view) === true) {
            return $view;
        }
        return false;
    }

    private function check($dir)
    {
        if (is_dir($dir) !== true) {
            return mkdir($dir, '0755');
        }
        return true;
    }

    private function core()
    {
        return $this->root('core');
    }

    private function routeCommand()
    {
        return $this->root('command');
    }

    private function routeCache()
    {
        return $this->root('cache');
    }

    private function routeData()
    {
        return $this->root('data');
    }

    private function routeTranslator($key)
    {
        $translator = $this->root('data') . 'translator' . DIRECTORY_SEPARATOR . 'translator' . '.' . $key . '.' . 'json';
        if (is_file($translator) === true) {
            return $translator;
        }
        return false;
    }

    private function routeSchema()
    {
        return $this->root('schema');
    }

    public static function routeError()
    {
        return str_replace(DIRECTORY_SEPARATOR . basename(__DIR__), null, __DIR__) . DIRECTORY_SEPARATOR . 'debug' . DIRECTORY_SEPARATOR . 'error' . DIRECTORY_SEPARATOR;
    }

    public function routeDebug()
    {
        return $this->root('debug');
    }

    private function routePlugin()
    {
        return $this->root('plugin');
    }

    public function plugin($key)
    {
        $plugin = $this->routePlugin() . 'plugin' . '.' . $key . '.' . 'php';
        if (is_file($plugin) === true) {
            return $plugin;
        }
        return false;
    }

    public function routePluginBrand($key)
    {
        $pluginBrand = $this->routePlugin() . 'brand' . DIRECTORY_SEPARATOR . 'brand' . '.' . $key . '.' . 'json';
        if (is_file($pluginBrand) === true) {
            return $pluginBrand;
        }
        return false;
    }

    public function command($key)
    {
        $controller = $this->routeCommand() . 'command' . '.' . $key . '.' . 'php';
        if (is_file($controller) === true) {
            return $controller;
        }
        return false;
    }

    public function schema($key)
    {
        $schema = $this->routeSchema() . 'schema' . '.' . $key . '.' . 'json';
        if (is_file($schema) === true) {
            return $schema;
        }
        return false;
    }

    public function cache($type, $key)
    {
        $common = array('localize', 'ssl', 'instance', 'reseller', 'update');
        $isolated =(in_array($type, $common) === true) ? null : 'data' . DIRECTORY_SEPARATOR . $this->instance . DIRECTORY_SEPARATOR;
        $cacheFile = $this->routeCache() . $isolated . $type . DIRECTORY_SEPARATOR . 'cache' . '.' . $key . '.' . 'data';
        return $cacheFile;
    }

    public function brand($key)
    {
        return $this->routeCache() . 'brand' . DIRECTORY_SEPARATOR . 'brand' . '.' . $key . '.' . 'json';
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
        return $this->version;
    }


}
