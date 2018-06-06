<?php

use PHPUnit\Framework\TestCase;

class payapiSdkTestCore extends TestCase
{

    private $payapiSdk = false;
    private $test      = null;
    private $param     = array();

    public function __construct()
    {
        $this->payapiSdk = new payapiSdk(); // @codeCoverageIgnore
    } // @codeCoverageIgnore

    public function testControl()
    {
        $this->assertNull(null);
    }
    /*

    */


}
