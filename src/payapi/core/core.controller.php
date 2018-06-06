<?php

namespace payapi;

//-> include public_id in ALL renders!

abstract class controller extends helper
{

    protected $staging                   = false;
    protected $customer                  = false;
    protected $entity                    = false;
    protected $session                   = false;
    protected $data                      = false;
    protected $token                     = false;
    protected $cache                     = false;
    protected $validate                  = false;
    protected $sanitizer                 = false;
    protected $load                      = false;
    protected $api                       = false;
    protected $localized                 = false;
    protected $language                  = false;
    protected $currency                  = false;
    protected $adaptor                   = false;
    protected $db                        = false;
    protected $partialPaymentCountryCode = false;
    protected $partialPaymentSettings    = false;

    private $crypter                     = false;
    private $publicId                    = false;
    private $apiKey                      = false;
    private $account                     = false;
    private $settings                    = false;
    private $brand                       = false;
    private $arguments                   = false;

    protected function ___autoload($native)
    {
        $this->entity = entity::single();
        $this->crypter = new crypter();
        $this->cache = new cache();
        $this->load = $this->entity->get('load');
        $this->validate = $this->entity->get('validate');
        $this->api = $this->entity->get('api');
        if ($this->api->env() === 'server' && $this->locate() !== true) {
            return $this->returnResponse($this->error->notLocalizableAccess());
        }
        $this->validateAccount();
        $this->adaptor = $this->entity->get('adaptor');
        $this->sdk();
    }

    protected function validateAccount()
    {
        //-> publicId, apiKey, staging, timestamp
        $account = $this->cache('read', 'account', $this->instance());
        if (is_array($account) === true) {
            $filtered = $this->validate->schema($account, $this->load->schema('account'));
            if (is_array($filtered) === true) {
                $this->account = $filtered;
                if ($this->account['staging'] !== false) {
                    $this->config->mode(true);
                } else {
                    $this->config->mode(false);
                }
                return $this->account;
            }
            $this->cache('delete', 'account', $this->instance());
            return $this->warning('[settings] no valid');
        }
        $this->debug('[settings] not found');
    }

    protected function accessConsumer()
    {
        //-> get from adaptor
        $data = array(
            "payapiwebshop" => $this->adapt->mode(),
            "currency" => $this->adapt->currency(),
            "language" => $this->adapt->language(),
            "consumerIp" => $this->ip
        );
    }

    protected function pluginBranding($brand = false)
    {
        if (is_string($brand) === true) {
            $this->debug('checking brand: ' . $brand);
            return $this->getPluginBrandFromKey($brand);
        } else {
            if (method_exists('\Payapi\Branding\Branding', 'getBrandingCode')) {
                $brandFromComposer = new \Payapi\Branding\Branding();
                $brandCode = $brandFromComposer->getBrandingCode();
                $this->debug('checking brand from library: ' . $brandCode);
                return $this->getPluginBrandFromKey($brandCode);
            } else {
                $this->debug('[brand] default');
                return $this->getPluginBrandFromKey($this->default);
            }
        }
        return false;
    }

    private function getPluginBrandFromKey($code)
    {
        if (is_string($code) === true) {
            $pluginBrand = $this->load->pluginBrand($code);
            if (is_array($pluginBrand) === true) {
                $pluginBrand['partnerBackoffice'] = array(
                    "production" => 'input.payapi.io',
                    "staging"    => 'staging-input.payapi.io'
                );
                return $pluginBrand;
            } else {
                $this->warning('invalid plugin branding');
            }
        } else {
            $this->warning('invalid value');
        }
        return false;
    }

    public function locate()
    {
        $this->localized = $this->localization();
        if (isset($this->localized['ip']) === true && isset($this->localized['countryCode']) === true) {
            return true;
        }
        $this->error('not localized', 'warning');
        return false;
    }

    protected function localization($requestedIp = false)
    {
        if ($requestedIp !== false) {
              $ip = $requestedIp;
        } else {
              $ip = $this->ip();
        }
        if ($this->validate->ip($ip) === true) {
            $cached = $this->cache('read', 'localize', $ip);
            if ($cached !== false) {
                $this->debug('[localized] success');
                return $cached;
            }
            $endPoint = $this->serialize->endPointLocalization($ip);
            $request = $this->curl($endPoint, false, false);
            if ($request !== false && isset($request['code']) === true) {
                if ($request['code'] === 200) {
                    $validated = $this->validate->schema($request['data'], $this->load->schema('localize'));
                    if (is_array($validated) !== false) {
                        $this->debug('[localize] valid schema');
                        $adaptedData = $this->adaptor->localized($validated);
                        $this->cache('writte', 'localize', $ip, $adaptedData);
                        $cached = $this->cache('read', 'localize', $ip);
                        //$this->debug(json_encode($cached, JSON_HEX_TAG));
                        return $cached;
                    }
                }
            }
        }
        $this->error('no valid localization', 'warning');
        return false;
    }

