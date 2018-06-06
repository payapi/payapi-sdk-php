<?php

class Payment
{
    public $products;
    public $order;
    public $callbacks;
    public $returnUrls;

    public function __construct()
    {
        $product     = new Product();
        $shipping    = new Shipping();
        $order       = new Order();
        $callbacks   = new Callbacks();
        $returnUrls  = new ReturnUrls();
        $this->products       = array(
            json_decode((string) $product, true),
            json_decode((string) $shipping, true),
        );
        $this->order          = json_decode((string) $order, true);
        $this->callbacks      = json_decode((string) $callbacks, true);
        $this->returnUrls     = json_decode((string) $returnUrls, true);
        $this->calculate();
    }

    private function calculate()
    {
        $priceInCentsExcVat = 0;
        $priceInCentsIncVat = 0;
        foreach($this->products as $key => $product) {
            $priceInCentsExcVat += $product['priceInCentsExcVat'];
            $priceInCentsIncVat += $product['priceInCentsIncVat'];
        }
        $this->order['sumInCentsExcVat'] = $priceInCentsExcVat * $product['quantity'];
        $this->order['sumInCentsIncVat'] = $priceInCentsIncVat * $product['quantity'];
        $this->order['vatInCents'] = $this->order['sumInCentsIncVat'] - $this->order['sumInCentsExcVat'];
        $this->order['vatPercentage'] = round(($this->order['vatInCents'] * 100) / $priceInCentsExcVat, 2);
    }

    public function __toString()
    {
        return json_encode(array(
                    'products'        => $this->products,
                    'order'           => $this->order,
                    'callbacks'       => $this->callbacks,
                    'returnUrls'      => $this->returnUrls,
                ));
    }


}