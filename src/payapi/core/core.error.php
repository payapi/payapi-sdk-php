<?php

namespace payapi;

//-> add error loggin
final class error
{

    public static $single = false;

    private $error        = false;
    private $fullTrace    = false;
    private $domain       = false;
    private $instance     = false;
    private $log          = false;
    private $labels       = array();

    private function __construct()
    {
        $this->labes = param::errors();
        $this->domain = instance::domain();
        $this->instance = instance::this();
        $this->log = router::routeError() . date('Ymd') . '.' . 'error' . '.' . 'log';
    }

    private function save($info, $label)
    {
        $trace = serializer::trace(debug_backtrace());
        $entry = (date('Y-m-d H:i:s e', time()) . '[' . $this->domain . '][' .
            $this->instance . '][' . $label . '] ' . $trace . ' ' .
            ((is_string($info)) ?  $info :((is_array($info) ?
            json_encode($info) :((is_bool($info) || is_object($info)) ?(string) $info : 'undefined')))));
        $fileredEntry = filter_var($entry, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        return error_log($fileredEntry . "\n", 3, $this->log);
    }

    public function add($error, $label)
    {
        $checkedLabel =(in_array($label, $this->labels) === true) ? $label : 'undefined';
        $this->error[$checkedLabel][] = $error;
        return $this->save($error, $label);
    }

    public function alert()
    {
        return $this->error;
    }

    public function undefined()
    {
        return 600;
    }

    public function noValidSsl()
    {
        return 505;
    }

    public function notAcceptable()
    {
        return 406;
    }

    public function notImplemented()
    {
        return 501;
    }

    public function badRequest()
    {
        return 400;
    }

    public function notFound()
    {
        return 404;
    }

    public function unexpectedResponse()
    {
        return 406;
    }

    public function timeout()
    {
        return 504;
    }

    public function knockNotValid()
    {
        return 404;
    }

    public function unauthorized()
    {
        return 401;
    }

    public function noValidCronAccess()
    {
        return 404;
    }

    public function transactionDuplicated()
    {
        return 401;
    }

    public function forbidden()
    {
        return 403;
    }

    public function knockUnexpectedSignature()
    {
        return 403;
    }

    public function notValidSchema()
    {
        return 400;
    }

    public function notValidLocalizationSchema()
    {
        return 404;
    }

    public function notValidMethod()
    {
        return 405;
    }

    public function notValidSsl()
    {
        return 505;
    }

    public function notLocalizableAccess()
    {
        return 416;
    }

    public function noCacheSanitization()
    {
        return 409;
    }

    public function notSatisfied()
    {
        return 412;
    }

    public function booBoo()
    {
        return 600;
    }

    public static function single()
    {
        if (self::$single === false) {
            self::$single = new self();
        }
        return self::$single;
    }
}
