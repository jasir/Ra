<?php

abstract class BaseTestCase extends \PHPUnit_Framework_TestCase
{
	/** @var \Mockista\Registry */
	protected $mockista;


	protected function setUp()
	{
		$this->mockista = new \Mockista\Registry();
	}


	protected function tearDown()
	{
		$this->mockista->assertExpectations();
	}


	protected function assertException($closure, $expectedExceptionClass = 'Exception', $expectedMessage = NULL)
	{
		try {
			call_user_func($closure);
		} catch (\Exception $e) {
			if (!$e instanceOf $expectedExceptionClass) {
				$this->fail("Expected exception $expectedExceptionClass, " . get_class($e) . ' catched instead');
			}
			if ($expectedMessage && $expectedMessage !== $e->getMessage()) {
				$this->fail("Exception was thrown, but message differs from expected - expected \"$expectedMessage\", got {$e->getMessage()}");
			}
			return;
		}
		$this->fail("Expected exception $expectedExceptionClass was not thrown");
	}
}
