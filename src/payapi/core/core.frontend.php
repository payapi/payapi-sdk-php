<?php

namespace payapi;

final class frontend {

	protected $style = array();
	protected $script = array();
	protected $meta = array();

	private $compressed = true;
	private $config = false;
	private $route = false;
	private $key = false;
	private $template = false;
	private $view = null;
	private $response = array();
	private $debug = false;
	private $wording = false;

	public function __construct($template = false)
	{
		$this->config = config::single();
        $this->route = router::single();
        $this->debug = debug::single();
        $this->wording = wording::single();
		if (isset($template) === true && is_string($template) === true && is_string($this->route->template($template)) == true) {
			$this->key = $template;
		} else {
			$this->key = __NAMESPACE__;
		}
		$this->template = $this->route->template($this->key);
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

	public function render($view, $response)
	{
		if (isset($response['code']) === true) {
			if ($response['code'] === 200) {
				$this->wording($response['data']);
				
			} else {
				$this->response = $response;
			}
		} else {
			$this->response = array(
				"code" => '404',
				"data" => '[frontend] undefined error'
			);
		}
		$this->response['metadata'] = $this->view($this->template . 'common' . DIRECTORY_SEPARATOR . 'metadata' . '.' . 'tpl');
		$this->response['header'] = $this->view($this->template . 'common' . DIRECTORY_SEPARATOR . 'header' . '.' . 'tpl');
		$this->response['navigation'] = $this->view($this->template . 'common' . DIRECTORY_SEPARATOR . 'navigation' . '.' . 'tpl');
		$this->response['footer'] = $this->view($this->template . 'common' . DIRECTORY_SEPARATOR . 'footer' . '.' . 'tpl');
		if (isset($this->response['code']) === true) {
			$this->view = 'error';
		} else {
			$this->view = $view;
		}
		$this->debug('rendering frontend');
		$this->debug('template: ' . $this->key);
		$this->debug('view: ' . $this->view);
		$this->response['content'] = $this->view($this->template . 'view' . DIRECTORY_SEPARATOR . $this->view . '.' . 'tpl');
		$frontend = $this->view($this->template . 'frontend' . '.' . 'tpl');
		if ($this->compressed === true) {
			return $this->compress($frontend);
		}
		return $frontend;
	}

	private function wording()
	{
		$branding = $this->wording->get('branding');
		$this->response['brand'] = $branding['name'];
		$this->response['slogan'] = $branding['slogan'];
		$this->response['title'] = $this->response['brand'] . ', ' . $branding['slogan'];
		$this->response['locale'] = substr($branding['locale'], 0, 2);
		$this->response['logo'] = $branding['logoUrl'];
		$this->response['website'] = $branding['webUrl'];
		$this->response['favicon'] = $branding['iconUrl'];
		$this->response['country'] = $branding['country'];
		$this->response['email'] = $this->encodeHtml($branding['contactEmail']);
		$this->response['hrefEmail'] = $this->encodeHtml('mailto:' . $branding['contactEmail']);
		$this->response['phone'] = $this->encodeHtml($branding['contactPhone']);
		$this->response['hrefPhone'] = $this->encodeHtml('tel:+' . str_replace(' ', null, $branding['contactPhone']));
		$this->response['mobile'] = $this->encodeHtml($branding['contactMobile']);
		$this->response['hrefMobile'] = $this->encodeHtml('tel:+' . str_replace(' ', null, $branding['contactMobile']));
		$this->response['address'] = $this->encodeHtml($branding['address']);
		$this->response['country'] = $this->encodeHtml($branding['country']);
		$this->response['PC'] = $this->encodeHtml($branding['PC']);
		$this->response['city'] = $this->encodeHtml($branding['city']);
		$this->response['region'] = $this->encodeHtml($branding['region']);
		$this->response['support'] = $this->encodeHtml($branding['supportInfoL1']);
		$this->response['copy'] = date('Y', time()) . ' ' . $this->response['title'];
		if (isset($branding['from']) === true && $branding['from'] < date('Y', time())) {
			$this->response['copy'] = $branding['from'] . '-' . date('Y', time());
		} else {
			$this->response['copy'] = date('Y', time());
		}
		$this->response['copyright'] = $this->response['copy'] . ' ' . $this->response['brand'] . ', ' . $branding['slogan'];
		$this->response['minimized'] = '.min';
		$this->response['colorMain'] = $branding['template']['colorMain'];
		$this->response['colorSecond'] = $branding['template']['colorSecond'];
		$this->response['colorThird'] = $branding['template']['colorThird'];
		$this->response['background'] = $branding['template']['background'];
		// FIXME
		//$this->response['minimized'] = '';
		if(isset($branding['template']['colorTitle']) === true) {
			$this->response['colorTitle'] = $branding['template']['colorTitle'];
		} else {
			$this->response['colorTitle'] = $branding['template']['colorMain'];
		}
		if(isset($branding['delegations']) === true) {
			$this->response['delegations'] = $branding['delegations'];
		} else {
			$this->response['delegations'] = array();
		}
		if(isset($branding['template']['fade']) === true) {
			$this->response['fade'] = $branding['template']['fade'];
		} else {
			$this->response['fade'] = '.2';
		}
		if(isset($branding['template']['radius']) === true) {
			$this->response['radius'] = $branding['template']['radius'];
		} else {
			$this->response['radius'] = '22px';
		}
		if(isset($branding['template']['logoAlign']) === true) {
			$this->response['logoAlign'] = $branding['template']['logoAlign'];
		} else {
			$this->response['logoAlign'] = 'left';
		}
	}

	private function view($view)
	{
		extract($this->response);
		ob_start();
		require($view);
		$clean = ob_get_clean();
		return $clean;
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
