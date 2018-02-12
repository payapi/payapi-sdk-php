<?php

namespace payapi;

final class plugin
{

    public static $single           =     false;

    public $version                 =   '0.0.1';

    private $native                 =     false;
    private $config                 =     false;
    private $session                =     false;
    private $db                     =     false;
    private $schemaProduct          =    array(
        "product_id"      => true,
        "meta_title"      => true,
        "model"           => false,
        "url"             => true,
        "quantity"        => true,
        "image"           => false,
        "price"           => true,
        "special"         => true,
        "reward"          => false,
        "shipping"        => false,
        "tax_class_id"    => false,
        "subtract"        => false,
        "minimum"         => false,
        "status"          => false
    );

    private function __construct($native)
    {
        $this->native = $native;
        $this->session = $this->session();
        $this->db = $this->db();
        $this->config = $this->config();
        $this->loadLog();
    }

    public function language()
    {
        return $this->session->data['language'];
    }

    public function currency()
    {
        return $this->session->data['currency'];
    }

    public function validated()
    {
      //->
        return true;
    }

    public function product($product)
    {
        if ($this->validateProduct($product) === true) {
            if (is_numeric($product['special']) === true) {
                $priceInCents = $product['special'];
            } else {
                $priceInCents = $product['price'];
            }
            
            $adapted = array(
                "id"              => $product['product_id'],
                "priceInCents"    => $priceInCents,
                "mandatory"       => $this->productHasMandatory($product['product_id']),
                "url"             => $product['url']
            );
            //->
            return $adapted;
        }
        //->
        return false;
    }

    private function validateProduct($product)
    {
        $error = 0;
        if (is_array($product) === true) {
            foreach ($this->schemaProduct as $key => $mandatory) {
                if (isset($product[$key]) !== true && $mandatory === true) {
                    $error ++;
                }
            }
        } else {
            $error ++;
        }
        if ($error === 0) {
            return true;
        }
        return false;
    }

    public function payment($payment)
    {
        //->
        return $payment;
    }

    public function instantPayment($payment)
    {
        //->
        return $payment;
    }

    public function loadLog()
    {
        if ($this->debug() === true) {
            return new \Log(date('Ymd', time()) . '.' . strtolower(__NAMESPACE__) . '.' . 'log');
        }
        return false;
    }

    public function log($info)
    {
        return $this->log($info);
    }

    public function session()
    {
        return $this->native->get('session');
    }

    public function db()
    {
        return $this->native->get('db');
    }

    public function config()
    {
        return $this->native->get('config');
    }

    public function customer($email)
    {
        //-> checks customer data by email
        $query = $this->db->query("SELECT `customer_id` FROM `oc_customer` WHERE ´email´= '" . $email . "' LIMIT 1");
        if (isset($query->row) === true && is_array($query->row) === true) {
            return $query->row;
        }
        return false;
    }

    public function debug()
    {
        //-> @TODO DELETE
        return true;
        if ($this->config->has('payapi_debug') && $this->config->get('payapi_debug') != 0) {
            return true;
        }
        return false;
    }

    public function demo()
    {
        if ($this->config->has('payapi_demo') !== false && $this->config->get('payapi_demo') != false) {
            return true;
        }
        return false;
    }

    public function nativeVersion()
    {
        return VERSION;
    }

    public function staging()
    {
        if ($this->config->has('payapi_test') && $this->config->get('payapi_test') != 0) {
            return true;
        }
        return false;
    }

    public function version()
    {
        return $this->version;
    }

    public function localized($localized)
    {
        $resultCountry = $this->db->query("SELECT `country_id` FROM `" . DB_PREFIX . "country` WHERE `iso_code_2` = '" . $localized['countryCode'] . "'  LIMIT 1");
        if (isset($resultCountry) === true && $resultCountry->num_rows > 0) {
            $resultZone = $this->db->query("SELECT `zone_id` FROM `" . DB_PREFIX . "zone` WHERE `country_id` = '" . $resultCountry->row['country_id'] . "' LIMIT 1");
            if (isset($resultZone) === true && $resultZone->num_rows > 0) {
                return array_merge(
                    $localized,
                    array(
                        'contry_id' => $resultCountry->row['country_id'],
                        'zone_id'   => $resultZone->row['zone_id']
                    )
                );
            }
        }
        return $localized;
    }

    public function productHasMandatory($product_id)
    {
      //->
        return false;
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
}