    private function sdk()
    {
        $this->arguments = $this->entity->get('arguments');
        $this->entity->remove('validate');
        $this->entity->remove('load');
        $this->entity->remove('api');
        $this->publicId = $this->publicId();
        if ($this->validate->publicId($this->publicId) === true) {
            $this->settings = $this->cache('read', 'settings', $this->instance());
            //-> @NOTE gets merchant settings reseller partnerKey
            $this->brand = $this->cache('read', 'reseller', $this->settings('reseller'));
            $this->token = $this->crypter->instanceToken($this->publicId());
            $this->entity->addInfo('tk', $this->token());
        }
        $this->info();
    }

    protected function staging()
    {
        $mode = $this->config->staging();
        if ($mode !== false) {
            $this->staging = true;
            $mode = 'STAG';
        } else {
            $this->staging = false;
            $mode = 'PROD';
        }
        $this->debug('[mode] ' . $mode);
        return $this->staging;
    }


    public function instance()
    {
        return $this->instance;
    }

    protected function token()
    {
        return str_replace(strtok($this->token, '.') . '.', null, $this->token);
    }

    private function info()
    {
        if ($this->brand !== false) {
            $this->entity->addInfo('brand', $this->brand('partnerName') . ', ' . $this->brand('partnerSlogan'));
        }
        $this->debug('[run] ' . strtolower($this->entity->get('command')));
        $this->entity->addInfo('adaptor_' . $this->entity->get('plugin') . '_v', (string) $this->adaptor);
        $this->entity->addInfo('api_v', (string) $this->api);
        $this->entity->addInfo('crypter_v', (string) $this->crypter);
        $this->entity->addInfo('validator_v', (string) $this->validate);
        $this->entity->addInfo('sanitizer_v', (string) $this->sanitizer);
        $this->entity->addInfo('serializer_v', (string) $this->serialize);
    }
    //-> SDK passed argument(s)
    protected function arguments($key)
    {
        //-> to filter
        if (isset($this->arguments[0][$key])) {
            return $this->arguments[0][$key];
        }
        return $this->serialize->undefined();
    }
    //-> merchantSettings
    protected function settings($key = false)
    {
        if ($this->settings == false) {
            return false;
        }
        if ($key == false) {
            return $this->settings;
        }
        if (isset($this->settings[$key])) {
            return $this->settings[$key];
        }
        return false;
    }
    //-> account login
    public function publicId()
    {
        return $this->account('publicId');
    }

    protected function apiKey()
    {
        $this->debug('encKey decoded');
        return $this->decode($this->account('apiKey'), false, true);
    }

    private function account($key)
    {
        if (is_array($this->account) !== false) {
            if (isset($this->account[$key]) === true) {
                return $this->account[$key];
            }
        }
        return false;
    }

    protected function ip()
    {
        return $this->api->ip();
    }

    protected function curl(
        $url,
        $post = false,
        $secured = true,
        $timeout = 1,
        $return = 1,
        $header = 0,
        $ssl = 1,
        $fresh = 1,
        $noreuse = 1
    ) {
        return $this->api->curl($url, $post, $secured, $timeout, $return, $header, $ssl, $fresh, $noreuse);
    }

    protected function knock()
    {
        return $this->api->knock();
    }

    protected function partialPayments()
    {
        if (is_array($this->settings('partialPayments')) !== false) {
            return true;
        }
        return false;
    }

    protected function calculatePartialPayment($paymentPriceInCents, $paymentCurrency, $demo = false)
    {
        if ($demo === true) {
            $this->debug('[demo] enabled');
            $this->partialPaymentSettings = $this->demoPartialData();
        } elseif ($this->partialPayments() === true) {
            $this->partialPaymentSettings = $this->settings('partialPayments');
        }
        $countryCode = (isset($this->localized['countryCode']) === true) ? $this->localized['countryCode'] : null;
        if (is_array($this->partialPaymentSettings) === true &&
            md5($paymentCurrency) === md5($this->partialPaymentSettings['invoiceFeeCurrency'])) {
            if ($demo === true || is_string($countryCode) === true) {
                if ($demo === true || isset($this->partialPaymentSettings['whitelistedCountries']) !== true ||
                    $this->partialPaymentSettings['whitelistedCountries'] === false ||
                    in_array($countryCode, $this->partialPaymentSettings['whitelistedCountries']) === true) {
                    if (is_int($paymentPriceInCents) === true &&
                        $paymentPriceInCents >= $this->partialPaymentSettings['minimumAmountAllowedInCents'] &&
                        is_string($paymentCurrency) === true) {
                        $calculate = array();
                        $partial = array();
                        $minimumAmountPerMonthInCents =
                            ($this->partialPaymentSettings['monthlyFeeThresholdInCents'] /
                            $this->partialPaymentSettings['numberOfInstallments']);
                        $minimumAmountPerMonth = ($minimumAmountPerMonthInCents / 100);
                        if ($minimumAmountPerMonth < 1 ||
                            round(($paymentPriceInCents / $minimumAmountPerMonthInCents), 0) >=
                            $this->partialPaymentSettings['numberOfInstallments']) {
                            $partial['paymentMonths'] = $this->partialPaymentSettings['numberOfInstallments'];
                        } else {
                            $partial['paymentMonths'] =
                                round(($paymentPriceInCents / $minimumAmountPerMonthInCents), 0);
                        }
                        $partial['interestRate'] =
                            (($this->partialPaymentSettings['nominalAnnualInterestRateInCents'] / 100) / 12) *
                            $partial['paymentMonths'];
                        $partial['interestRatePerMonth'] =
                            round(($partial['interestRate'] / $partial['paymentMonths']), 2);
                        $partial['interestPriceInCents'] =
                            round((($paymentPriceInCents / 100) * $partial['interestRate']), 0);
                        $partial['openingFeeInCents'] = $this->partialPaymentSettings['openingFeeInCents'];
                        $partial['invoiceFeeInCents'] = $this->partialPaymentSettings['invoiceFeeInCents'];
                        $partial['priceInCents'] =
                            $paymentPriceInCents + $partial['interestPriceInCents'] +
                            ($partial['invoiceFeeInCents'] * $partial['paymentMonths']);
                        $partial['pricePerMonthInCents'] =
                            round($partial['priceInCents'] / $partial['paymentMonths'], 0);
                        $partial['paymentMethod'] = $this->partialPaymentSettings['preselectedPartialPayment'];
                        $partial['invoiceFeeDays'] = $this->partialPaymentSettings['paymentTermInDays'];
                        $partial['currency'] = $paymentCurrency;
                        $partial['country'] = $countryCode;
                        return $partial;
                    }
                }
            }
        }
        return false;
    }

