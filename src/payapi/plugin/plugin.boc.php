<?php

namespace payapi;

final class plugin
{

    public static $single = false;

    public $version       = '0.0.1';

    private $native       = false;
    private $config       = false;
    private $db           = false;
    private $session      = false;
    private $code         = 'payapi';

    private function __construct($native)
    {
        $this->native = $native;
        $this->db = $this->native->get('db');
        $this->config = $this->native->get('config');
        $this->session = $this->native->get('session');
        $this->loadLog();
        $this->loadSettings();
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

    public function product($product, $callback = false, $return = false)
    {
        //($product); exit;
        //-> add store prodcut adaption
        if (isset($product['product_id'])) {
            if (isset($product['tax_id']) === true) {
                //-> @TODO
                $vatInCents = null;
            } else {
                $vatInCents = 0;
            }
            if (isset($product['minimum']) === true &&
                is_numeric($product['minimum']) === true && $product['minimum'] > 1) {
                $quantity = $product['minimum'];
            } else {
                $quantity = 1;
            }
            //-> @FIXME
            $product['category'] = 'undefined';
            //-> @TODO handle options
            $processed = array(
                "id"                 => $product['product_id'],
                "quantity"           => $quantity,
                "title"              => $product['name'],
                "description"        => filter_var(strip_tags($product['description']), FILTER_SANITIZE_STRING),
                "model"              => $product['model'],
                "category"           => $product['category'],
                "imageUrl"           => $this->mediaUrl() . $product['image'],
                "priceInCentsExcVat" => $product['priceInCentsEcxVat'],
                "vatInCents"         => $product['vatInCents'],
                "options"            => null,
                "url"                => $product['href']
            );

            //-> disccount
            //return $product;
            //var_dump($processed); exit;
            return $processed;
        }
        //->
        return false;
    }
    //-> @TODO
    public function mediaUrl()
    {
        $mediaUrl = ltrim(MEDIA_URL, '/');
        return $this->domain() . $mediaUrl;
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
            //-> @NOTE @CARE BOC isolates logs itself
            return new \Log(strtolower(__NAMESPACE__) . '.' . 'log');
        }
        return false;
    }

    public function log($info)
    {
        return $this->log($info);
    }

    public function config()
    {
        return $this->native->get('config');
    }

    public function session()
    {
        return $this->native->get('session');
    }

    public function db()
    {
        return $this->native->get('db');
    }
    //-> NEW
    public function consumerId()
    {
        if (isset($this->session->data['customer_id']) === true) {
            return $this->session->data['customer_id'];
        }
        return null;
    }
    //-> TODO
    public function consumerEmail()
    {
        if ($this->consumerId() !== null) {
            $customer_id = $this->consumerId();
            $query =
                $this->db->query("SELECT `email` FROM `" . DB_PREFIX . "customer` WHERE `customer_id` = '" . $customer_id . "' LIMIT 1");
            if (isset($query->row['email']) === true) {
                return $query->row['email'];
            }
        }
        return null;
    }

    public function token()
    {
        return $this->session->data['token'];
    }

    public function tosUrl()
    {
        return null;
    }
    //->
    public function customer($email)
    {
        //-> checks customer data by email
        $query = $this->db->query("SELECT `customer_id` FROM `" . DB_PREFIX . "customer` WHERE ´email´= '" . $email . "' LIMIT 1");
        if (isset($query->row) === true && is_array($query->row) === true) {
            return $query->row['customer_id'];
        }
        return false;
    }

    public function debug()
    {
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
                        'country_id' => $resultCountry->row['country_id'],
                        'zone_id'   => $resultZone->row['zone_id']
                    )
                );
            }
        }
        return $localized;
    }

    private function loadSettings()
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "setting` WHERE `code`= '" . $this->code . "'");
        if (isset($query->rows) === true && is_array($query->rows) === true) {
            foreach ($query->rows as $key => $value) {
                $this->config->set($value['key'], $value['value']);
            }
        }
    }

    public function domain()
    {
        return HTTPS_SERVER;
    }

    public function callbacks($mode)
    {
        return $this->domain() . 'index.php?route=payment/payapi_payments/callback';
    }

    public function returnUrls($mode)
    {
        return $this->domain() . 'index.php?route=payment/payapi_payments/' . $mode;
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
