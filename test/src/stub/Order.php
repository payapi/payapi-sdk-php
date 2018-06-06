<?php

class Order
{
    public $currency;
    public $referenceId;
    public $tosUrl;

    public function __construct()
    {
        $domain   = 'https://api.example.com/';
        $currency = new Currency();
        $this->currency       = json_decode((string) $currency, true);
        $this->referenceId    = 'order#' . rand(999,2000) . '';
        $this->tosUrl         = $domain . 'terms';
    }

    public function __toString()
    {
        return json_encode(array(
            'currency'        => $this->currency,
            'referenceId'     => $this->referenceId,
            'tosUrl'          => $this->tosUrl,
        ));
    }


}