<?php

final class transaction {

  private
    $error                  =  false ,
    $transaction            =  false ,
    $referenceId            =  false ,
    $currency               =  false ,
    $order                  =  false ,
    $products               =  false ,
    $shippingAddres         =  false ,
    $consumer               =  false ,
    $callbacks              =  false ,
    $returns                =  false ;

  protected function __construct ( $payapi_public_id , $referenceId , $currency , $products , $shippingAddres = array () , $consumer = array () , $callbacks = array () , $returns = array () , $tosUrl = '' ) {
    if ( is_string ( $payapi_public_id ) === true) {
      if ( is_string ( $referenceId ) === true ) {
        $this -> referenceId = $referenceId ;
        if ( $this -> products ( $products ) === true ) {
          if ( $this -> shippingAddres ( $shippingAddres ) === true ) {
            if ( $this -> consumer ( $consumer ) === true ) {
              if ( $this -> callbacks ( $callbacks ) === true ) {
                if ( $this -> returns ( $returns ) === true ) {
                  if ( $this -> buildOrder ( $referenceId , $currency , $tosUrl ) === true ) {
                    return $this -> getTransactionPayload () ;
                  }
                }
              }
            }
          }
        }
      } else {
        $this -> error ( 'Order id not provided' ) ;
      }
    } else {
      $this -> error ( 'PayApi public id not provided' ) ;
    }
    return $this -> debug () ;
  }

  private function buildOrder ( $referenceId , $currency , $tosUrl ) {
    if ( count ( $this -> products > 0 ) ) {
      $sumInCentsExcVat = null ;
      $vatInCents = null ;
      $sumInCentsIncVat = null ;
      $this -> order = array (
        "sumInCentsIncVat"  => $sumInCentsIncVat ,
        "sumInCentsExcVat"  => $sumInCentsExcVat ,
        "vatInCents"        => $vatInCents ,
        "currency"          => $currency ,
        "referenceId"       => $referenceId ,
        "tosUrl"            => $tosUrl
      ) ;
      return $this -> order ;
    }
    return false ;
  }

  private function shippingAddress ( $shippingAddres ) {
    $this -> shippingAddres = array (
      "recipientName"       => $this -> setValue ( 'recipientName' , $shippingAddres ) ,
      "co"                  => $this -> setValue ( 'co' , $shippingAddres ) ,
      "streetAddress"       => $this -> setValue ( 'streetAddress' , $shippingAddres ) ,
      "streetAddress2"      => $this -> setValue ( 'streetAddress2' , $shippingAddres ) ,
      "postalCode"          => $this -> setValue ( 'postalCode' , $shippingAddres ) ,
      "city"                => $this -> setValue ( 'city' , $shippingAddres ) ,
      "stateOrProvince"     => $this -> setValue ( 'stateOrProvince' , $shippingAddres ) ,
      "countryCode"         => $this -> setValue ( 'countryCode' , $shippingAddres )
    ) ;
    return $this -> shippingAddres ;
  }

  private function consumer ( $consumer ) {
    $this -> consumer = array (
      "consumerId"          => $this -> setValue ( 'consumerId' , $consumer ) ,
      "email"               => $this -> setValue ( 'email' , $consumer ) ,
      "locale"              => $this -> setValue ( 'locale' , $consumer ) ,
      "mobilePhoneNumber"   => $this -> setValue ( 'mobilePhoneNumber' , $consumer )
    ) ;
    return $this -> order ;
  }

