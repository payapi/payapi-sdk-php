<?php
/*
* 
*  instance isolated settings static model
*
*  @NOTE for optional use in the store side
*
*        SDK does not handle this model,
*        just load it so it is available
*
*/

final class payapiSettings {

	private static $single = false;
	private static $settings = array();

	private static function isolated()
	{
		return md5(str_replace('*', 'store', ((getenv('HTTP_HOST', true) !== false) ? getenv('HTTP_HOST', true) : getenv('HTTP_HOST'))));
	}

	public static function boolify($key)
	{
		if (self::get($key) !== false && self::get($key) !== 0) {
			return 'true';
		}
		return 'false';
	}

	public static function get($key)
	{
		if (self::has($key) === true) {
			return self::$settings[self::isolated()][$key];
		}
		return false;
	}

	public static function has($key) {
		if (isset(self::$settings[self::isolated()][$key]) === true) {
			return true;
		}
		return false;
	}

	public static function set($key, $value)
	{
		self::$settings[self::isolated()][$key] = $value;
	}

	public static function enabled()
	{
		if (self::get('sandbox') == 1 || (is_string(self::get('___public')) === true && self::get('___public') != null)) {
			return true;
		}
		return false;
	}

	public function partials()
	{
		if (is_array(self::merchant()) === true) {
			if (isset(self::$settings[self::isolated()]['merchant']['partialPayments']) === true && is_array(self::$settings[self::isolated()]['merchant']['partialPayments'])) {
				return self::$settings[self::isolated()]['merchant']['partialPayments'];
			}
		}
		return false;
	}

	public function merchant()
	{
		return self::get('merchant');
	}

	public function paymentGateway()
	{
		if (isset(self::$settings[self::isolated()]['merchant']['enabledPaymentGateways']) === true) {
			return self::$settings[self::isolated()]['merchant']['enabledPaymentGateways'];
		}
		//-> @TOUPDATE AFTER UPDATE
		return true;
		//return false;
	}

	public function resume()
	{
		return json_encode(self::$setting, true);
	}
	//-> @NOTE single is not forced
	public static function single()
	{
		if (self::$single === false) {
			self::$single = new self;
		}
		return self::$single;
	}


}
