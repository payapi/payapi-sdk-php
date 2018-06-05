<?php

use PHPUnit\Framework\TestCase;

#require(__DIR__ . DIRECTORY_SEPARATOR . 'test' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'sdk' . '.' . 'php');

class payapiSdkTest extends TestCase
{

  #private $callMock = null;
  #private $sdk      = null;

  #public function __construct()
  #{
  #  $this->sdk = new payapiSdk();
  #}

  public function testSomething()
  {
    #$this->calcMock=$this->getMock('payapiSdkTest');
    #$this->calcMock->expects($this->once())
    #        ->method('__construct')
    #        ->will($this->returnValue(true));
    $this->assertNull(null);
  }
}
