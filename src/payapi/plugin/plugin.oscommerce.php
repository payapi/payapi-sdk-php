<?php

namespace payapi;

/**
 * Class PluginOscommerce
 * @author efim@payapi.in
 */
final class plugin
{
    public static $single = false;

    public $version       = '0.0.1';

    private $config       = false;

    private function __construct($config)
    {
        $this->config = $config;
        $this->loadLog();
    }

    public function validated()
    {
        return true;
    }

    public function product($product)
    {
        return $product;
    }

    /**
     * adapt specific store cart to secure form
     *
     * @param order (more like cart in OSCommerce) object
     *
     * @return secure form object
     */
    public function payment($tmp_order, $customer_id, $partialPaymentMethod = null)
    {
        $transaction_id = tep_create_random_value(16);

        $finalAddr = [
            'recipientName'   => $tmp_order->delivery['firstname'] . ' '. $tmp_order->delivery['lastname'],
            'co'              => $tmp_order->delivery['company'],
            'streetAddress'   => $tmp_order->delivery['street_address'],
            'streetAddress2'  => $tmp_order->delivery['suburb'],
            'city'            => $tmp_order->delivery['city'],
            'stateOrProvince' => $tmp_order->delivery['state'],
            'postalCode'        => $tmp_order->delivery['postcode'],
            'countryCode'     => $tmp_order->delivery['country']['iso_code_2']
        ];

        $items = $tmp_order->products;
        $products = [];
        if ($items) {
            $hasExtradata = false;
            $totalTaxes = 0;
            foreach ($items as $item) {
                // $imageUrl = tep_output_string(HTTPS_SERVER . DIR_WS_IMAGES . $item['products_image']);

                $excVat  = floatval($item['final_price']);
                $percent = floatval($item['tax']);
                $taxes   = $excVat * $percent / 100.0;
                $incVat  = $excVat + $taxes;
                $qty     = intval($item['qty']);
                $totalTaxes += $qty * $taxes;
                
                $products[] = [
                    "id"                 => $item['id'],
                    "quantity"           => $qty,
                    "title"              => $item['name'],
                    "model"              => $item['model'],
                    "priceInCentsIncVat" => round($incVat * 100),
                    "priceInCentsExcVat" => round($excVat * 100),
                    "vatInCents"         => round($taxes * 100),
                    "vatPercentage"      => $percent,
                    // "imageUrl"           => "",
                ];
                $hasExtradata = true;
            }

            // Merge extra data with the order object (serialized)
            $products[0] = array_merge(
                $products[0],
                array('extraData' => serialize($tmp_order))
            );

            error_log(' products at index 0: '.json_encode($products[0]), 0);
        }

        $shipExcVat  = $tmp_order->info['shipping_cost'];
        $shipTaxes   = floatval($tmp_order->info['tax']) - $totalTaxes;
        $shipIncVat  = $shipExcVat + $shipTaxes;
        $shipPercent = 0;
        if ($shipExcVat > 0) {
            $shipPercent = $shipTaxes * 100.0 / $shipExcVat;
        }

        $products[] = [
            "id"                 => $tmp_order->info['shipping_method'],
            "quantity"           => 1,
            "title"              => 'Handling and Delivery',
            "priceInCentsIncVat" => round($shipIncVat * 100),
            "priceInCentsExcVat" => round($shipExcVat * 100),
            "vatInCents"         => round($shipTaxes * 100),
            "vatPercentage"      => $shipPercent,
            "extraData"          => "",
            // "imageUrl"           => "",
        ];

        $baseExclTax  = $tmp_order->info['subtotal'];
        $taxAmount    = $tmp_order->info['tax'] - $shipTaxes;
        $totalOrdered = $tmp_order->info['total'];

        $order = ["sumInCentsIncVat" => round($totalOrdered * 100),
            "sumInCentsExcVat"       => round(($baseExclTax + $shipExcVat) * 100),
            "vatInCents"             => round(($taxAmount + $shipTaxes) * 100),
            "currency"               => $tmp_order->info['currency'],
            "referenceId"            => $transaction_id,
            "tosUrl"                 => "https://payapi.io/terms"
        ];
                
        $consumer = [
            "email" => $tmp_order->customer['email_address'],
            "consumerId" => (string)$customer_id
        ];
        //Return URLs

        $returnUrls = [
            "success" => $this->getStoreUrl() . "checkout_success.php",
            "cancel"  => $this->getStoreUrl()  ,
            "failed"  => $this->getStoreUrl()
        ];

        $callbackUrl = $this->getStoreUrl() . "ext/modules/payment/payapi/payapi_callbacks.php";
        // $callbackUrl   = $this->getStoreUrl() . "checkout_process.php";
        $jsonCallbacks = [
            "processing" => $callbackUrl,
            "success"    => $callbackUrl,
            "failed"     => $callbackUrl,
            "chargeback" => $callbackUrl,
        ];

        $res = ["order"       => $order,
            "products"        => $products,
            "consumer"        => $consumer,
            "shippingAddress" => $finalAddr,
            "returnUrls"      => $returnUrls,
            "callbacks"       => $jsonCallbacks
            ];

        return $res;

        /*
        $currency = new \Currency($cart->id_currency);

        $sumInCentsIncVat = (float)$cart->getOrderTotal(true, \Cart::BOTH) * 100;
        $sumInCentsExcVat = (float)$cart->getOrderTotal(false, \Cart::BOTH) * 100;

        $vatInCents = $sumInCentsIncVat - $sumInCentsExcVat;

        $referenceId = (string)$cart->id;

        // Terms of Services
        $tosUrl = "https://payapi.io/terms";

        // Assume the schema will take care of empty/null payment method
        $order = array( 'sumInCentsIncVat' => $sumInCentsIncVat,
            'sumInCentsExcVat' => $sumInCentsExcVat,
            'vatInCents' => $vatInCents,
            'currency' => $currency->iso_code,
            'referenceId' => $referenceId,
            'tosUrl' => $tosUrl,
        );
        if ($partialPaymentMethod) {
            $order = array_merge($order, array('preselectedPartialPayment' => $partialPaymentMethod));
        }

        $products_array = array();
        foreach ($cart->getProducts() as $product) {
            // Price calculations
            $prodPriceInCentsExcVat = round($product['price'] * 100);
            $prodPriceInCentsIncVat = round($product['price_with_reduction'] * 100);
            $prodVatInCents = $prodPriceInCentsIncVat - $prodPriceInCentsExcVat;
            $prodVatPercentage = round($prodVatInCents / $prodPriceInCentsExcVat * 100);

            //error_log("attributes: ".$product['attributes'], 0);
            $name_with_attributes = $product['name']." ".$product['attributes'];

            $productObject = array(
                'id' => $product['id_product'],
                'quantity' => $product['quantity'],
                'title' => $name_with_attributes,
                'description' => $product['description_short'],
                'category' => $product['category'],
                'priceInCentsIncVat' => $prodPriceInCentsIncVat,
                'priceInCentsExcVat' => $prodPriceInCentsExcVat,
                'vatInCents' => $prodVatInCents,
                'vatPercentage' => $prodVatPercentage,
            );
            array_push($products_array, $productObject);
        }

        $shipping_cost = (float)$cart->getOrderTotal(true, \Cart::ONLY_SHIPPING) * 100;
        $shipping_cost_no_tax = (float)$cart->getOrderTotal(false, \Cart::ONLY_SHIPPING) * 100;
        // Add shipping cost as a last product
        $shippingObject = array(
            'id' => $cart->id_carrier,
            'quantity' => 1,
            'title' => "Shipping cost",
            'description' => "",
            'category' => "shipping",
            'priceInCentsIncVat' => $shipping_cost,
            'priceInCentsExcVat' => $shipping_cost_no_tax,
            'vatInCents' => 0,
            'vatPercentage' => 0,
        );
        array_push($products_array, $shippingObject);

        // Get shipping address info.
        $address = new \Address((int)$cart->id_address_delivery);

        $customer = new \Customer((int)$address->id_customer);

        $country = new \Country((int)$address->id_country);

        $shipping_address = array(
            'recipientName' => $customer->firstname." ".$customer->lastname,
            'co' => $address->company,
            'streetAddress' => $address->address1,
            'streetAddress2' => $address->address2,
            'postalCode' => $address->postcode,
            'city' => $address->city,
            'countryCode' => $country->iso_code,
        );

        $shop_url = \Tools::getHttpHost(true).__PS_BASE_URI__;

        $returnUrls = array(
            'success' => $shop_url.'index.php?payapireturn=success',
            'cancel' => $shop_url.'index.php?payapireturn=cancel',
            'failed' => $shop_url.'index.php?payapireturn=failed',
        );

        $callbacks = array(
            'processing' => $shop_url.'index.php?fc=module&module=payapi&controller=callback',
            'success' => $shop_url.'index.php?fc=module&module=payapi&controller=callback',
            'failed' => $shop_url.'index.php?fc=module&module=payapi&controller=callback',
            'chargeback' => $shop_url.'index.php?fc=module&module=payapi&controller=callback',
        );

        $secureformObject = array(
            'order' => $order,
            'products' => $products_array,
            'shippingAddress' => $shipping_address,
            'callbacks' => $callbacks,
            'returnUrls' => $returnUrls
        );

        return $secureformObject;
         */
    }

