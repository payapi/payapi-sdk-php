<?php

namespace payapi ;

final class partialpayment {

  public function calculatePartialPayment ( $productPrice , $productHandlingExcludingVate , $productCurrency ) {
    if ( is_numeric ( $productPrice ) === true && is_numeric ( $productHandlingExcludingVate ) === true && $productPrice >= 50 && $productHandlingExcludingVate > 0 && is_string ( $productCurrency ) === true ) {
      $partial = array (
        "settingsInvoiceFee"                     => 12 , //-> does not affect (fee for not paid invoice)
        "settingsExpirationDays"                 => 8 ,
        "settingsMaximumMonths"                  => 36 ,
        "settingsMinimumAmountPerMonthInCents"   => 990 ,
        "settingsInterestRatePerCent"            => 5 ,
        "settingsPricesCurrency"                 => null ,
        "currency"                               => $productCurrency
      ) ;
      $partial [ 'settingsMinimumAmountPerMonth' ] = ( $partial [ 'settingsMinimumAmountPerMonthInCents' ] / 100 ) ;
      $partial [ 'paymentProductTotalPrice' ] = round ( ( int ) $productPrice + ( int ) $productHandlingExcludingVate , 0 ) ;
      $maximumPricePartialMonths = round ( ( $partial [ 'paymentProductTotalPrice' ] / $partial [ 'settingsMinimumAmountPerMonth' ] ) , 0 ) ;
      if ( $maximumPricePartialMonths > $partial [ 'settingsMaximumMonths' ] ) {
        $partial [ 'paymentMonths'] = $partial [ 'settingsMaximumMonths' ] ;
      } else {
        $partial [ 'paymentMonths'] = $maximumPricePartialMonths ;
      }
      $partial [ 'paymentProductPricePerMonth' ] = ( $partial [ 'paymentProductTotalPrice' ] / $partial [ 'paymentMonths'] ) ;
      $partial [ 'paymentTotalInterestPrice' ] = ( $partial [ 'paymentProductTotalPrice' ] * ( $partial [ 'settingsInterestRatePerCent' ] / 100 ) ) ;
      $partial [ 'paymentInteresPricePerMonth' ] = $partial [ 'paymentTotalInterestPrice' ] / $partial [ 'paymentMonths'] ;
      //->
      $partial [ 'paymentTotalPricePerMonth' ] = round ( ( $partial [ 'paymentProductPricePerMonth' ] + $partial [ 'paymentInteresPricePerMonth' ] ) , 2 ) ;
      $partial [ 'paymentTotalPrice' ] = round ( ( $partial [ 'paymentProductTotalPrice' ] + $partial [ 'paymentTotalInterestPrice' ] ) , 0 ) ;
      return $partial ;
    }
    return false ;
  }


}
