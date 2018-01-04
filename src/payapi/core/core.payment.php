<?php

namespace payapi;

final class payment extends helper
{

	public  $version  = '0.0.1';
	private $products = array();
	private $publicId = null;
	private $shippingAddress = array();
	private $consumer = array();
	private $callbacks = array();
	private $returnUrls = array();
	private $order = array(
		"sumInCentsIncVat"  => 0,
		"sumInCentsExcVat"  => 0,
		"vatInCents"        => 0,
		"currency"          => null,
		"referenceId"       => null

	);


	protected function ___autoload($native = false)
    {
    	//var_dump($this->config); exit;
    }

    public function order($publicId, $currency, $tosUrl = false, $referenceId = false)
    {
    	$this->publicId = $publicId;
    	foreach($this->products as $key => $product) {
    		$this->order['sumInCentsExcVat'] += $product['priceInCentsExcVat'];
    		$this->order['vatInCents'] += $product['vatInCents'];
    	}
    	$this->order['sumInCentsIncVat'] = $this->order['sumInCentsExcVat'] + $this->order['vatInCents'];
    	$this->order['currency'] = $currency;
    	if (is_string($tosUrl) === true) {
    		$this->order['tosUrl'] = $tosUrl;
    	} else {
    		unset($this->order['tosUrl']);
    	}
    	if(is_numeric($referenceId) === true) {
    		$this->order['referenceId'] = (string) $referenceId;
    	} else {
    		$this->order['referenceId'] = $this->orderReferenceId();
    	}
    	return array(
    		"order"           => $this->order,
    		"products"        => $this->products,
    		"shippingAddress" => $this->shippingAddress,
    		"consumer"        => $this->consumer,
    		"callbacks"       => $this->callbacks,
    		"returnUrls"      => $this->returnUrls
    	);
    	
    }

    protected function orderReferenceId()
    {
    	return $this->publicId . '-' . md5(date('YmdHis', time()) . $this->publicId);
    	//->
    }

    public function product($product)
    {
    	$this->products[] = $product;
    }

    private function shippingAddress()
    {
    	return false;
    }

    private function callbacks()
    {
    	return false;
    }

    private function returnUrls()
    {
    	return false;
    }


}