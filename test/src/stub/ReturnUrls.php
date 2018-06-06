<?php

class ReturnUrls
{
    public $success;
    public $cancel;
    public $failed;

    public function __construct()
    {
        $domain = 'https://api.example.com/';
        $this->success   = $domain . 'payment-success';
        $this->cancel    = $domain . 'payment-cancel';
        $this->failed    = $domain . 'payment-failed';
    }

    public function __toString()
    {
        return json_encode(array(
                    'success'    => $this->success,
                    'cancel'     => $this->cancel,
                    'failed'     => $this->failed,
                ));
    }


}