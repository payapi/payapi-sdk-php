<?php

namespace payapi;

/*
* @COMMAND
*           $sdk->partialPayment($paymentPriceInCents, $paymentCurrency, $paymentIp = false)
*
* @PARAMS
*           $paymentPriceInCents = numeric
*           $paymentCurrency = string
*
* @RETURNS
*           partialPayment array OR $this->error->notImplemented()
*
* @SAMPLE
*          ["code"]=>
*           int(200)
*          ["data"]=>
*           array(11) {
*            ["paymentMonths"]=>
*             int(36)
*            ["interestRate"]=>
*             float(39)
*            ["interestRatePerMonth"]=>
*             float(1.08)
*            ["interestPriceInCents"]=>
*             float(39000)
*            ["priceInCents"]=>
*             float(139000)
*            ["pricePerMonthInCents"]=>
*             float(3861)
*            ["invoiceFeeInCents"]=>
*             int(435)
*            ["paymentMethod"]=>
*             string(12) "pay_in_parts"
*            ["invoiceFeeDays"]=>
*             int(30)
*            ["currency"]=>
*             string(3) "EUR"
*            ["country"]=>
*             string(2) "FI"
*           }
*
* @NOTE
*          if 'whitelistedCountries' setting is empty all countries are allowed
*          IP will be handled automachilly if product url are used
*          IMPORTANT [20171011] florin
*                    PA payload merchantSettings.partialPayments updated
*                    - added 'openingFeeInCents' && 'monthlyFeeThresholdInCents'
*
* @TODO
*          handle currency
*
*/

final class commandPartialPayment extends controller
{

    private $partialPaymentCountryCode = false;
    private $partialPaymentSettings = false;

    public function run()
    {
        if ($this->partialPayments() === true) {
            if (is_numeric($this->arguments(0)) === true && is_string($this->arguments(1)) === true) {
                $partialPayment = $this->calculatePartialPayment((int) $this->arguments(0),  $this->arguments(1));
                if (is_array($partialPayment) !== false) {
                    return $this->render($partialPayment);
                } else {
                    return $this->returnResponse($this->error->notSatisfied());
                }
            } else {
                return $this->returnResponse($this->error->badRequest());
            }
        }
        return $this->returnResponse($this->error->notImplemented());
    }

    private function calculatePartialPayment($paymentPriceInCents, $paymentCurrency)
    {
        //-> settings
        $this->partialPaymentSettings = $this->settings('partialPayments');
        //-> checks localization country
        //-> @NOTE: when PA fetch metadata should localize provided ip
        $countryCode = $this->countryCode();
        if(is_string($countryCode) === true) {
            if (isset($this->partialPaymentSettings['whitelistedCountries']) !== true || $this->partialPaymentSettings['whitelistedCountries'] === false || in_array($this->partialPaymentCountryCode, $this->partialPaymentSettings['whitelistedCountries']) === true) {
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
                    $partial['priceInCents'] = $paymentPriceInCents + $partial['interestPriceInCents'];
                    $partial['pricePerMonthInCents'] = round($partial['priceInCents'] / $partial['paymentMonths'], 0);
                    $partial['invoiceFeeInCents'] = $this->partialPaymentSettings['invoiceFeeInCents'];
                    $partial['paymentMethod'] = $this->partialPaymentSettings['preselectedPartialPayment'];
                    $partial['invoiceFeeDays'] = $this->partialPaymentSettings['paymentTermInDays'];
                    $partial['currency'] = $paymentCurrency;
                    $partial['country'] = $this->localized['countryCode'];
                    return $partial;
                }
            }
        }
        return false;
    }

    private function countryCode()
    {
        //->
        if (is_string($this->arguments(2)) === true) {
            $countryCode = $this->localization($this->arguments(2));
        } else {
            $countryCode = $this->localized['countryCode'];
        }        
        //-> @FIXME TODELETE
        $this->warning('countryCode hacked');
        $countryCode = 'FI';
        //->
        if (is_string($countryCode) === true) {
          $this->partialPaymentCountryCode = $countryCode;
          return $countryCode;
        }
        $this->debug('not localized');
        return false;
    }


}
