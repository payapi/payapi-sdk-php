<?php

namespace payapi;

final class api extends helper
{

    public $version    = '0.0.2';
    public $request    = false;

    private $env       = false;
    private $curl      = false;
    private $knock     = false;
    private $code      = false;
    private $entity    = false;
    private $headers   = false;
    private $timeout   = 1;
    private $index     = 'no';
    private $follow    = 'no';
    private $mode      = 'sdk';
    private $terminal  = '127.0.0.1';
    private $modes     = array();
    private $responses = array();


    protected function ___autoload()
    {
        $this->modes = param::modes();
        $this->responses = param::responses();
        //-> @TOREVIEW move this to main controller!?
        $this->request = request::single();
        $this->ip = $this->getIp();
        $this->env();
    }

    public function env()
    {   
        if ($this->env === false) {
            if (isset($_SERVER) === true && isset($_SERVER['HTTP_HOST']) === true) {
                $this->env = 'server';
            } else {
                $this->env = 'terminal';
            }
        }
        $this->debug('[APP][ENV] ' . $this->env);
        return $this->env;
    }


    public function domain()
    {
        if ($this->env === 'terminal') {
            return $this->terminal;
        }
        //-> force OS env values if available
        return str_replace('*', 'store', ((getenv('HTTP_HOST', true) !== false) ? getenv('HTTP_HOST', true) : getenv('HTTP_HOST')));
    }

    public function ip()
    {
        return $this->ip;
    }

    public function render($data, $code)
    {
        $label =(($code === 200) ? 'data' : 'error');
        $this->buffer = array(
            "code" => $code,
            $label => $data
        );
        $this->code =($this->checkCode($code) === true) ? $code : 600;
        $this->headers();
        $this->listening();
        $this->debug->blank('=== LISTENING ==>');
        return $this->buffer;
    }

    private function listening()
    {
        $this->debug('[' . $this->code . '] rendering', 'api');
        $this->debug->run();
    }

    public function response($code = false)
    {
        return $this->getApiResponse($code);
    }

    public function returnResponse($code)
    {
        $code = ($this->checkCode($code) === true) ? $code : 600;
        return $this->render($this->responses[$code], $code);
    }

    public function curl(
        $url,
        $secured = true,
        $post = false,
        $timeout = 1,
        $return = 1,
        $header = 0,
        $ssl = 1,
        $fresh = 1,
        $noreuse = 1
    )
    {
        if ($this->curl === false) {
            $this->curl = new curl();
        }
        $checkTimeout =($this->config->staging() === true) ? 2 : $timeout;
        $response = $this->curl->proccess(
            $url,
            $secured,
            $post,
            $checkTimeout,
            $return,
            $header,
            $ssl,
            $fresh,
            $noreuse
        );
        //->
        return $response;
    }

    public function knock()
    {
        if ($this->knock === false) {
            $this->knock = new knock();
        }
        $knock = $this->knock->listen();
        //->
        return $knock;
    }

    public function defaultCode()
    {
        end($this->responses);
        return key($this->responses);
    }

    public function code($responseCode)
    {
        if ($this->isCleanCodeInt($responseCode) === true && isset($this->responses[$responseCode])) {
            return $responseCode;
        }
        return $this->error->notAcceptable();
    }

    public function checkCode($responseCode)
    {
        if ($this->isCleanCodeInt($responseCode) === true && isset($this->responses[$responseCode]) === true) {
            return true;
        }
        return false;
    }

    private function headers()
    {
        if ($this->headers !== true) {
            $this->debug('[headers] disabled');
            return true;
        }
        //-> reset headers
        $this->headers = false;
        $this->debug('[headers] ' . $this->mode);
        if (isset($this->modes[$this->mode]) === true && is_string($this->modes[$this->mode]) === true) {
            header('Content-type: ' . $this->modes[$this->mode]);
        }
        header("X-Robots-Tag: " . $this->index . "index," . $this->follow . "follow");
        return http_response_code($this->code);
    }

    public function mode($mode)
    {
        if (is_string($mode) === true && isset($this->modes[$mode]) === true) {
            return $this->mode = $mode;
        }
        $this->warning('no valid api mode');
        return false;
    }

    public function index($status = false)
    {
        if ($status === true) {
            return $this->index = null;
        }
        return $this->index = 'no';
    }

    public function follow($status = false)
    {
        if ($status === true) {
            return $this->follow = null;
        }
        return $this->follow = 'no';
    }

    private function getApiResponse($responseCode)
    {
        if ($this->isCleanCodeInt($responseCode) === true && isset($this->responses[$responseCode]) === true) {
            $code = $responseCode;
        } else {
            $this->debug('response code no valid', 'api');
            $code = $this->error->notAcceptable();
        }
        return $this->responses[$code];
    }

    private function getIp()
    {
        if ($this->paCallCheck() === true) {
            return $this->paCallValid();
        }
        if ($this->env === 'terminal') {
            return $this->terminal;
        }
        if (($access = $this->sanitize->ip($this->getenvvalue('HTTP_CLIENT_IP'))) == false) {
            if (($access = $this->sanitize->ip($this->getenvvalue('HTTP_X_FORWARDED_FOR'))) == false) {
                if (($access = $this->sanitize->ip($this->getenvvalue('HTTP_X_FORWARDED'))) == false) {
                    if (($access = $this->sanitize->ip($this->getenvvalue('HTTP_FORWARDED_FOR'))) == false) {
                        if (($access = $this->sanitize->ip($this->getenvvalue('HTTP_FORWARDED'))) == false) {
                            if (($access = $this->sanitize->ip($this->getenvvalue('REMOTE_ADDR'))) == false) {
                                $access = $this->serialize->undefined();
                            }
                        }
                    }
                }
            }
        }
        $ip = htmlspecialchars($access, ENT_COMPAT, 'UTF-8');
        return $ip;
    }

    private function paCallCheck()
    {
        if (is_string($this->request->get('payapiwebshop')) === true &&
            is_string($this->request->get('quantity')) === true &&
            is_string($this->request->get('consumerIp')) === true &&
            is_string($this->request->get('locale')) === true &&
            is_string($this->request->get('currency')) === true) {
            return true;
        }
        return false;
    }

    private function paCallValid()
    {
        if (filter_var($this->request->get('consumerIp'), FILTER_VALIDATE_IP) !== false) {
            $this->debug('[PA][valid]');
            return $this->request->get('consumerIp');
        }
        $this->error('[PA][error][consumerIp]');
        return $this->serialize->undefined();
    }

    public function validPAccess()
    {
        if (is_string($this->request->get('payapiwebshop')) === true &&
            is_string($this->request->get('quantity')) === true &&
            is_string($this->request->get('consumerIp')) === true &&
            is_string($this->request->get('locale')) === true &&
            is_string($this->request->get('currency')) === true) {
            return true;
        }
        return false;
    }

    private function getenvvalue($key)
    {
        return (getenv($key, true) != false) ? getenv($key, true) : getenv($key);
    }

    private function isCleanCodeInt($int)
    {
        if (is_int($int) === true && $int >= 200 && $int <= 600) {
            return true;
        }
        return false;
    }
    //-> @TODO
    public function checkIncomingValidSsl($url)
    {
        return $this->checkSsl($url);
    }

    public function __toString()
    {
        return $this->version;
    }
}
