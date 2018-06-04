<?php

namespace payapi;

/*
* @COMMAND
*           $sdk->settings($staging, $payapi_public_id, $payapi_api_enc_key) 
*            - delete cached settings
*            - get settings
*
*           $sdk->settings()     
*            - get cached settings
*
* @TYPE     public
*
* @PARAMS
*.          $staging = boolean
*           $payapi_public_id = string/false
*           $payapi_api_enc_key = string/false
*
* @RETURNS
*           settings array OR $this->error->badRequest()
*
* @SAMPLE
*          ["code"]=>
*           int(200)
*          ["data"]=>
*           array(3) {
*            ["partialPayments"]=>
*             array(9) {
*              ["preselectedPartialPayment"]=>
*               string(12) "pay_in_parts"
*              ["numberOfInstallments"]=>
*               int(36)
*              ["minimumAmountAllowedInCents"]=>
*               int(10000)
*              ["minimumAmountAllowedCurrency"]=>
*               string(3) "EUR"
*              ["invoiceFeeInCents"]=>
*               int(435)
*              ["invoiceFeeCurrency"]=>
*               string(3) "EUR"
*              ["nominalAnnualInterestRateInCents"]=>
*               int(1300)
*              ["paymentTermInDays"]=>
*               int(30)
*              ["whitelistedCountries"]=>
*               array(1) {
*                [0]=>
*                 string(2) "FI"
*               }
*             }
*            ["reseller"]=>
*             string(6) "payapi"
*            ["timestamp"]=>
*             float(1500903420.0991)
*           }
* @CARE
*          most SDK commands are not available till settings is successful
*          public commands: info, localize, settings
*
*
* @NOTE
*          seetings info is cached
*          reseller info is also cached
*
* @VALID
*          schema.settings*
*
* @TODO
*          add stag/prod flag to command
*.         update brand data on settings refresh
*
*/
final class commandSettings extends controller
{

    public function run()
    {
        if ($this->arguments(1) !== $this->serialize->undefined()) {
            $this->cacheDelete();
            $publicId = $this->sanitize->string($this->arguments(1));
            $apiKey = $this->sanitize->string($this->arguments(2));
            $this->config->mode($this->arguments(0));
        } else {
            $publicId = $this->publicId();
            $apiKey = $this->apiKey();
        }
        $this->staging = $this->staging();
        if ($this->arguments(1) == $this->serialize->undefined()) {
            return $this->cacheReturn();
        }
        if ($this->validate->publicId($publicId) === true && $this->validate->apiKey($apiKey) === true) {
            $endPoint = $this->serialize->endPointSettings($publicId);
            $request = $this->curl($endPoint, $this->payload($apiKey), true);
            if ($request !== false && isset($request['code']) === true) {
                if ($request['code'] === 200) {
                    $decodedData = json_decode($this->decode($request['data'], $apiKey), true);
                    $validated = $this->validate->schema($decodedData, $this->load->schema('settings'));
                    if (is_array($validated) !== false) {
                        $error = 0;
                        foreach ($validated as $key => $value) {
                            if ($value !== false && $value !== true) {
                                $settings[$key] = $this->validate->schema(
                                    $value,
                                    $this->load->schema('settings' . '.' . $key)
                                );
                                if (is_array($settings[$key]) === false) {
                                    $error ++;
                                }
                            } else {
                                $settings[$key] = $value;
                            }
                        }
                        if ($error === 0) {
                            $this->cache('writte', 'account', $this->instance(), array(
                                "publicId" => $publicId,
                                "apiKey"   => $this->encode($apiKey, false, true),
                                "staging"  => $this->staging
                            ));
                            $resellerData = $settings['reseller'];
                            $resellerId = $resellerData['partnerId'];
                            $settings['reseller'] = $resellerId;
                            $settings['staging'] = $this->staging;
                            $this->cache('writte', 'reseller', $resellerId, $resellerData);
                            $this->cache('writte', 'settings', $this->instance(), $this->refreshData($settings));
                            return $this->render($this->cache('read', 'settings', $this->instance()));
                        } else {
                            $this->error('no valid settings', 'warning');
                            return $this->returnResponse($this->error->notValidSchema());
                        }
                    } else {
                        //-> not valid schema from PA
                        $this->error('no valid settings', 'warning');
                        return $this->returnResponse($this->error->notValidSchema());
                    }
                } else {
                    return $this->returnResponse($request['code']);
                }
            } else {
                return $this->returnResponse($this->error->unexpectedResponse());
            }
        } else {
            return $this->returnResponse($this->error->badRequest());
        }
        //-> @TOREVIEW @TODO check timeout
        return $this->returnResponse($this->error->timeout());
    }

    private function refreshData($settings)
    {
        $account = $this->cache('read', 'account', $this->instance());
        if (is_array($settings) === true && is_array($account) === true) {
            $this->settings = $settings;
            $this->account = $account;
            return $this->settings;
        }
        $this->settings = false;
        $this->account = false;
        return $this->settings;
    }

    private function cacheReturn()
    {
        $cached = $this->cache('read', 'settings', $this->instance());
        if ($cached !== false) {
            return $this->render($cached);
        } else {
            return $this->returnResponse($this->error->notFound());
        }
    }

    private function cacheDelete()
    {
        $this->debug('[merchant][cache] delete');
        if ($this->cache('read', 'account', $this->instance()) !== false) {
            $this->cache('delete', 'account', $this->instance());
            $this->debug('[account][cache] deleted');
        }
        if ($this->cache('read', 'settings', $this->instance()) !== false) {
            $this->cache('delete', 'settings', $this->instance());
            $this->debug('[settings][cache] deleted');
        }
        return 404;
    }

    private function updateBrand()
    {
        //->
    }

    private function payload($apiKey)
    {
        $payload = array(
            "storeDomain" => $this->api->domain()
        );
        return $this->encode($payload, $apiKey);
    }
}
