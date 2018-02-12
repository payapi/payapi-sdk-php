<?php

class CommandUpdate extends Controller
{
    public function run()
    {
        $this->getSettingValue('payapi_payments_merchant_id');
        $updated = array();
        $updating = array(
            "merchant_id"          => "merchant_id",
            "default_shipping"     => "shipping",
            "test"                 => "test",
            "debug"                => "debug",
            "instantpayments"      => "instantpayments",
            "demo"                 => "demo",
            "processing_status_id" => "processing_status_id",
            "canceled_status_id"   => "canceled_status_id",
            "failed_status_id"     => "failed_status_id",
            "chargeback_status_id" => "chargeback_status_id",
            "processed_status_id"  => "processed_status_id"

        );
        foreach ($updating as $old => $new) {
            if (is_string($new) === true) {
                $value = $this->getSettingValue('payapi_payments_' . $old);
                if ($value != null && $value != false) {
                    $updated['payapi_' . $new] = $this->config->get('payapi_payments_' . $old);
                }
            }
        }
        if (is_array($updated) === true && $this->config->get('payapi_payments_api_key') != false && $this->config->get('payapi_payments_api_key') != null) {
            $mode = $this->config->get('payapi_payments_test');
            if ($mode == 0) {
                $production = false;
            } else {
                $production = true;
            }
            //-> Sign into the SDK
            $request = $this->payapi->settings($production, $this->config->get('payapi_payments_merchant_id'), $this->config->get('payapi_payments_api_key'));
            if (isset($request['code']) === true && $request['code'] === 200) {
                $this->reset('payapi_payments');
                return $this->add('payapi', $updated);
            }
        }
        return false;
    }

    public function getSettingValue($key, $store_id = 0)
    {
        $query = $this->db->query("SELECT value FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `key` = '" . $this->db->escape($key) . "'");

        if ($query->num_rows) {
            return $query->row['value'];
        } else {
            return null;
        }
    }

    public function edit($key, $value, $pre)
    {
        if (is_string($pre) === true) {
            $prefix = $pre;
        } else {
            $prefix = 'payapi';
        }
        return $this->editSettingValue($prefix, $prefix . '_' . $key, $value);
    }

    private function reset($key)
    {
        $this->deleteSetting($key);
    }

    private function add($key, $data)
    {
        $this->editSetting($key, $data);
    }

    private function editSettingValue($code = '', $key = '', $value = '', $store_id = 0)
    {
        if (!is_array($value)) {
            $this->db->query("UPDATE " . DB_PREFIX . "setting SET `value` = '" . $this->db->escape($value) . "', serialized = '0'  WHERE `code` = '" . $this->db->escape($code) . "' AND `key` = '" . $this->db->escape($key) . "' AND store_id = '" . (int)$store_id . "'");
        } else {
            $this->db->query("UPDATE " . DB_PREFIX . "setting SET `value` = '" . $this->db->escape(json_encode($value)) . "', serialized = '1' WHERE `code` = '" . $this->db->escape($code) . "' AND `key` = '" . $this->db->escape($key) . "' AND store_id = '" . (int)$store_id . "'");
        }
    }

    public function deleteSetting($code, $store_id = 0)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `code` = '" . $this->db->escape($code) . "'");
    }

    private function editSetting($code, $data, $store_id = 0)
    {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE store_id = '" . (int)$store_id . "' AND `code` = '" . $this->db->escape($code) . "'");

        foreach ($data as $key => $value) {
            if (substr($key, 0, strlen($code)) == $code) {
                if (!is_array($value)) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `code` = '" . $this->db->escape($code) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "'");
                } else {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `code` = '" . $this->db->escape($code) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape(json_encode($value, true)) . "', serialized = '1'");
                }
            }
        }
    }
}
