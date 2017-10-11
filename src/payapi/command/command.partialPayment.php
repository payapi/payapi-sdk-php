<?php

namespace payapi;

/*
* @COMMAND
*           $sdk->partialPayment($paymentPriceInCents, $paymentCurrency)
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
*
* @TODO
*          handle currency
*
*/
final class commandPartialPayment extends controller
{

  public function run()
  {
    //->
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
    $partialPaymentSettings = $this->settings('partialPayments');
    //-> checks localization country
    //-> @NOTE: when PA fetch metadata should localize provided ip
    if (isset($partialPaymentSettings['whitelistedCountries']) !== true || $partialPaymentSettings['whitelistedCountries'] === false || in_array($this->localized['countryCode'], $partialPaymentSettings['whitelistedCountries']) === true) {
      if (is_int($paymentPriceInCents) === true && $paymentPriceInCents >= $partialPaymentSettings['minimumAmountAllowedInCents'] && is_string($paymentCurrency) === true) {
        $calculate = array();
        $partial = array();
        $minimumAmountPerMonthInCents =($partialPaymentSettings['minimumAmountAllowedInCents'] / $partialPaymentSettings['numberOfInstallments']);
        $minimumAmountPerMonth =($minimumAmountPerMonthInCents / 100);
        if (round(($paymentPriceInCents / $minimumAmountPerMonthInCents), 0) >= $partialPaymentSettings['numberOfInstallments']) {
          $partial['paymentMonths'] = $partialPaymentSettings['numberOfInstallments'];
        } else {
          $partial['paymentMonths'] = $maximumPricePartialMonths;
        }
        $partial['interestRate'] =(($partialPaymentSettings['nominalAnnualInterestRateInCents'] / 100) / 12) * $partial['paymentMonths'];
        $partial['interestRatePerMonth'] = round(($partial['interestRate'] / $partial['paymentMonths']), 2);
        $partial['interestPriceInCents'] = round((($paymentPriceInCents / 100) * $partial['interestRate']), 0);
        $partial['priceInCents'] = $paymentPriceInCents + $partial['interestPriceInCents'];
        $partial['pricePerMonthInCents'] = round($partial['priceInCents'] / $partial['paymentMonths'], 0);
        $partial['invoiceFeeInCents'] = $partialPaymentSettings['invoiceFeeInCents'];
        $partial['paymentMethod'] = $partialPaymentSettings['preselectedPartialPayment'];
        $partial['invoiceFeeDays'] = $partialPaymentSettings['paymentTermInDays'];
        $partial['currency'] = $paymentCurrency;
        $partial['country'] = $this->localized['countryCode'];
        return $partial;
      }
    }
    return false;
  }


}
