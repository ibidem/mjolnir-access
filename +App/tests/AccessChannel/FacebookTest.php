<?php namespace mjolnir\access\tests;

use \mjolnir\access\AccessChannel_Facebook;

class AccessChannel_FacebookTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\access\AccessChannel_Facebook'));
	}

	// @todo tests for \mjolnir\access\AccessChannel_Facebook

} # test
