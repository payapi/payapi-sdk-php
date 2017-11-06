<?php

namespace payapi;

class serializer
{

    public 
      static  $single                     =   false;

    protected $instance                   =   false;
    protected $version                    = '0.0.1';

    private   $staging                    =   false;

    protected function __construct()
    {
        $this->instance = instance::this();
        $this->domain = instance::domain();
        $this->config = config::single();
        $this->staging = $this->config->get('staging');
    }

    public function publicQueryFlag()
    {
        return md5($this->domain . md5($this->instance));
    }

    public function monetize($price)
    {
        //-> @FIXME monetizes from language code, it should use currency!!
        $monetized = money_format('%.2n', $price);
        if(strpos($monetized, '.') === false) {
            return $monetized;
        }
        return str_replace('.', '<small>.', $monetized) . '</small>';
    }

    public function mode($staging = false)
    {
        if($staging === true || $staging === 1) {
            $this->staging = true;
        } else {
            $this->staging = false;
        }
        return $this->staging;
    }

    public function endPointLocalization($ip)
    {
        $api = $this->https() . $this->staging() . 'input' . '.' . 'payapi' . '.' . 'io' . '/' . 'v1' . '/' . 'api' . '/' . 'fraud' . '/' . 'ipdata' . '/' . $ip;
        return $api;
    }

    public function endPointSettings($publicId, $staging = false)
    {
        $api = $this->https() . $this->staging() . $this->api() . 'merchantSettings' . '/' . $publicId;
        return $api;
    }

    public function endPointInstantBuy($publicId)
    {
        $api = $this->https() . $this->staging() . $this->webshop() . $publicId . '/';
        return $api;
    }

    public function endPointPayment($publicId)
    {
        $api = $this->https() . $this->staging() . $this->payment() . $publicId . '/';
        return $api;
    }

    public function endPointQr($url)
    {
        return $this - https() . $this->staging() . $this->webshop() . 'qr' . '/' . $this->paymentUrlEncode($url);
    }

    private function webshop()
    {
        return $this->endPoint() . 'webshop' . '/';
    }

    private function payment()
    {
        return $this->endPoint() . 'secureform' . '/';
    }

    private function api()
    {
        return $this->endPoint() . 'api' . '/';
    }

    private function endPoint()
    {
        return 'input' . '.' . 'payapi' . '.' . 'io' . '/' . 'v1' . '/';
    }

    private function https()
    {
        return 'https' . ':' . '//';
    }

    private function staging()
    {
        $route =(($this->staging === true) ? 'staging' . '-' : null);
        return $route;
    }

    public function paymentUrlEncode($url)
    {
        return urlencode(html_entity_decode($url));
    }

    public function microstamp()
    {
        return  microtime(true);
    }

    public function getDomainFromUrl($url)
    {
        $parsed = parse_url($url);
        if (isset($parsed['host']) === true) {
            return $parsed['host'];
        }
        return false;
    }

    public function timestamp()
    {
        return date('Y-m-d H:i:s T', time());
    }

    public function undefined()
    {
          return '___undefined___';
    }

    public function options($options)
    {
        $optionsSerialized = '';
        foreach($options as $option => $value) {
            $optionsSerialized .=(($optionsSerialized !== '') ? '&' : null) . $option . '=' . $value;
        }
        return $optionsSerialized;
    }

    public function percentage($total, $part)
    {
        return(int) round((($part * 100) /($total - $part)), 0);
    }

    public function arrayToJson($array)
    {
        $json = json_encode($array, true);
        return $json;
    }

    public function jsonToArray($json, $toArray = false)
    {
        $array = json_decode($json, $toArray);
        return $array;
    }

    public function lenght($value, $lenght)
    {
        if (is_array($value) !== true && is_object($value) !== true) {
            if (preg_match("/^\d{" . $lenght . "}$/", $int) === true) {
                return true;
            }
        }
        return false;
    }

    public function clean($data)
    {
        $cleaned = array();
        foreach ($data as $key => $value) {
            $cleaned[$key] = addslashes($value);
        }
        return $cleaned;
    }

    public function urlGet($url, $key = false)
    {
        //-> parse_url($url) returns array: schema, user, pass, host, port, path, query, fragment
        //-> parse_url($url, <flags>): PHP_URL_SCHEME, PHP_URL_USER, PHP_URL_PASS, PHP_URL_HOST, PHP_URL_PORT, PHP_URL_PATH, PHP_URL_QUERY, PHP_URL_FRAGMENT
        $parsed = parse_url($url);
        //-> TODO anchorRight
        if(is_array($parsed) === true) {
            if (strpos($url, '/#') !== false) {
                $parsed['anchorRight'] = false;
            } else {
                $parsed['anchorRight'] = true;
            }
            if ($key === false) {
                return $parsed;
            } else
            if (is_string($key) === true) {
                if (isset($parsed[$key]) === true) {
                    return $parsed[$key];
                }
            }
        }
        return false;
    }

    public function urlInsertQuery($url, $addQuery)
    {
        //->
        $parsed = $this->urlGet($url);
        $host = (isset($parsed['host']) === true) ? $parsed['host'] : false;
        $secure = (isset($parsed['schema']) === true && $parsed['schema'] != 'http') ? true : false;
        $user = (isset($parsed['user']) === true) ? $parsed['user'] : false;
        $pass = (isset($parsed['pass']) === true) ? $parsed['pass'] : false;
        $port = (isset($parsed['port']) === true) ? $parsed['port'] : false;
        $path = (isset($parsed['path']) === true) ? $parsed['path'] : false;
        $query = (isset($parsed['query']) === true) ? $parsed['query'] : false;
        if (strpos($url, '/#') !== false) {
            $anchorRight = false;
        } else {
            $anchorRight = true;
        }
        $fragment = (isset($parsed['fragment']) === true) ? $parsed['fragment'] : false;
        return $this->urlBuild($host, $secure, $user, $pass, $port, $path, $query, $fragment, $anchorRight);
    }

    public function urlBuild($host, $secure = true, $user = false, $pass = false, $port = false, $path = false, $query = false, $fragment = false, $anchorRight = true)
    {
        if (is_string($host) === true) {
            $schema = 'http';
            if($secure === true) {
              $schema .= 's';
            }
            $url = $schema . '://';
            if (is_numeric($port) === true) {
                $url .= ':' . $port;
            }
            if (is_numeric($path) === true) {
                if (substr($path, 0, 1) !== '/') {
                    $url .= '/';
                }
                $url .= $path;
            }
            if (is_string($fragment) === true) {
                $anchor = '#' . $fragment;
                if ($anchorRight === false) {
                     $url .= $anchor;
                }
            } else {
                $anchor = null;
            }
            if (is_string($query) === true) {
                $url .= '?' . $query;
            }
            if ($anchor != null && $anchorRight === true) {
                $url .= $anchor;
            }
            if (is_string($fragment) === true) {
                $url .= '#' . $fragment;
            }

            return $url;
        }
        return false;
    }

    public static function cleanLogNamespace($route)
    {
        return str_replace(array('payapi\\', 'controller_', 'model_'), null, $route);
    }

    public function __toString()
    {
        return $this->version;
    }

    public static function single()
    {
        if (self::$single === false) {
            self::$single = new self();
        }
        return self::$single;
    }


}
