<?php

class Country
{
    public $iso_code;

    public function __construct()
    {
        $this->iso_code = "ES";
    }

    public function __toString()
    {
        return json_encode($this->iso_code);
    }


}