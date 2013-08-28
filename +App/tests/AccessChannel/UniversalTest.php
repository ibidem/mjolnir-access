<?php namespace mjolnir\access\tests;

use \mjolnir\access\AccessChannel_Universal;

class AccessChannel_UniversalTest extends \PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\access\AccessChannel_Universal'));
	}

	// @todo tests for \mjolnir\access\AccessChannel_Universal

} # test
