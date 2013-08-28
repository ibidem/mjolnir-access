<?php namespace mjolnir\access\tests;

use \mjolnir\access\Ban;

class BanTest extends \PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\access\Ban'));
	}

	// @todo tests for \mjolnir\access\Ban

} # test