    private function demoPartialData()
    {
        return array(
            "preselectedPartialPayment"        => 'partial_demo',
            "paymentTermInDays"                => 8,
            "whitelistedCountries"             => false,
            "openingFeeInCents"                => 900,
            "nominalAnnualInterestRateInCents" => 200,
            "minimumAmountAllowedInCents"      => 100,
            "numberOfInstallments"             => 36,
            "invoiceFeeInCents"                => 900,
            "monthlyFeeThresholdInCents"       => 900,
            "invoiceFeeCurrency"               => 'EUR'
        );
    }

    public function demoConsumerData()
    {
        return array(
            "fullname" => 'John Doe',
            "name" => 'John',
            "surname" => 'Doe',
            "care" => 'Mary Doe',
            "address1" => 'Doe street 7',
            "address2" => null,
            "phone" => '123456789',
            "postal" => '3F4S8K',
            "city" => 'San Diego',
            "region" => 'California',
            "country" => 'United States',
            "email" => 'john-doe@email.com',
            "cardHolder" => 'John Doe',
            "cardNumber" => '4321',
            "cardCvv" => '123',
            "expiration" => '2021'
        ) ;
    }

    protected function brand($key = false)
    {
        if ($key === false) {
            return $this->brand;
        }
        if (isset($this->brand[$key]) === true) {
            return $this->brand[$key];
        }
        return false;
    }

    protected function render($data)
    {
        $render = $this->api->render($data, 200);
        $populate = $this->populate($render);
        $return = (($this->config->debug() === true) ? $this->entity->addExtradata($populate) : $populate);
        $sanitized =  $this->sanitize->render($return);
        return $sanitized;
    }

    protected function populate($data)
    {
        $data['public'] = $this->publicId();
        $data['staging'] = $this->staging;
        return $data;
    }

    protected function response($code)
    {
        return $this->api->response($code);
    }

    public function returnResponse($code)
    {
        //-> @FIXME refresh public_id
        //-> @NOTE 
        //->       could be diferent if settings is called with diferent vars
        $render =$this->api->returnResponse($code);
        $populate = $this->populate($render);
        $return =(($this->config->debug() === true) ? $this->entity->addExtradata($populate) : $populate);
        $sanitized =  $this->sanitize->render($return);
        return $sanitized;
    }

    public function decode($encoded, $hash = false, $crypted = false)
    {
        return $this->crypter->decode($encoded, $hash, $crypted);
    }

    public function encode($decoded, $hash = false, $crypted = false)
    {
        return $this->crypter->encode($decoded, $hash, $crypted);
    }

    protected function cache($action, $type, $token, $data = false)
    {
        $tokenCoded = $this->encode($token, false, true);
        $cacheKey = str_replace(strtok($tokenCoded, '.') . '.', null, $tokenCoded);
        switch ($action) {
            case 'writte':
                if (is_array($data) !== false) {
                    $data['timestamp'] = $this->serialize->microstamp();
                }
                $encryptedData = $this->encode($data, false, true);
                return $this->cache->writte($type, $cacheKey, $encryptedData);
            case 'read':
                $cached = $this->cache->read($type, $cacheKey);
                if ($cached !== false) {
                    return $this->decode($cached, false, true);
                }
                break;
            case 'delete':
                return $this->cache->delete($type, $cacheKey);
            break;
            default:
                return false;
            break;
        }
        return false;
    }

    protected function paymentPayload($products)
    {
        $sumInCentsExcVat = 0;
        $vatInCents = 0;

        foreach ($products as $key => $product) {
        }
    }
}
