<?php

namespace Payapi\PaymentsSdk;

require(__DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'sdk' . '.' . 'php');

class test extends \PHPUnit_Framework_TestCase
{

	public function __construct()
	{
		$this->sdk = new payapiSdkTest();
	}

	public function info()
	{
		return $this->sdk->info(true);
	}

	public function settings()
	{
		return $this->sdk->settings();
	}

	public function success()
	{
		return $this->sdk->response(200);
	}


}