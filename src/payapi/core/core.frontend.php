<?php

namespace payapi;

final class frontend {

	protected $style = array();
	protected $script = array();
	protected $meta = array();

	private $compressed         =   true;
	private $config             =   false;
	private $route              =   false;
	private $key                =   false;
	private $template           =   false;
	private $view               =  'load';
	private $response           = array();
	private $debug              =   false;
	private $wording            =   false;

	public function __construct($template = false)
	{
		$this->config = config::single();
        $this->route = router::single();
        $this->debug = debug::single();
        $this->wording = wording::single();
        $this->template = $this->route->ui() . 'demo' . DIRECTORY_SEPARATOR;
	}

	public function encodeHtml($unencoded)
	{
		$encoded = null;
		for ($i = 0; $i < strlen($unencoded); $i++) {
		    $encoded .= '&#'.ord($unencoded[$i]).';';
		}
		return $encoded;
	}

	private function debug($info, $label='front')
	{
		return $this->debug->add($info, $label);
	}

	public function render($view)
	{
		$this->debug('rendering frontend');
		$this->debug('view: ' . $this->view);
		$this->response = $this->view($this->view);
		$frontend = $this->view($view);
		if ($this->compressed === true) {
			return $this->compress($frontend);
		}
		return $frontend;
	}

	private function view($view)
	{
		$template = $this->route->demo($view);
		if(is_file($template) === true) {
			$wording = $this->wording->get();
			$media = 'https://input.payapi.io/';
			extract($wording);
			ob_start();
			require($this->template . 'common' . DIRECTORY_SEPARATOR . 'header' . '.' . 'tpl');
			$header = ob_get_clean();
			ob_start();
			require($this->template . 'common' . DIRECTORY_SEPARATOR . 'footer' . '.' . 'tpl');
			$footer = ob_get_clean();
			ob_start();
			require($template);
			$clean = ob_get_clean();
			return $clean;			
		}
		$this->debug('[debug][frontend] cannot get template: ' . $view);
		return null;
	}

	public function compress($buffer)
	{
		if (strpos($buffer, '<script type="text/javascript">') !== false) {
			$script_search = explode("</script>", $buffer);
			foreach ($script_search as $key => $value) {
				$script_content = explode('<script type="text/javascript">', $value);
				$script_search[$key] = @$script_content[1];
				if ($value != '' && $key < count($script_search) - 1) {
					$scripts[md5($key)] = $script_search[$key];
					$buffer = str_replace($script_search[$key], '[[' . md5($key) . ']]', $buffer);
				}
			}
		} else {
			$scripts = false;
		}
		$buffer = preg_replace(
			array(
				'/<!--(.|\s)*?-->/',
				'/\n/',
				'/\t+/',
				'/\s\s+/',
				'/ \/>/',
				'/> </',
				'/" >/',
				'/; </',
				'/; } </',
				'/; -/',
				'/; /',
				'/{ /',
				'/: #/',
				'/> :root { -/'
			),
			array(
				'',
				' ',
				' ',
				' ',
				'/>',
				'><',
				'">',
				';<',
				';}<',
				';-',
				';',
				'{',
				':#',
				'>:root{-'
			),
			$buffer
		);
		// if there is any script
		if ($scripts != null) {
			foreach ($scripts as $key => $value) {
				$buffer = str_replace('[['.$key.']]', preg_replace('/\t+/', '', $value), $buffer);
			}
		}

		return $buffer;
	}

	public function addStyle($style)
	{
		$this->style[] = $style;
	}

	public function style()
	{
		return $this->style;
	}

	public function addScript($script)
	{
		$this->script[] = $script;
	}

	public function script()
	{
		return $this->script;
	}

	public function addMeta($meta)
	{
		$this->meta[] = $meta;
	}

	public function meta()
	{
		return $this->meta;
	}

	public function compressed($enable = false)
	{
		if($enable === true) {
			return $this->compressed = true;
		}
		return $this->compressed = false;
	}


	public function __toString()
	{
		return $this->template;
	}

  
}
