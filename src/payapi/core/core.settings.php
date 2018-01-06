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
	private static $instance = 'single';

	public function __contruct() {
		self::$instance = instance::this();
		self::$settings[self::$instance] = array();
	}

	public static function get($key)
	{
		if (self::has($key) === true) {
			return self::$settings[self::$instance][$key];
		}
		return false;
	}

	public static function has($key) {
		if (isset(self::$settings[self::$instance][$key]) === true) {
			return true;
		}
		return false;
	}

	public static function set($key, $value)
	{
		self::$settings[self::$instance][$key] = $value;
	}

	public static function enabled()
	{
		if (self::get('demo') == 1 || (is_string(self::get('___public')) === true && self::get('___public') != null)) {
			return true;
		}
		return false;
	}

	public function partials()
	{
		if (self::merchant() != null) {
			if (isset(self::$settings[self::$instance]['merchant']['partialPayments']) === true && is_array(self::$settings[self::$instance]['merchant']['partialPayments'])) {
				return self::$settings[self::$instance]['merchant']['partialPayments'];
			}
		}
		return false;
	}

	public function merchant()
	{
		return self::get('merchant');
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
