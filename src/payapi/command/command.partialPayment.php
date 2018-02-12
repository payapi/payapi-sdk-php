<?php

namespace payapi;

/*
* @COMMAND
*           $sdk->partialPayment($paymentPriceInCents, $paymentCurrency, $paymentCountry, $demo = false)
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
//-> @TODO demo mode
final class commandPartialPayment extends controller
{

    public function run()
    {
        if ($this->partialPayments() === true || $this->arguments(2) === true) {
            if (is_numeric($this->arguments(0)) === true && is_string($this->arguments(1)) === true) {
                $partialPayment =
                    $this->calculatePartialPayment((int)$this->arguments(0), $this->arguments(1), $this->arguments(2));
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
}
