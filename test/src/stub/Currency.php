<?php

class Currency
{
    public $iso_code;

    public function __construct()
    {
        $this->iso_code = "EUR";
    }

    public function __toString()
    {
        return json_encode($this->iso_code);
    }


}