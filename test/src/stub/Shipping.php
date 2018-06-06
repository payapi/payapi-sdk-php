<?php

class Shipping
{
    public $id;
    public $url;
    public $title;
    public $imageUrl;
    public $category;
    public $priceInCentsExcVat;
    public $priceInCentsIncVat;
    public $vatInCents;
    public $vatPercentage;
    public $quantity;
    public $options;

    public function __construct()
    {
    	$this->id                 = 0;
	    $this->url                = 'https://store.multimerchantshop.xyz/index.php?route=product/product&product_id=41';
	    $this->title              = 'Shipping and handling';
	    $this->imageUrl           = 'https://store.payapi.io/media/43307ac7f356d51e6dd65b8ca9fe3d93/image/cache/catalog/Users/User4/payapi_premium_support-228x228.jpg';
	    $this->category           = 'Shipping';
	    $this->priceInCentsExcVat = 1000;
	    $this->priceInCentsIncVat = 1240;
        $this->vatInCents = $this->priceInCentsIncVat - $this->priceInCentsExcVat;
        $this->vatPercentage = round(($this->vatInCents * 100) / $this->priceInCentsExcVat, 2);
	    $this->quantity           = 1;
	    $this->options            = array ();
    }

    public function __toString()
    {
        return json_encode(array(
                    'id'                 => (string) $this->id,
                    'url'                => $this->url,
                    'title'              => $this->title,
                    'imageUrl'           => $this->imageUrl,            
                    'category'           => $this->category,            
                    'priceInCentsExcVat' => $this->priceInCentsExcVat,            
                    'priceInCentsIncVat' => $this->priceInCentsIncVat,            
                    'quantity'           => $this->quantity,            
                    'vatInCents'         => $this->vatInCents,            
                    'vatPercentage'      => $this->vatPercentage,            
                    'options'            => $this->options,            
                ));
    }


}