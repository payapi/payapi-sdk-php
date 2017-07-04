<?php

namespace payapi ;

//-> OpenCart translator

final class plugin {

  protected
    $version               =  '0.0.0' ;

  private
    $order                     =      false ,
    $products                  =      false ,
    $shippingAddress           =      false ,
    $consumer                  =      false ;


  private function translateNativeProduct ( $product ) {
    //-> this shoudl be done in store side (tax, options, qty, ...) -> better to pass just shopping car order for creating a transaction
    try {
      $appProduct = array (
        "id"                      => @$product [ 'product_id' ] ,
        //-> @TODO note this is the stock qty. not consumer requested
        "quantity"                => @$product [ 'quantity' ] ,
        "title"                   => @$product [ 'name' ] ,
        "description"             => @$product [ 'description' ] ,
        "imageUrl"                => @$product [ 'thumb' ] ,
        "category"                => @$product [ 'tag' ] ,
        "model"                   => @$product [ 'model' ] ,
        "options"                 => $this -> productOptions ( @$product [ 'options' ] ) ,
        //-> @TODO this is not vatInCents, just for testing
        "vatInCents"              => $this -> vatInCents ( @$product [ 'tax_class_id' ] ) ,
        "priceInCentsExcVat"      => $this -> productSpecial ( @$product [ 'price' ] , @$product [ 'special' ] )
      ) ;
      $appProduct [ 'priceInCentsExcVat' ] = $appProduct [ 'priceInCentsExcVat' ] + $appProduct [ 'vatInCents' ] ;

    } catch (Exception $e) {
      $appProduct = array ( 'undefined' => 'error.input' ) ;
    }
    return $appProduct ;
  }

  private function productOptions ( $nativeOptions ) {
    //->
    return $nativeOptions ;
  }

  private function productSpecial ( $nativePrice , $nativeSpecial ) {
    $price = ( @round ( ( ( isset ( $nativeSpecial ) ) ? $nativeSpecial : $nativePrice ) , 2 ) * 100 ) ;
    return $price ;
  }

  private function vatInCents ( $taxes ) {
    return ( $taxes * 100 ) ;
  }

  private function translateNativeOrder ( $nativeOrder ) { // shopping cart order
    $this -> order = array (
      "sumInCentsIncVat" => null ,
      "sumInCentsExcVat" => null ,
      "vatInCents" => "" ,
      "currency" => "" ,
      "referenceId" => "" ,
      "tosUrl" => ""
    ) ;

/*
return array(
  'order_id'                => $order_query->row['order_id'],
  'invoice_no'              => $order_query->row['invoice_no'],
  'invoice_prefix'          => $order_query->row['invoice_prefix'],
  'store_id'                => $order_query->row['store_id'],
  'store_name'              => $order_query->row['store_name'],
  'store_url'               => $order_query->row['store_url'],
  'customer_id'             => $order_query->row['customer_id'],
  'firstname'               => $order_query->row['firstname'],
  'lastname'                => $order_query->row['lastname'],
  'email'                   => $order_query->row['email'],
  'telephone'               => $order_query->row['telephone'],
  'fax'                     => $order_query->row['fax'],
  'custom_field'            => json_decode($order_query->row['custom_field'], true),
  'payment_firstname'       => $order_query->row['payment_firstname'],
  'payment_lastname'        => $order_query->row['payment_lastname'],
  'payment_company'         => $order_query->row['payment_company'],
  'payment_address_1'       => $order_query->row['payment_address_1'],
  'payment_address_2'       => $order_query->row['payment_address_2'],
  'payment_postcode'        => $order_query->row['payment_postcode'],
  'payment_city'            => $order_query->row['payment_city'],
  'payment_zone_id'         => $order_query->row['payment_zone_id'],
  'payment_zone'            => $order_query->row['payment_zone'],
  'payment_zone_code'       => $payment_zone_code,
  'payment_country_id'      => $order_query->row['payment_country_id'],
  'payment_country'         => $order_query->row['payment_country'],
  'payment_iso_code_2'      => $payment_iso_code_2,
  'payment_iso_code_3'      => $payment_iso_code_3,
  'payment_address_format'  => $order_query->row['payment_address_format'],
  'payment_custom_field'    => json_decode($order_query->row['payment_custom_field'], true),
  'payment_method'          => $order_query->row['payment_method'],
  'payment_code'            => $order_query->row['payment_code'],
  'shipping_firstname'      => $order_query->row['shipping_firstname'],
  'shipping_lastname'       => $order_query->row['shipping_lastname'],
  'shipping_company'        => $order_query->row['shipping_company'],
  'shipping_address_1'      => $order_query->row['shipping_address_1'],
  'shipping_address_2'      => $order_query->row['shipping_address_2'],
  'shipping_postcode'       => $order_query->row['shipping_postcode'],
  'shipping_city'           => $order_query->row['shipping_city'],
  'shipping_zone_id'        => $order_query->row['shipping_zone_id'],
  'shipping_zone'           => $order_query->row['shipping_zone'],
  'shipping_zone_code'      => $shipping_zone_code,
  'shipping_country_id'     => $order_query->row['shipping_country_id'],
  'shipping_country'        => $order_query->row['shipping_country'],
  'shipping_iso_code_2'     => $shipping_iso_code_2,
  'shipping_iso_code_3'     => $shipping_iso_code_3,
  'shipping_address_format' => $order_query->row['shipping_address_format'],
  'shipping_custom_field'   => json_decode($order_query->row['shipping_custom_field'], true),
  'shipping_method'         => $order_query->row['shipping_method'],
  'shipping_code'           => $order_query->row['shipping_code'],
  'comment'                 => $order_query->row['comment'],
  'total'                   => $order_query->row['total'],
  'order_status_id'         => $order_query->row['order_status_id'],
  'order_status'            => $order_query->row['order_status'],
  'affiliate_id'            => $order_query->row['affiliate_id'],
  'commission'              => $order_query->row['commission'],
  'language_id'             => $order_query->row['language_id'],
  'language_code'           => $language_code,
  'currency_id'             => $order_query->row['currency_id'],
  'currency_code'           => $order_query->row['currency_code'],
  'currency_value'          => $order_query->row['currency_value'],
  'ip'                      => $order_query->row['ip'],
  'forwarded_ip'            => $order_query->row['forwarded_ip'],
  'user_agent'              => $order_query->row['user_agent'],
  'accept_language'         => $order_query->row['accept_language'],
  'date_added'              => $order_query->row['date_added'],
  'date_modified'           => $order_query->row['date_modified']
);

*/

    return $order ;
  }

  private function translateNativeConsumer ( $consumer ) {
    return $consumer ;
  }

  private function translateNativeAddress ( $address ) {
    return $order ;
  }

  public function product ( $product ) {
    $translatedProduct = $this -> translateNativeProduct ( $product ) ;
    return $translatedProduct ;
  }

  public function order ( $order ) {
    $translatedOrder = $this -> translateNativeOrder ( $order ) ;
    return $translatedOrder ;
  }

  public function consumer ( $consumer ) {
    $translatedConsumer = $this -> translateNativeConsumer ( $consumer ) ;
    return $translatedConsumer ;
  }

  public function address ( $address ) {
    $translatedAddress = $this -> translateNativeAddress ( $address ) ;
    return $translatedAddress ;
  }

  public function __toString () {
    return $this -> version ;
  }


}
