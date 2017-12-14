<?php

namespace payapi;

abstract class controller extends helper
{

    protected $customer                  =     false;
    protected $entity                    =     false;
    protected $session                   =     false;
    protected $data                      =     false;
    protected $token                     =     false;
    protected $cache                     =     false;
    protected $validate                  =     false;
    protected $sanitizer                 =     false;
    protected $load                      =     false;
    protected $api                       =     false;
    protected $localized                 =     false;
    protected $language                  =     false;
    protected $currency                  =     false;
    protected $adaptor                   =     false;
    protected $db                        =     false;
    protected $partialPaymentCountryCode =     false;
    protected $partialPaymentSettings    =     false;

    private   $crypter                   =     false;
    private   $publicId                  =     false;
    private   $apiKey                    =     false;
    private   $account                   =     false;
    private   $settings                  =     false;
    private   $brand                     =     false;
    private   $arguments                 =     false;

    protected function ___autoload($native)
    {
        $this->entity = entity::single();
        $this->crypter = new crypter();
        $this->cache = new cache();
        $this->load = $this->entity->get('load');
        $this->validate = $this->entity->get('validate');
        $this->api = $this->entity->get('api');
        $this->account = $this->cache('read', 'account', $this->instance());
        if (isset($this->account['staging']) === true) {
            $this->config->mode($this->account['staging']);
            $mode = 'STAG';
        } else {
            $mode = 'PROD';
        }
        $this->debug('[mode] ' . $mode);
        if (is_string($this->cache('read', 'ssl', $this->api->ip())) !== true) {
            $validated = $this->validate->ssl();
            if (is_resource($validated) === true) {
                $this->cache('writte', 'ssl', $this->api->ip(),(string) $validated);
            } else {
                return $this->api->returnResponse($this->error->noValidSsl());
            }
        }
        $this->adaptor = $this->entity->get('adaptor');
        $this->language = $this->adaptor->language();
        $this->currency = $this->adaptor->currency();
        //-> @TODO validat PA access
        $this->sdk();
    }

    protected function accessScript()
    {
        //-> validate PA access
        //   to update currency, language, ip
        //-> TODO add get values to API(cgi)
        $expected = array(
            "payapiwebshop",
            "currency",
            "language",
            "quantity",
            //-> @NOTE. @TODO token to implement in PA side?
            "payapi",
            /*"mandatoryFields",*/
            "consumerIp"
        );
        if (isset($_GET['payapi'])) {
            //-> this should be crypted (dyn JWT) (note check just one dot, this is crypted)
            if ($this->validateScriptAccess() === true) {
                $data = array();
                $error = 0;
                foreach ($expected as $key => $value) {
                  if(isset($_GET[$value]) === true) {
                      $data[$value] = addslashes($_GET[$value]);
                  } else {
                      $error ++;
                  }
                }
                if($error === 0) {
                    return $data;
                }
                $this->warning('No valid PA token', 'HACK');
            }
        }
        return false;
    }

