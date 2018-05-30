<?php 

namespace Payapi\PaymentsSdk;

use \payapi\engine as app;
use \payapi\debug as debug;

class tester {

	private $error      = false;
	private $return     = false;
	private $debug      = false;

	public function __construct($return)
	{
		$this->debug = debug::single(true);
		$this->debug('[autoload] tester');
		$this->return = $return;
	}

	public function result()
	{
		if (isset($this->return['code']) === true) {
			if ($this->return['code'] === 200 && isset($this->return['data']) === true) {
				$this->debug(' success');
				return $this->return['data'];
			} else {
				$this->error[] = '[' . $this->return['code'] . '] ' . $this->return['error'];
			}
		} else {
			$this->error[] = 'undefined error';
		}
		$this->debug(json_encode($this->error, true), 'error');
		return false;
	}

	public function debug($info, $label = 'test')
	{
		return $this->debug->add('[' . $label . ']' . $info, $label);
	}


}