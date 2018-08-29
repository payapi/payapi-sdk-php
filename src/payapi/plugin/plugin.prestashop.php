<?php

namespace payapi;

/**
 * Class PluginPrestashop
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

    public function language()
    {
        return 'en-gb';
    }

    public function currency()
    {
        return 'EUR';
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
     * @param cart object
     *
     * @return secure form object
     */
    public function payment($cart, $partialPaymentMethod = null)
    {
        $currency = new \Currency($cart->id_currency);

        $sumInCentsIncVat = (float)$cart->getOrderTotal(true, \Cart::BOTH) * 100;
        $sumInCentsExcVat = (float)$cart->getOrderTotal(false, \Cart::BOTH) * 100;

        $vatInCents = $sumInCentsIncVat - $sumInCentsExcVat;

        $referenceId = (string)$cart->id . "_" . \Tools::passwdGen();

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
            if (array_key_exists('attributes', $product)) {
                $name_with_attributes = $product['name']." ".$product['attributes'];
            } else {
                $name_with_attributes = $product['name'];
            }

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
            //-> florin added 20180418
            if (isset($cart->gallery) === true && isset($cart->gallery[$product['id_product']]['imageUrl']) === true) {
                $productObject['imageUrl'] = $cart->gallery[$product['id_product']]['imageUrl'];
                $this->debug('[product][image] ' . $productObject['imageUrl']);
            }
            array_push($products_array, $productObject);
        }

        //-> discount with/taxes (true)
        $discount = (float)$cart->getOrderTotal(true, \Cart::ONLY_DISCOUNTS);
        if ($discount > 0) {
            $discounted = (int)(0 - round(($discount * 100), 2));
            $discountedExcTaxes = (int)(0 - round(($cart->getOrderTotal(false, \Cart::ONLY_DISCOUNTS) * 100), 2));
            $discountVatInCents = -($discounted - $discountedExcTaxes);
            $discountVatPercentage = -(round($discountVatInCents / $discountedExcTaxes * 100));
            $productDiscount = array(
                'id' => '0',
                'quantity' => 1,
                'title' => 'Discount',
                'description' => 'Discount',
                'category' => 'discount', //-> handled by category
                'priceInCentsIncVat' => $discounted,
                'priceInCentsExcVat' => $discountedExcTaxes,
                'vatInCents' => -$discountVatInCents,
                'vatPercentage' => $discountVatPercentage,
            );
            array_push($products_array, $productDiscount);
        }

        $shipping_cost = (float)$cart->getOrderTotal(true, \Cart::ONLY_SHIPPING) * 100;
        $shipping_cost_no_tax = (float)$cart->getOrderTotal(false, \Cart::ONLY_SHIPPING) * 100;
        // Add shipping cost as a last product
        $shippingObject = array(
            //-> @FIXED 201804031322 florin (added string to shipping ID)
            //   *NOTE @FIXME (using shipping method ID means could get duplicated 'product' ID)
            'id' => (string) $cart->id_carrier,
            //->
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
        $shipping_address = null;
        // In case when user is not registered we will not send shipping address
        if ((int)$cart->id_address_delivery) {
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
        }

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

        $secureformObject = array();
        if ($shipping_address) {
            $secureformObject = array(
                'order' => $order,
                'products' => $products_array,
                'shippingAddress' => $shipping_address,
                'callbacks' => $callbacks,
                'returnUrls' => $returnUrls
            );
        } else {
            $secureformObject = array(
                'order' => $order,
                'products' => $products_array,
                'callbacks' => $callbacks,
                'returnUrls' => $returnUrls
            );
        }
        $this->debug('[cart][payment] ' . json_encode($secureformObject));
        return $secureformObject;
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
}