    protected function validateScriptAccess()
    {
        //-> @FIXME TODELETE this has to be implemented in PA side
        //   @TODO better to add it in product url contruct in the meanwhile
        //-> @TOUPDATE plz use request model!
        if (isset($_GET['payapiwebshop']) === true && isset($_GET['currency']) === true && isset($_GET['language']) === true && isset($_GET['quantity']) === true) {
            return true;
        }
        return true;
        //->
        if (isset($_GET['payapi']) === true) {
            //-> this should be crypted (dyn JWT) (note check just one dot, this is crypted)
            if (is_string($_GET['payapi']) === true && substr_count($_GET['payapi'], '.') === 1) {
                $payapi = addslashes($_GET['payapi']);
                $hash = md5($this->publicId . date('YmdH', time()));
                $decoded = $this->decode($payapi, $hash);
                if(is_array($decoded) === true) {
                  return true;
                }
            }
        }
        return false; 
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
            if (method_exists('\Payapi\Branding\Branding','getBrandingCode')) {
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
        //-> note if PA access this should be PA query ip
        $this->localized = $this->localization();
        if (isset($this->localized['ip']) === true && isset($this->localized['countryCode']) === true) {
            return true;
        }
        $this->error( 'not localized', 'warning');
        return false;
    }

    protected function localization($requestedIp = false)
    {
        //-> @TODO localization just when needed(just for payments)
        //         validate $requestedIp and handle error(s)
        if(is_string($requestedIp) === true) {
              $ip = $requestedIp;
        } else {
              $ip = $this->ip();
        }
        $cached = $this->cache('read', 'localize', $ip);
        if ($cached !== false) {
            $this->debug('[localized] success');
            //$this->debug(json_encode($cached, JSON_HEX_TAG));
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
        $this->wording = wording::single($this->language);
        $this->wording->set('branding', $this->pluginBranding());

        $this->info();
    }
    //-> @TODELETE
    public function staging()
    {
        return $this->config->staging();
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
        $this->entity->addInfo('adaptor_' . $this->entity->get('plugin') . '_v',(string) $this->adaptor);
        $this->entity->addInfo('api_v',(string) $this->api);
        $this->entity->addInfo('crypter_v',(string) $this->crypter);
        $this->entity->addInfo('validator_v',(string) $this->validate);
        $this->entity->addInfo('sanitizer_v',(string) $this->sanitizer);
        $this->entity->addInfo('serializer_v',(string) $this->serialize);
    }
    //-> SDK passed argument(s)
    protected function arguments($key)
    {
        //-> to filter
        if (isset($this->arguments[0][$key])) {
            return $this->arguments[0][$key];
        }
        return false;
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
        $this->debug('encKey decoded ');
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

    protected function curl($url, $post = false, $secured = true, $timeout = 1, $return = 1, $header = 0, $ssl = 1, $fresh = 1, $noreuse = 1)
    {
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

    protected function calculatePartialPayment($paymentPriceInCents, $paymentCurrency, $countryCode)
    {
        if ($this->partialPayments() === true) {
            $this->partialPaymentSettings = $this->settings('partialPayments');
            if(is_string($countryCode) === true) { //->  && is_array($this->partialPaymentSettings) === true
                if (isset($this->partialPaymentSettings['whitelistedCountries']) !== true || $this->partialPaymentSettings['whitelistedCountries'] === false || in_array($countryCode, $this->partialPaymentSettings['whitelistedCountries']) === true) {
                    if (is_int($paymentPriceInCents) === true && $paymentPriceInCents >= $this->partialPaymentSettings['minimumAmountAllowedInCents'] && is_string($paymentCurrency) === true) {
                        $calculate = array();
                        $partial = array();
                        $minimumAmountPerMonthInCents =($this->partialPaymentSettings['monthlyFeeThresholdInCents'] / $this->partialPaymentSettings['numberOfInstallments']);
                        $minimumAmountPerMonth =($minimumAmountPerMonthInCents / 100);
                        if (round(($paymentPriceInCents / $minimumAmountPerMonthInCents), 0) >= $this->partialPaymentSettings['numberOfInstallments']) {
                            $partial['paymentMonths'] = $this->partialPaymentSettings['numberOfInstallments'];
                        } else {
                            $partial['paymentMonths'] = $maximumPricePartialMonths;
                        }
                        $partial['interestRate'] =(($this->partialPaymentSettings['nominalAnnualInterestRateInCents'] / 100) / 12) * $partial['paymentMonths'];
                        $partial['interestRatePerMonth'] = round(($partial['interestRate'] / $partial['paymentMonths']), 2);
                        $partial['interestPriceInCents'] = round((($paymentPriceInCents / 100) * $partial['interestRate']), 0);
                        $partial['openingFeeInCents'] = $this->partialPaymentSettings['openingFeeInCents'];
                        $partial['invoiceFeeInCents'] = $this->partialPaymentSettings['invoiceFeeInCents'];
                        $partial['priceInCents'] = $paymentPriceInCents + $partial['interestPriceInCents'] + ($partial['invoiceFeeInCents'] * $partial['paymentMonths']);
                        $partial['pricePerMonthInCents'] = round($partial['priceInCents'] / $partial['paymentMonths'], 0);
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
        $data['___public'] = $this->publicId();
        return $data;
    }

    protected function response($code)
    {
        return $this->api->response($code);
    }

    public function returnResponse($code)
    {
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
        //-> @TODO review common caches
        //-> @FIXME token is still isolated per account
        $tokenCoded = $this->encode($token, false, true);
        $cacheKey = str_replace(strtok($tokenCoded, '.') . '.', null, $tokenCoded);
        switch($action) {
            case 'writte' :
                if (is_array($data) !== false) {
                    $data['timestamp'] = $this->serialize->microstamp();
                }
                $encryptedData = $this->encode($data, false, true);
                return $this->cache->writte($type, $cacheKey, $encryptedData);
            case 'read' :
                $cached = $this->cache->read($type, $cacheKey);
                if ($cached !== false) {
                    return $this->decode($cached, false, true);
                }
            break;
            case 'delete' :
                return $this->cache->delete($type, $cacheKey);
            break;
            default :
                return false;
            break;
        }
        return false;
    }


}
