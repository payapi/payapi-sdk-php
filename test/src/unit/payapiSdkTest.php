<?php

use PHPUnit\Framework\TestCase;

class payapiSdkTest extends TestCase
{

    private $payapiSdk = false;

    public function testControl()
    {
        $this->assertNull(null);
    }

    public function testSdkLoadNative()
    {
        $this->assertInstanceOf(
            payapiSdk::class,
            $this->payapiSdk
        );
    } // @codeCoverageIgnore

    public function testInfo()
    {
        $this->payapiSdk = new payapiSdk();
        $this->assertArrayHasKey('data', $this->payapiSdk->info());
    }

    public function testBranding()
    {
        $this->payapiSdk = new payapiSdk();
        $this->assertArrayHasKey('data', $this->payapiSdk->branding('payapi'));
    }


}
