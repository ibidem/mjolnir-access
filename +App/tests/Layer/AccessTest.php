<?php namespace mjolnir\access\tests;

use \mjolnir\access\Layer_Access;

class Layer_AccessTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\access\Layer_Access'));
	}

	// @todo tests for \mjolnir\access\Layer_Access

} # test