    public function instantPayment($payment)
    {
        return $payment;
    }

    public function loadLog()
    {
        return false;
    }

    public function log($info)
    {
        return true;
    }

    public function config()
    {
        return $this->config;
    }

    public function session()
    {
        return false;
    }

    public function db()
    {
        return false;
    }

    public function customer($email)
    {
        return false;
    }

    public function debug()
    {
        return $this->config['debug'];
    }

    public function nativeVersion()
    {
        return $this->version;
    }

    public function version()
    {
        return $this->version;
    }

    public function staging()
    {
        return $this->config['staging'];
    }

    public function localized($localized)
    {
        /*
        $country_id = \Country::getByIso($localized['countryCode']);
        if ($country_id != false) {
            $zone_id = \Country::getIdZone($country_id);
            if ($zone_id != false) {
                return array_merge(
                    $localized,
                    array(
                        'country_id' => $country_id,
                        'zone_id'    => $zone_id,
                    )
                );
            }
        }
         */
        return $localized;
    }

    public static function single($adapt)
    {
        if (self::$single === false) {
            self::$single = new self($adapt);
        }
        return self::$single;
    }

    public function __toString()
    {
        return $this->version;
    }

    public function demo()
    {
        return false;
    }

    private function getStoreUrl()
    {
        return HTTPS_SERVER . DIR_WS_HTTPS_CATALOG;
    }
}
