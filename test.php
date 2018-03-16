<?php 

namespace Payapi\PaymentsSdk;

require(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'autoload' . '.' . 'php');

final class test {

	public $test      = 'localize';
	public $domain    = 'www.google.com';
	public $ip        = '216.58.210.142';

	private $response = false;
	private $settings = array(
		"debug"   => true,
		"staging" => true
	);

	public function __construct()
	{
		$this->load();
		var_dump($this->response);
		return $this->response;
	}

	private function load()
	{
		$file = __DIR__ . DIRECTORY_SEPARATOR . 'test' . DIRECTORY_SEPARATOR . 'test' . '.' . $this->test . '.' . 'php';
		if (is_file($file) === true) {
			$this->params();
			$sdk = new payapiSdk($this->settings, 'native');
			require($file);
			return $this->response = $test;
		}
		$this->response = array("code" => 404, "error" => 'not found');
		return false;
	}

	private function params()
	{
		putenv('HTTP_HOST=' . $this->domain);
		putenv('HTTP_CLIENT_IP=' . $this->ip);
	}


}

new test();