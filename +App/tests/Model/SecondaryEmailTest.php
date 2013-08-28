<?php namespace mjolnir\access\tests;

use \mjolnir\access\Model_SecondaryEmail;

class Model_SecondaryEmailTest extends \PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\access\Model_SecondaryEmail'));
	}

	// @todo tests for \mjolnir\access\Model_SecondaryEmail

} # test
