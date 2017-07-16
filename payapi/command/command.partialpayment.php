<?php

namespace payapi ;

final class commandPartialPayment extends controller {

  public function run () {
    //->
    if ( $this -> partialPayments () === true ) {
      if ( is_numeric ( $this -> arguments ( 0 ) ) === true && is_string ( $this -> arguments ( 1 ) ) === true && is_string ( $this -> arguments ( 2 ) ) === true ) {
        //$partialPayment = $this -> calculatePartialPayment ( ( int ) $this -> arguments ( 0 ) ,  $this -> arguments ( 1 ) , $this -> arguments ( 2 ) ) ;
        $partialPayment = $this -> calculatePartialPayment ( ( int ) $this -> arguments ( 0 ) ,  $this -> arguments ( 1 ) , 'FI' ) ;
        if ( is_array ( $partialPayment ) !== false ) {
          return $this -> render ( $partialPayment ) ;
        }
      } else {
        return $this -> returnResponse ( $this -> error -> badRequest () ) ;
      }
    }
    return $this -> returnResponse ( $this -> error -> notImplemented () ) ;
  }

  private function calculatePartialPayment ( $paymentPriceInCents , $paymentCurrency , $country = false ) {
    //-> settings
    $partialPaymentSettings = $this -> settings ( 'partialPayments' ) ;
    if ( in_array ( $country , $partialPaymentSettings [ 'whitelistedCountries' ] ) === true ) {
      if ( is_int ( $paymentPriceInCents ) === true && $paymentPriceInCents >= $partialPaymentSettings [ 'minimumAmountAllowedInCents' ] && is_string ( $paymentCurrency ) === true ) {
        $calculate = array () ;
        $partial = array () ;
        $minimumAmountPerMonthInCents = ( $partialPaymentSettings [ 'minimumAmountAllowedInCents' ] / $partialPaymentSettings [ 'numberOfInstallments' ] ) ;
        $minimumAmountPerMonth = ( $minimumAmountPerMonthInCents / 100 );
        if ( round ( ( $paymentPriceInCents / $minimumAmountPerMonthInCents ) , 0 ) >= $partialPaymentSettings [ 'numberOfInstallments' ] ) {
          $partial [ 'paymentMonths'] = $partialPaymentSettings [ 'numberOfInstallments' ] ;
        } else {
          $partial [ 'paymentMonths'] = $maximumPricePartialMonths ;
        }
        $partial [ 'interestRate' ] = ( ( $partialPaymentSettings [ 'nominalAnnualInterestRateInCents' ] / 100 ) / 12 ) * $partial [ 'paymentMonths'] ;
        $partial [ 'interestRatePerMonth' ] = round ( ( $partial [ 'interestRate' ] / $partial [ 'paymentMonths'] ) , 2 ) ;
        $partial [ 'interestPriceInCents' ] = round ( ( ( $paymentPriceInCents / 100 ) * $partial [ 'interestRate' ] ) , 0 ) ;
        $partial [ 'priceInCents' ] = $paymentPriceInCents + $partial [ 'interestPriceInCents' ] ;
        $partial [ 'pricePerMonthInCents' ] = round ( $partial [ 'priceInCents' ] / $partial [ 'paymentMonths'] , 0 ) ;
        $partial [ 'invoiceFeeInCents' ] = $partialPaymentSettings [ 'invoiceFeeInCents' ] ;
        $partial [ 'paymentMethod' ] = $partialPaymentSettings [ 'preselectedPartialPayment' ] ;
        $partial [ 'invoiceFeeDays' ] = $partialPaymentSettings [ 'paymentTermInDays' ] ;
        $partial [ 'currency' ] = $paymentCurrency ;
        return $partial ;
      }
    }
    return false ;
  }


}
