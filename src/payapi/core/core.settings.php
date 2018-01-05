<?php

class payapiSettings {

	private
	   static $settings = array();

	public static function get($key)
	{
		if (self::has($key) === true) {
			return self::$settings[$key];
		}
		return false;
	}

	public static function has($key) {
		if (isset(self::$settings[$key]) === true) {
			return true;
		}
		return false;
	}

	public static function set($key, $value)
	{
		self::$settings[$key] = $value;
	}

	public static function enabled()
	{
		if (self::get('demo') == 1 || is_string(self::get('public_id')) === true) {
			return true;
		}
		return false;
	}

	public function merchant()
	{
		return self::get('merchant');
	}


}
