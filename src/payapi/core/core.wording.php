<?php

namespace payapi;

class wording {

	public static $single = false;

	private $locale = 'en-en';
	private $data = array();
	private $route = false;
	private $loaded = array();


	private function __construct($locale)
	{
		$this->data = array();
		$this->route = router::single();
		if(is_string($locale) === true && is_string($this->route->dictionary($locale . DIRECTORY_SEPARATOR)) === true) {
			$this->locale = $locale;
		}
		$this->load($this->locale);
	}

	public function set($key, $value)
	{
		$this->data[$key] = $value;
	}

	public function get($key)
	{
		if($this->has($key) === true) {
			return $this->data[$key];
		}
		return false;
	}

	public function has($key)
	{
		if(isset($this->data[$key]) === true) {
			return true;
		}
		return false;
	}

	public function load($key)
	{
		if(in_array($key, $this->loaded)) {
			return true;
		}
		$_ = array();
		$dictionary = $this->route->dictionary($this->locale . DIRECTORY_SEPARATOR . $key);
		if (is_string($dictionary) === true) {
			require $dictionary;
			$this->data = array_merge($this->data, $_);
			$this->loaded[] = $key;
			return true;
		}
		return false;
	}

    public static function single($locale = false)
    {
        if (self::$single === false) {
            self::$single = new self($locale);
        }
        return self::$single;
    }

	public function __toString()
	{
		return json_encode($this->data, true);
	}


}