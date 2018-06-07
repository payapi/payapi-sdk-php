<?php

use PHPUnit\Framework\TestCase;

class payapiSdkServerTestCommand extends TestCase
{

    private $payapiSdk = false;
    private $test      = null;
    private $account   = array(
                'publicId' => '<public_id>',
                'apiKey'   => '<api_key>',
                'staging'  => true,
            );

    public function __construct()
    {
        //-> @NOTE loads sdk in server mode using native plugin
        $this->payapiSdk = new payapiSdk('server'); // @codeCoverageIgnore
    } // @codeCoverageIgnore

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
        $info = $this->payapiSdk->info();
        $this->test = ((isset($info['data']) === true) ? $info['data'] : 'undefined');
        $this->payapiSdk->debug('[commands] ' . $this->test);
        $this->assertArrayHasKey('data', $info);
    }

    public function testBranding()
    {
        $this->assertArrayHasKey('data', $this->payapiSdk->branding('payapi'));
    }
    //-> @NOTE testMerchantSettingsError deletes merchantSettings if cached
    public function testMerchantSettingsError()
    {
        $merchantSettings = $this->payapiSdk->settings(true, 'reset', 'error');
        $this->assertArrayHasKey('error', $merchantSettings);
    }
    //-> @NOTE testMerchantSettingsSuccess enables SDK private commands
    public function testMerchantSettingsSuccess()
    {
        $merchantSettings = $this->payapiSdk->settings($this->account['staging'], $this->account['publicId'], $this->account['apiKey']);
        //->$this->payapiSdk->debug('[merchantSettings] ' . json_encode($merchantSettings));
        $this->assertArrayHasKey('data', $merchantSettings);
    }

    public function testPaymentError()
    {
        $this->assertArrayHasKey('error', $this->payapiSdk->payment(null));
    }

    public function testPaymentSuccess()
    {
        $payment = new Payment();
        //->$this->payapiSdk->debug('[payment] ' . (string) $payment);
        $params = json_decode((string) $payment, true);
        $paymentSuccess = $this->payapiSdk->payment($params);
        //->$this->payapiSdk->debug('[testPaymentSuccess] ' . json_encode($testPaymentSuccess));
        $this->assertArrayHasKey('data', $paymentSuccess);
    }

    public function testPartialPaymentError()
    {
        $partialPayment = $this->payapiSdk->partialPayment(null, null, false);
        $this->assertArrayHasKey('error', $partialPayment);
    }

    public function testPartialPaymentSuccess()
    {
        $paymentPriceInCents = 10000000;
        $paymentCurrency = 'EUR';
        $demo = true;
        $partialPayment = $this->payapiSdk->partialPayment($paymentPriceInCents, $paymentCurrency, $demo);
        //->$this->payapiSdk->debug('[testPartialPaymentSuccess] ' . json_encode($partialPayment));
        $this->assertArrayHasKey('data', $partialPayment);
    }

    public function testPayload()
    {
        $this->assertArrayHasKey('data', $this->payapiSdk->payload(array()));
    }

    public function testResponseError()
    {
        $this->assertArrayHasKey('error', $this->payapiSdk->response(600));
    }

    public function testResponseSuccess()
    {
        $this->assertArrayHasKey('data', $this->payapiSdk->response(200));
    }

    public function testLocalize()
    {
        $ipRandom = rand(77, 95) . '.' . rand(200, 250) . '.' . rand(200, 250) . '.'. rand(99, 250);
        $this->assertArrayHasKey('data', $this->payapiSdk->localize(true, $ipRandom));
    }


}
