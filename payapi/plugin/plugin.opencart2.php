<?php

namespace payapi;

final class plugin
{

  public static
    $single                    =     false;

  public
    $version                   =        '0.0.1';

  private
    $native                    =          false,
    $config                    =          false,
    $db                        =          false;

  private function __construct($native)
  {
    $this->native = $native;
    $this->db = $this->native->get('db');
    $this->config = $this->native->get('config');
    $this->loadLog();
  }

  public function validated()
  {
    //->
    return true;
  }

  public function product($product)
  {
    if (isset($product['product_id'])) {
      $priceExcVat =(isset($product['special'])) ? $product['special'] : $product['price'];
      if (isset($product['tax_id'])) {
        //-> @TODO
        $tax = null;
      } else {
        $tax = 0;
      }
      //-> disccount
      return $product;
    }
    //->
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
      return new \Log(strtolower(__NAMESPACE__) . '.' . date('YmdHis', time()) . '.' . __NAMESPACE__ . '.' . 'log');
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

  public function customer()
  {
    return $this->native->get('customer');
  }

  public function debug()
  {
    if (DEBUGGING === 1) {
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
    if (STAGING === 1) {
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
