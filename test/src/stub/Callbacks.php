<?php

class Callbacks
{
    public $processing;
    public $success;
    public $failed;
    public $chargeback;

    public function __construct()
    {
        $domain = 'https://api.example.com/';
        $this->processing = $domain . 'callback-processing';
        $this->success    = $domain . 'callback-success';
        $this->failed     = $domain . 'callback-failed';
        $this->chargeback = $domain . 'callback-chargeback';
    }

    public function __toString()
    {
        return json_encode(array(
                    'processing' => $this->processing,
                    'success'    => $this->success,
                    'failed'     => $this->processing,
                    'chargeback' => $this->processing,            
                ));
    }


}