<?php namespace mjolnir\access\tests;

use \mjolnir\access\ReCaptcha;

class ReCaptchaTest extends \PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\access\ReCaptcha'));
	}

	// @todo tests for \mjolnir\access\ReCaptcha

} # test
