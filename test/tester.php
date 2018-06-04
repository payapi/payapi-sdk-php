<?php

require(__DIR__ . DIRECTORY_SEPARATOR . '.' . 'tests' . '.' . 'php');

class tester extends \PHPUnit_Framework_TestCase
{

	private $callMock = false;

	public function test()
	{
		$this->calcMock=$this->getMock('test');
		$this->calcMock->expects($this->once())
            ->method('info')
            ->will($this->returnValue(true));
	}


}