  private function products ( $products ) {
    foreach ( $products as $product ) {
      $this -> products [] = array (
        "id"                  => $this -> setValue ( 'id' , $product ) ,
        "quantity"            => $this -> setValue ( 'quantity' , $product ) ,
        "title"               => $this -> setValue ( 'title' , $product ) ,
        "description"         => $this -> setValue ( 'description' , $product ) ,
        "imageUrl"            => $this -> setValue ( 'imageUrl' , $product ) ,
        "category"            => $this -> setValue ( 'category' , $product ) , //-> readable
        "options"             => $this -> setValue ( 'options' , $product ) ,
        "model"               => $this -> setValue ( 'model' , $product ) ,
        "priceInCentsIncVat"  => $this -> setValue ( 'priceInCentsIncVat' , $product ) ,
        "priceInCentsExcVat"  => $this -> setValue ( 'priceInCentsExcVat' , $product ) ,
        "vatInCents"          => $this -> setValue ( 'vatInCents' , $product ) ,
        "vatPercentage"       => $this -> setValue ( 'vatPercentage' , $product ) ,
        "extraData"           => $this -> extraData ( $this -> setValue ( 'extraData' , $product ) ) //-> would be encoded/decoded
      ) ;
    }
    return $this -> products ;
  }
  //->
  private function extraData ( $extradata ) {
    return $this -> crypter -> encode ( $extradata , true ) ;
  }

  private function callbacks ( $callbacks ) {
    $this -> callbacks = array (
      "processing"          => $this -> setValue ( 'processing' , $callbacks ) ,
      "success"             => $this -> setValue ( 'success' , $callbacks ) ,
      "failed"              => $this -> setValue ( 'failed' , $callbacks ) ,
      "chargeback"          => $this -> setValue ( 'chargeback' , $callbacks )
    ) ;
    return $this -> callbacks ;
  }

  private function returns ( $returns ) {
    $this -> returns = array (
      "success"             => $this -> setValue ( 'success' , $returns ) ,
      "cancel"              => $this -> setValue ( 'cancel' , $returns ) ,
      "failed"              => $this -> setValue ( 'failed' , $returns )
    ) ;
    return $this -> returns ;
  }

  private function setValue ( $key , $value , $mandatory = false ) {
    if ( isset ( $key [ $value ] ) === true ) {
      return $value ;
    }
    if ( $mandatory === true ) {
      $this -> error ( 'mandatory field [' . $key . '] missed' ) ;
    }
    return '' ;
  }

  private function validate () {
    return $this -> buildOrder () ;
  }

  public function getTransactionByToken ( $token ) {
    return false ;
  }

  public function getTransactionToken () {
    return false ;
  }

  public function orderId () {
    if ( isset ( $this -> order [ 'referenceId' ] ) === true ) {
      return $this -> order [ 'referenceId' ] ;
    }
    $this -> error ( 'mandatory [' . $key . '] missed' ) ;
    return false ;
  }

  private function yieldPayloadData () {

  }

  private function payload () {
    $payloadData = array (
      "order"               => $this -> order ,
      "products"            => $this -> products ,
      "shippingAddress"     => $this -> shippingAddress ,
      "consumer"            => $this -> consumer ,
      "callbacks"           => $this -> callbacks ,
      "returnUrls"          => $this -> returnUrls
    ) ;
    $jsonPayload = json_encode ( $payloadData , true ) ;
    return $jsonPayload ;
  }

  public function getTransactionPayload () {
    return ( string ) $this ;
  }

  public function getTransactionData () {
    return json_decode ( ( string ) $this , true ) ;
  }

  protected function error ( $error ) {
    $this -> error = ( string ) $error ;
  }

  public function debug () {
    return array (
      "valid" => $this -> validTransaction () ,
      "error" => $this -> error
    ) ;
  }

  public function validTransaction () {
    if ( $this -> error === false ) {
      return true ;
    }
    return false ;
  }

  private function toString () {
    if ( $this -> validTransaction () === true ) {
      return $this -> payload () ;
    }
    $this -> error ( '___de_bug_ged___' ) ;
    return $this -> debug () ;
  }

  public function __toString () {
    return $this -> toString () ;
  }

  public function __call ( $command , $arguments ) {
    if ( $this -> error === false || in_array ( $command ,
      array (
        "__toString" , "validTransaction" , "getTransactionData" , "getTransactionPayload"
      ) ) ) {
      return $this -> $command ( $arguments ) ;
    }
    return false ;
  }


}
