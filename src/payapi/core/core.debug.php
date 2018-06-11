<?php

namespace payapi;

final class debug
{
    public static $single = false;

    protected $history    = false;

    private $enabled      = false;
    private $microtime    = false;
    private $lapses       = 0;
    private $run          = 0;
    private $lapse        = array();
    private $fullTrace    = false;
    private $dir          = false;
    private $file         = false;
    private $labels       = array();

    protected function __construct($enabled)
    {
        $this->labels = param::labels();
        // $this->route
        if ($enabled !== true) {
            return false;
        } else {
            $this->enabled = true;
        }
        $this->microtime = microtime(true);
        $this->lapse('execution', true);
        $this->lapse('app', true);
        files::checkDir(router::debug());
        $this->file = router::debug() . 'debug.' . __NAMESPACE__ . '.' . 'log';
        $this->reset();
        $this->set('=== DEBUG === ' . $this->timestamp() . ' ==>');
    }

    public function load()
    {
        return $this->timing('load', (microtime(true) - $this->microtime));
    }

    public function run($refresh = false)
    {
        if ($refresh === true) {
            $this->run = microtime(true);
        } else {
            $microseconds =(microtime(true) - $this->run);
            return $this->timing('run', $microseconds);
        }
    }

    public function lapse($key, $refresh = false)
    {
        if (isset($this->lapse[$key]) === true && is_numeric($this->lapse[$key]) && $refresh !== true) {
            $lapse =(microtime(true) - $this->lapse[$key]);
            switch ($key) {
                case 'app':
                    $microseconds =($lapse - $this->lapses);
                    break;
                case 'execution':
                    $microseconds = $lapse;
                    break;
                default:
                    if ($key != 'run') {
                        $this->lapses += $lapse;
                    }
                    $this->run += $lapse;
                    $microseconds = $lapse;
                    break;
            }
            return $this->timing($key, $microseconds);
        }
        $this->lapse[$key] = microtime(true);
    }

    private function milisecons($microseconds)
    {
        $miliseconds = (round($microseconds, 3) * 1000);
        return $miliseconds;
    }

    private function reset()
    {
        $this->history = false;
        file_put_contents($this->file, '');
        return $this->blank();
    }

    private function timing($key, $microseconds)
    {
        $timing = '[' . $key . '] timing ' . $this->milisecons($microseconds) . 'ms.';
        return $this->add($timing, 'time');
    }

    private function timestamp()
    {
        return date('Y-m-d H:i:s e', time());
    }

    public function add($info, $label = 'info')
    {
        $trace = serializer::trace(debug_backtrace());
        $miliseconds = str_pad(round((microtime(true) - $this->microtime) * 1000, 0), 4, '0', STR_PAD_LEFT);
        $entry = ($miliseconds . ' [' . $this->label($label) . '] ' . $trace . ' ' .
            ((is_string($info)) ? $info :((is_array($info) ? json_encode($info) :
            ((is_bool($info) || is_object($info)) ?(string) $info : 'undefined')))));
        $this->history[] = $entry;
        return $this->set($entry);
    }

    public function blank($info = null)
    {
        $this->set($info);
    }

    public function label($label)
    {
        if (is_string($label) && preg_match('~^[a-z]+$~i', $label) && in_array($label, $this->labels)) {
            return $label;
        }
        reset($this->labels);
        return current($this->labels);
    }

    public function history()
    {
        return $this->history;
    }

    protected function set($entry)
    {
        if ($this->enabled !== true) {
            return false;
        }
        $fileredEntry = filter_var($entry, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        return error_log($fileredEntry . "\n", 3, $this->file);
    }

    public static function single($enabled = false)
    {
        if (self::$single === false) {
            self::$single = new self($enabled);
        }
        return self::$single;
    }

    public function __toString()
    {
        return json_encode($this->history, true);
    }

    public function __destruct()
    {
        $this->lapse('app');
        $this->set('=== ' . $this->milisecons(microtime(true) - $this->microtime) .
            'ms. === ' . $this->timestamp() . ' ===');
        $this->blank();
    }
}
