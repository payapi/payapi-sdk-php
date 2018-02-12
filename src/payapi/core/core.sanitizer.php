<?php

namespace payapi;

final class sanitizer
{

    public static $single = false;

    protected $version    = '0.0.0';

    public function schema($schema, $data)
    {
        if (isset($schema['___schema']) === true && is_array($data) !== false) {
            $diffs = array_diff_key($data, $schema['___schema']);
            foreach ($diffs as $diff => $value) {
                if ($diff != 'numberOfInstallments') {
                    unset($data[$diff]);
                }
            }
            return $data;
        }
        return false;
    }

    public function domain($url)
    {
        $parsed = parse_url($url);
        if (isset($parsed['host']) === true) {
            return $parsed['host'];
        }
        return false;
    }

    public function stream($stream)
    {
        return filter_var($url, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
    }

    public function quotes($url)
    {
        return filter_var($url, FILTER_SANITIZE_MAGIC_QUOTES);
    }

    public function specialChars($string)
    {
        return filter_var($string, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    public function fullSpecialChars($string)
    {
        return filter_var($string, FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES);
    }

    public function int($int)
    {
        return filter_var($int, FILTER_SANITIZE_NUMBER_INT);
    }

    public function string($string)
    {
        return filter_var($string, FILTER_SANITIZE_STRING);
    }

    public function render($render)
    {
        if (is_array($render) !== false) {
            $sanitize = array('___tk');
            foreach ($render as $key => $value) {
                if (in_array($key, $sanitize) === true) {
                    unset($render[$key]);
                }
                if (is_array($value)) {
                    foreach ($value as $key2 => $value2) {
                        if (in_array($key2, $sanitize) === true) {
                            unset($render[$key][$key2]);
                        }
                    }
                }
            }
        }
        return $render;
    }

    public function clean($array)
    {
        $sanitized = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $sanitized[$this->string($key)] = $this->clean($value);
            } else {
                $sanitized[$this->string($key)] = $this->string(addslashes($value));
            }
        }
        return $sanitized;
    }

    public function ip($ip)
    {
        if (md5(filter_var($ip, FILTER_VALIDATE_IP)) === md5($ip) && ip2long($ip) !== false) {
            return preg_replace("/[^0-0.\d]/i", '', $ip);
        }
        return false;
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
